<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TrackingEventController extends Controller
{
    /**
     * Simpan aktivitas tracking dan validasi perpindahan status shipment.
     */
    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => [
                'required',
                'string',
                Rule::in([
                    'BOOKING',
                    'PICKUP',
                    'DEPARTURE',
                    'ARRIVAL',
                    'DELIVERY',
                    'INSPECTION',
                ]),
            ],
            'event_at' => ['required', 'date'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'event_type.required' => 'Jenis aktivitas wajib dipilih.',
            'event_type.in' => 'Jenis aktivitas tracking tidak valid.',
            'event_at.required' => 'Waktu aktivitas wajib diisi.',
            'event_at.date' => 'Format waktu aktivitas tidak valid.',
        ]);

        DB::transaction(function () use (
            $validated,
            $request,
            $shipment
        ): void {
            // Mengunci shipment agar dua pembaruan bersamaan tidak
            // menghasilkan perpindahan status yang tidak konsisten.
            $lockedShipment = Shipment::query()
                ->whereKey($shipment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedShipment->loadMissing('destinationLocation');

            $currentStatus = $lockedShipment->current_status;
            $eventType = $validated['event_type'];
            $eventAt = \Illuminate\Support\Carbon::parse($validated['event_at']);

            // Memastikan waktu aktivitas tidak mendahului riwayat terakhir.
            $lastEvent = $lockedShipment->trackingEvents()
                ->orderByDesc('event_at')
                ->orderByDesc('id')
                ->first();

            if ($lastEvent && $eventAt->lt($lastEvent->event_at)) {
                throw ValidationException::withMessages([
                    'event_at' => 'Waktu aktivitas tidak boleh lebih awal dari riwayat tracking sebelumnya.',
                ]);
            }

            // Status akhir tidak boleh diperbarui melalui tracking biasa.
            if (in_array($currentStatus, ['DELIVERED', 'CANCELLED'], true)) {
                throw ValidationException::withMessages([
                    'event_type' => 'Shipment sudah berstatus akhir dan tidak dapat diperbarui melalui tracking biasa.',
                ]);
            }

            $destination = $lockedShipment->destinationLocation;
            $trackingLocation = mb_strtolower(trim(
                $validated['location_name'] ?? ''
            ));

            $destinationNames = $destination
                ? array_filter([
                    mb_strtolower(trim($destination->name ?? '')),
                    mb_strtolower(trim($destination->city ?? '')),
                ])
                : [];

            $isAtDestination = $trackingLocation !== ''
                && in_array($trackingLocation, $destinationNames, true);

            // Tentukan status berdasarkan status sebelumnya dan aktivitas.
            $resolvedStatus = match ($eventType) {
                'BOOKING' => $currentStatus === 'CREATED'
                    ? 'BOOKED'
                    : null,

                'PICKUP' => $currentStatus === 'BOOKED'
                    ? 'PICKED_UP'
                    : null,

                'DEPARTURE' => $currentStatus === 'PICKED_UP'
                    ? 'IN_TRANSIT'
                    : null,

                'ARRIVAL' => $currentStatus === 'IN_TRANSIT'
                    ? ($isAtDestination ? 'ARRIVED' : 'IN_TRANSIT')
                    : null,

                'DELIVERY' => $currentStatus === 'ARRIVED'
                    ? 'DELIVERED'
                    : null,

                'INSPECTION' => $currentStatus,

                default => null,
            };

            if ($resolvedStatus === null) {
                throw ValidationException::withMessages([
                    'event_type' => "Aktivitas {$eventType} tidak dapat dilakukan ketika status shipment adalah {$currentStatus}.",
                ]);
            }

            // Simpan riwayat tracking.
            $lockedShipment->trackingEvents()->create([
                'created_by' => $request->user()->id,
                'event_type' => $eventType,
                'status' => $resolvedStatus,
                'event_at' => $eventAt,
                'location_name' => $validated['location_name'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            $lockedShipment->current_status = $resolvedStatus;
            $lockedShipment->updated_by = $request->user()->id;

            // Keberangkatan aktual dicatat ketika cargo benar-benar berangkat.
            if (
                $eventType === 'DEPARTURE'
                && ! $lockedShipment->actual_departure_at
            ) {
                $lockedShipment->actual_departure_at = $eventAt;
            }

            // Kedatangan aktual dicatat ketika cargo tiba di tujuan.
            if (
                $eventType === 'ARRIVAL'
                && $isAtDestination
                && ! $lockedShipment->actual_arrival_at
            ) {
                $lockedShipment->actual_arrival_at = $eventAt;
            }

            $lockedShipment->save();
        });

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', 'Pembaruan tracking berhasil disimpan.');
    }
}
