<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\ShipmentException;

class ShipmentDelayService
{
    /**
     * Perbarui ETA dan buat exception jika keberangkatan terlambat.
     */
    public function handleDepartureDelay(
        Shipment $shipment,
        TrackingEvent $trackingEvent
    ): void {
        if (
            ! $shipment->expected_departure_at
            || ! $trackingEvent->event_at
        ) {
            return;
        }

        $expectedDeparture = $shipment->expected_departure_at;
        $actualDeparture = $trackingEvent->event_at;

        // Tidak ada keterlambatan jika cargo berangkat tepat waktu atau lebih awal.
        if (! $actualDeparture->gt($expectedDeparture)) {
            return;
        }

        $delayMinutes = (int) $expectedDeparture->diffInMinutes(
            $actualDeparture
        );

        // Hitung ETA dari jadwal kedatangan awal agar tidak bertambah berulang.
        $baseEta = $shipment->expected_arrival_at
            ?? $shipment->estimated_arrival_at;

        if ($baseEta) {
            $shipment->estimated_arrival_at = $baseEta->copy()
                ->addMinutes($delayMinutes);
        }

        $threshold = config(
            'cargotrace.departure_delay_threshold_minutes',
            60
        );

        // Exception hanya dibuat jika keterlambatan mencapai ambang batas.
        if ($delayMinutes < $threshold) {
            return;
        }

        // Hindari membuat exception terbuka yang sama berulang kali.
        $existingException = ShipmentException::query()
            ->where('shipment_id', $shipment->id)
            ->where('exception_code', 'DEPARTURE_DELAY')
            ->where('status', 'OPEN')
            ->exists();

        if ($existingException) {
            return;
        }

        ShipmentException::create([
            'shipment_id' => $shipment->id,
            'tracking_event_id' => $trackingEvent->id,
            'reported_by' => $trackingEvent->created_by,
            'exception_code' => 'DEPARTURE_DELAY',
            'title' => 'Keterlambatan Keberangkatan',
            'description' => "Keberangkatan terlambat {$delayMinutes} menit dari jadwal.",
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
            'detected_at' => $actualDeparture,
        ]);
    }
}
