<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentException;

class AdminDashboardStatsService
{
    /**
     * Mengambil statistik operasional terbaru untuk dashboard Admin.
     */
    public function getStats(): array
    {
        return [
            'totalShipments' => Shipment::count(),

            'activeShipments' => Shipment::query()
                ->whereNotIn('current_status', ['DELIVERED', 'CANCELLED'])
                ->count(),

            'inTransitShipments' => Shipment::where(
                'current_status',
                'IN_TRANSIT'
            )->count(),

            'deliveredShipments' => Shipment::where(
                'current_status',
                'DELIVERED'
            )->count(),

            'delayedShipments' => Shipment::query()
                ->whereNotIn('current_status', ['DELIVERED', 'CANCELLED'])
                ->whereRaw(
                    'COALESCE(estimated_arrival_at, expected_arrival_at) < ?',
                    [now()]
                )
                ->count(),

            'openExceptions' => ShipmentException::whereIn(
                'status',
                ['OPEN', 'IN_PROGRESS']
            )->count(),

            'updatedAt' => now()->toIso8601String(),
        ];
    }
}