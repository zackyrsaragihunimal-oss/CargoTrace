<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateCargoTracking extends Command
{
    protected $signature = 'cargo:simulate-tracking {shipment_number}';

    protected $description = 'Simulate CargoTrace shipment tracking from terminal';

    public function handle(): int
    {
        $shipment = Shipment::with('destinationLocation')
            ->where('shipment_number', $this->argument('shipment_number'))
            ->first();

        if (! $shipment) {
            $this->error('Shipment tidak ditemukan.');
            return self::FAILURE;
        }

        $user = User::find($shipment->updated_by ?? $shipment->created_by);

        if (! $user || ! in_array($user->role, ['admin', 'operator'], true) || ! $user->is_active) {
            $this->error('Operator atau admin yang tercatat tidak valid atau tidak aktif.');
            return self::FAILURE;
        }

        $destination = $shipment->destinationLocation;

        if (! $destination || ! $destination->city) {
            $this->error('Lokasi tujuan atau kota tujuan belum tersedia.');
            return self::FAILURE;
        }

        $events = [
            [
                'event_type' => 'ARRIVAL',
                'location_name' => 'Medan',
                'description' => 'Cargo tiba di lokasi transit Medan untuk proses pengiriman lanjutan.',
                'event_at' => '2026-11-18 20:00:00',
                'status' => 'IN_TRANSIT',
            ],
            [
                'event_type' => 'ARRIVAL',
                'location_name' => $destination->city,
                'description' => 'Cargo telah tiba di lokasi tujuan akhir.',
                'event_at' => '2026-11-19 10:00:00',
                'status' => 'ARRIVED',
            ],
        ];

        $results = [];

        DB::transaction(function () use ($shipment, $user, $events, &$results) {
            foreach ($events as $event) {
                $exists = $shipment->trackingEvents()
                    ->where('event_type', $event['event_type'])
                    ->where('location_name', $event['location_name'])
                    ->exists();

                if ($exists) {
                    $results[] = [
                        $event['event_type'],
                        $event['location_name'],
                        'DILEWATI - SUDAH ADA',
                        $shipment->current_status,
                    ];
                    continue;
                }

                $eventAt = Carbon::parse(
                    $event['event_at'],
                    config('app.timezone')
                );

                $shipment->trackingEvents()->create([
                    'created_by' => $user->id,
                    'event_type' => $event['event_type'],
                    'status' => $event['status'],
                    'event_at' => $eventAt,
                    'location_name' => $event['location_name'],
                    'description' => $event['description'],
                ]);

                $shipment->current_status = $event['status'];
                $shipment->updated_by = $user->id;

                if (
                    $event['location_name'] === $shipment->destinationLocation->city
                    && ! $shipment->actual_arrival_at
                ) {
                    $shipment->actual_arrival_at = $eventAt;
                }

                $shipment->save();
                $shipment->touch();

                $results[] = [
                    $event['event_type'],
                    $event['location_name'],
                    'BERHASIL DITAMBAHKAN',
                    $event['status'],
                ];
            }
        });

        $shipment->refresh();

        $this->newLine();
        $this->info('SIMULASI TRACKING CARGOTRACE');
        $this->line('Shipment: ' . $shipment->shipment_number);

        $this->table(
            ['Aktivitas', 'Lokasi', 'Hasil', 'Status'],
            $results
        );

        $this->newLine();
        $this->info('STATUS AKHIR: ' . $shipment->current_status);
        $this->line(
            'Keberangkatan aktual: ' .
            ($shipment->actual_departure_at?->format('d M Y H:i') ?? '-')
        );
        $this->line(
            'Kedatangan aktual: ' .
            ($shipment->actual_arrival_at?->format('d M Y H:i') ?? '-')
        );

        return self::SUCCESS;
    }
}