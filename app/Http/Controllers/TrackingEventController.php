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

        DB::transaction(function () use ($validated, $request, $shipment): void {
            $shipment->trackingEvents()->create([
                'created_by' => $request->user()->id,
                'event_type' => $validated['event_type'],
                'status' => $validated['status'],
                'event_at' => $validated['event_at'],
                'location_name' => $validated['location_name'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            $shipment->current_status = $validated['status'];
            $shipment->updated_by = $request->user()->id;

            if ($validated['status'] === 'PICKED_UP' && ! $shipment->actual_departure_at) {
                $shipment->actual_departure_at = $validated['event_at'];
            }

            if ($validated['status'] === 'DELIVERED') {
                $shipment->actual_arrival_at = $validated['event_at'];
            }

            $shipment->save();
        });

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', 'Pembaruan tracking berhasil disimpan.');
    }
}
