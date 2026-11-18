<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrackingEventController extends Controller
{
    /**
     * Simpan pembaruan tracking dan sinkronkan status shipment.
     */
    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'string', Rule::in([
                'STATUS_UPDATE',
                'PICKUP',
                'DEPARTURE',
                'ARRIVAL',
                'DELIVERY',
                'INSPECTION',
            ])],
            'status' => ['required', 'string', Rule::in([
                'CREATED',
                'PENDING',
                'PICKED_UP',
                'IN_TRANSIT',
                'ARRIVED',
                'DELIVERED',
                'CANCELLED',
            ])],
            'event_at' => ['required', 'date'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'event_type.required' => 'Jenis aktivitas wajib dipilih.',
            'status.required' => 'Status pengiriman wajib dipilih.',
            'event_at.required' => 'Waktu aktivitas wajib diisi.',
        ]);

        $shipment->loadMissing('destinationLocation');

        $destination = $shipment->destinationLocation;
        $trackingLocation = mb_strtolower(trim($validated['location_name'] ?? ''));

        $destinationNames = $destination
            ? array_filter([
                mb_strtolower(trim($destination->name ?? '')),
                mb_strtolower(trim($destination->city ?? '')),
            ])
            : [];

        $isAtDestination = $trackingLocation !== ''
            && in_array($trackingLocation, $destinationNames, true);

        $resolvedStatus = match ($validated['event_type']) {
            'PICKUP' => 'PICKED_UP',
            'DEPARTURE' => 'IN_TRANSIT',
            'ARRIVAL' => $isAtDestination ? 'ARRIVED' : 'IN_TRANSIT',
            'DELIVERY' => 'IN_TRANSIT',
            'INSPECTION' => $shipment->current_status,
            default => $validated['status'],
        };

        DB::transaction(function () use (
            $validated,
            $request,
            $shipment,
            $resolvedStatus,
            $isAtDestination
        ): void {
            $shipment->trackingEvents()->create([
                'created_by' => $request->user()->id,
                'event_type' => $validated['event_type'],
                'status' => $resolvedStatus,
                'event_at' => $validated['event_at'],
                'location_name' => $validated['location_name'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            $shipment->current_status = $resolvedStatus;
            $shipment->updated_by = $request->user()->id;

            if (
                $resolvedStatus === 'PICKED_UP'
                && ! $shipment->actual_departure_at
            ) {
                $shipment->actual_departure_at = $validated['event_at'];
            }

            if (
                ($validated['event_type'] === 'ARRIVAL' && $isAtDestination)
                || $resolvedStatus === 'DELIVERED'
            ) {
                if (! $shipment->actual_arrival_at) {
                    $shipment->actual_arrival_at = $validated['event_at'];
                }
            }

            $shipment->save();
            $shipment->touch();
        });

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', 'Pembaruan tracking berhasil disimpan.');
    }
}