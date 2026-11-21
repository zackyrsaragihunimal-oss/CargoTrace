<?php

namespace App\Http\Controllers;

use App\Events\AdminDashboardStatsUpdated;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\AdminDashboardStatsService;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShipmentExceptionController extends Controller
{
    /**
     * Menampilkan daftar exception sesuai hak akses pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $exceptions = ShipmentException::with([
            'shipment.customer',
            'reporter',
            'assignee',
        ]);

        // Customer hanya melihat exception dari shipment miliknya.
        if ($user->isCustomer()) {
            $exceptions->whereHas('shipment', function ($query) use ($user) {
                $query->where('customer_id', $user->id);
            });
        }

        // Operator hanya melihat exception yang ditugaskan kepadanya.
        if ($user->isOperator()) {
            $exceptions->where('assigned_to', $user->id);
        }

        if ($request->filled('status')) {
            $exceptions->where('status', $request->input('status'));
        }

        if ($request->filled('severity')) {
            $exceptions->where('severity', $request->input('severity'));
        }

        $exceptions = $exceptions
            ->latest('detected_at')
            ->paginate(10)
            ->withQueryString();

        // Daftar operator aktif hanya diperlukan oleh administrator.
        $operators = $user->isAdmin()
            ? User::query()
                ->where('role', 'operator')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        return view('exceptions.index', [
            'exceptions' => $exceptions,
            'operators' => $operators,
            'status' => $request->input('status'),
            'severity' => $request->input('severity'),
        ]);
    }

    /**
     * Menetapkan exception kepada operator.
     * Hanya admin yang dapat melakukan penugasan.
     */
    public function assign(
        Request $request,
        ShipmentException $exception,
        AuditLogService $auditLogService,
        NotificationService $notificationService
    ): RedirectResponse {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'assigned_to' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('role', 'operator')
                    ->where('is_active', true),
            ],
        ], [
            'assigned_to.required' => 'Operator wajib dipilih.',
            'assigned_to.exists' => 'Operator tidak valid atau tidak aktif.',
        ]);

        DB::transaction(function () use (
            $request,
            $exception,
            $validated,
            $auditLogService,
            $notificationService
        ) {
            $lockedException = ShipmentException::query()
                ->lockForUpdate()
                ->findOrFail($exception->id);

            // Exception yang sudah selesai tidak dapat ditugaskan ulang.
            abort_unless(
                $lockedException->status !== 'RESOLVED',
                403,
                'Exception yang sudah selesai tidak dapat ditugaskan kembali.'
            );

            $oldAssignee = $lockedException->assigned_to;

            $lockedException->assigned_to = $validated['assigned_to'];
            $lockedException->save();

            // Beri tahu operator baru jika penugasannya berubah.
            if ((int) $oldAssignee !== (int) $lockedException->assigned_to) {
                $lockedException->loadMissing('shipment');

                $assignedOperator = User::findOrFail(
                    $lockedException->assigned_to
                );

                $notificationService->send(
                    $assignedOperator,
                    $lockedException->shipment,
                    'EXCEPTION_ASSIGNED',
                    'Exception pengiriman ditugaskan',
                    "Anda ditugaskan menangani exception {$lockedException->exception_code} pada pengiriman {$lockedException->shipment->shipment_number}.",
                    [
                        'exception_id' => $lockedException->id,
                        'exception_code' => $lockedException->exception_code,
                        'status' => $lockedException->status,
                    ]
                );
            }

            $auditLogService->record(
                $request,
                'EXCEPTION_ASSIGNED',
                'ShipmentException',
                $lockedException->id,
                ['assigned_to' => $oldAssignee],
                ['assigned_to' => $lockedException->assigned_to]
            );
        });

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Exception berhasil ditugaskan kepada operator.');
    }

    /**
     * Memperbarui status dan menyelesaikan exception.
     */
    public function update(
        Request $request,
        ShipmentException $exception,
        AuditLogService $auditLogService,
        NotificationService $notificationService,
        AdminDashboardStatsService $adminDashboardStatsService
    ): RedirectResponse {
        $user = $request->user();

        // Customer tidak boleh mengubah exception.
        abort_if($user->isCustomer(), 403);

        // Operator hanya boleh mengubah exception yang ditugaskan kepadanya.
        if (
            $user->isOperator() &&
            $exception->assigned_to !== $user->id
        ) {
            abort(403, 'Exception ini tidak ditugaskan kepada Anda.');
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['IN_PROGRESS', 'RESOLVED']),
            ],
            'resolution_notes' => [
                'required_if:status,RESOLVED',
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'status.required' => 'Status exception wajib dipilih.',
            'status.in' => 'Status exception tidak valid.',
            'resolution_notes.required_if' => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.max' => 'Catatan penyelesaian maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use (
            $request,
            $exception,
            $validated,
            $auditLogService,
            $notificationService
        ) {
            $lockedException = ShipmentException::query()
                ->lockForUpdate()
                ->findOrFail($exception->id);

            // Periksa kembali penugasan operator setelah data dikunci.
            if (
                $request->user()->isOperator() &&
                (int) $lockedException->assigned_to !== (int) $request->user()->id
            ) {
                abort(403, 'Exception ini tidak ditugaskan kepada Anda.');
            }

            // Exception yang sudah selesai tidak dapat diubah kembali.
            if ($lockedException->status === 'RESOLVED') {
                abort(403, 'Exception yang sudah selesai tidak dapat diubah.');
            }

            $oldValues = [
                'status' => $lockedException->status,
                'resolution_notes' => $lockedException->resolution_notes,
                'resolved_at' => $lockedException->resolved_at?->toDateTimeString(),
            ];

            $lockedException->status = $validated['status'];

            if ($validated['status'] === 'RESOLVED') {
                $lockedException->resolution_notes = $validated['resolution_notes'];
                $lockedException->resolved_at = now();
            } else {
                $lockedException->resolution_notes = null;
                $lockedException->resolved_at = null;
            }

            $lockedException->save();

            $auditLogService->record(
                $request,
                'EXCEPTION_STATUS_UPDATED',
                'ShipmentException',
                $lockedException->id,
                $oldValues,
                [
                    'status' => $lockedException->status,
                    'resolution_notes' => $lockedException->resolution_notes,
                    'resolved_at' => $lockedException->resolved_at?->toDateTimeString(),
                ]
            );

            // Beri tahu customer ketika exception berhasil diselesaikan.
            if ($validated['status'] === 'RESOLVED') {
                $lockedException->loadMissing('shipment.customer');

                $customer = $lockedException->shipment->customer;

                if ($customer) {
                    $notificationService->send(
                        $customer,
                        $lockedException->shipment,
                        'EXCEPTION_RESOLVED',
                        'Masalah pengiriman telah diselesaikan',
                        "Masalah pada pengiriman {$lockedException->shipment->shipment_number} telah diselesaikan.",
                        [
                            'exception_id' => $lockedException->id,
                            'exception_code' => $lockedException->exception_code,
                        ]
                    );
                }
            }
        });

        AdminDashboardStatsUpdated::dispatch($adminDashboardStatsService->getStats());

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Status exception berhasil diperbarui.');
    }
}
