<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => $this->adminDashboard($user),
            'operator' => $this->operatorDashboard($user),
            'customer' => $this->customerDashboard($user),
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };
    }

    /**
     * Data ringkasan untuk administrator.
     */
    private function adminDashboard(User $user): View
    {
        $shipmentStatusCounts = Shipment::query()
            ->selectRaw('current_status, COUNT(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status');

        // Tren pengiriman selama tujuh hari terakhir.
        $trendStart = now()->startOfDay()->subDays(6);
        $trendEnd = now()->endOfDay();

        $dailyShipmentCounts = Shipment::query()
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(created_at) as shipment_date, COUNT(*) as total')
            ->groupBy('shipment_date')
            ->pluck('total', 'shipment_date');

        // Tanggal tanpa pengiriman ditampilkan dengan nilai nol.
        $shipmentTrend = [];

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo)->toDateString();

            $shipmentTrend[] = [
                'date' => $date,
                'label' => now()->subDays($daysAgo)->format('d M'),
                'total' => (int) ($dailyShipmentCounts[$date] ?? 0),
            ];
        }

        $activeShipments = Shipment::query()
            ->whereNotIn('current_status', ['DELIVERED', 'CANCELLED'])
            ->count();

        $inTransitShipments = Shipment::where(
            'current_status',
            'IN_TRANSIT'
        )->count();

        $deliveredShipments = Shipment::where(
            'current_status',
            'DELIVERED'
        )->count();

        // Gunakan ETA terbaru, atau jadwal kedatangan awal jika ETA kosong.
        $delayedShipments = Shipment::query()
            ->whereNotIn('current_status', ['DELIVERED', 'CANCELLED'])
            ->whereRaw(
                'COALESCE(estimated_arrival_at, expected_arrival_at) < ?',
                [now()]
            )
            ->count();

        return view('dashboards.admin', [
            'user' => $user,
            'totalShipments' => Shipment::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalOperators' => User::where('role', 'operator')->count(),
            'openExceptions' => ShipmentException::whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(),

            'shipmentStatusCounts' => $shipmentStatusCounts,
            'activeShipments' => $activeShipments,
            'inTransitShipments' => $inTransitShipments,
            'deliveredShipments' => $deliveredShipments,
            'delayedShipments' => $delayedShipments,
            'shipmentTrend' => $shipmentTrend,

            'recentShipments' => Shipment::with([
                'customer',
                'carrier',
                'originLocation',
                'destinationLocation',
            ])->latest()->limit(6)->get(),

            'recentActivities' => AuditLog::with('user')
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ]);
    }

    /**
     * Data operasional untuk operator berdasarkan penugasannya.
     */
    private function operatorDashboard(User $user): View
    {
        $assignedExceptions = $user->assignedExceptions();

        $exceptionStatusCounts = (clone $assignedExceptions)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboards.operator', [
            'user' => $user,
            'assignedExceptionCount' => (clone $assignedExceptions)->count(),
            'openExceptionCount' => (clone $assignedExceptions)
                ->whereIn('status', ['OPEN', 'IN_PROGRESS'])
                ->count(),
            'resolvedExceptionCount' => (clone $assignedExceptions)
                ->where('status', 'RESOLVED')
                ->count(),
            'exceptionStatusCounts' => $exceptionStatusCounts,
            'recentAssignedExceptions' => (clone $assignedExceptions)
                ->with('shipment.customer')
                ->latest('detected_at')
                ->limit(6)
                ->get(),
            'todayTrackingCount' => TrackingEvent::where('created_by', $user->id)
                ->whereDate('event_at', today())
                ->count(),
            'recentTrackingEvents' => TrackingEvent::with('shipment')
                ->where('created_by', $user->id)
                ->latest('event_at')
                ->limit(6)
                ->get(),
        ]);
    }

    /**
     * Data pengiriman yang hanya dimiliki customer yang sedang login.
     */
    private function customerDashboard(User $user): View
    {
        $customerShipments = $user->shipments();

        $shipmentStatusCounts = (clone $customerShipments)
            ->selectRaw('current_status, COUNT(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status');

        $customerExceptions = ShipmentException::whereHas(
            'shipment',
            fn ($query) => $query->where('customer_id', $user->id)
        );

        return view('dashboards.customer', [
            'user' => $user,
            'totalShipments' => (clone $customerShipments)->count(),
            'activeShipments' => (clone $customerShipments)
                ->whereNotIn('current_status', ['DELIVERED', 'CANCELLED'])
                ->count(),
            'deliveredShipments' => (clone $customerShipments)
                ->where('current_status', 'DELIVERED')
                ->count(),
            'shipmentStatusCounts' => $shipmentStatusCounts,
            'openExceptions' => (clone $customerExceptions)
                ->whereIn('status', ['OPEN', 'IN_PROGRESS'])
                ->count(),
            'unreadNotifications' => $user->notifications()
                ->whereNull('read_at')
                ->count(),
            'recentShipments' => (clone $customerShipments)
                ->with(['carrier', 'originLocation', 'destinationLocation'])
                ->latest()
                ->limit(6)
                ->get(),
            'recentNotifications' => $user->notifications()
                ->with('shipment')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}