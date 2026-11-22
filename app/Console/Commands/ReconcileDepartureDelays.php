<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileDepartureDelays extends Command
{
    protected $signature = 'cargotrace:reconcile-departure-delays
                            {--dry-run : Jalankan audit tanpa mengubah database}';

    protected $description = 'Audit historis keterlambatan keberangkatan shipment tanpa mengubah data';

    public function handle(): int
    {
        // Pengaman: command hanya boleh dijalankan dalam mode dry-run.
        if (! $this->option('dry-run')) {
            $this->error('Operasi ditolak. Gunakan opsi --dry-run.');
            return self::FAILURE;
        }

        $timezone = config('app.timezone');
        $threshold = (int) config(
            'cargotrace.departure_delay_threshold_minutes',
            60
        );

        $shipments = DB::table('shipments')
            ->orderBy('id')
            ->get();

        $rows = [];
        $counts = [
            'READY' => 0,
            'NEEDS_REVIEW' => 0,
            'NO_DEPARTURE_EVENT' => 0,
            'ALREADY_RECORDED' => 0,
            'BELOW_THRESHOLD' => 0,
        ];

        foreach ($shipments as $shipment) {
            $events = DB::table('tracking_events')
                ->where('shipment_id', $shipment->id)
                ->where('event_type', 'DEPARTURE')
                ->orderBy('event_at')
                ->get();

            $exceptions = DB::table('shipment_exceptions')
                ->where('shipment_id', $shipment->id)
                ->where('exception_code', 'DEPARTURE_DELAY')
                ->get(['id', 'status']);

            $exceptionHistory = $exceptions->isNotEmpty();

            if ($events->isEmpty()) {
                $counts['NO_DEPARTURE_EVENT']++;

                $rows[] = [
                    $shipment->shipment_number,
                    '-',
                    '-',
                    '-',
                    '-',
                    'NO_DEPARTURE_EVENT',
                    'Tidak ada event DEPARTURE',
                    '-',
                ];

                continue;
            }

            if (! $shipment->expected_departure_at) {
                foreach ($events as $event) {
                    $counts['NEEDS_REVIEW']++;

                    $rows[] = [
                        $shipment->shipment_number,
                        $event->id,
                        '-',
                        $event->event_at,
                        '-',
                        'NEEDS_REVIEW',
                        'Jadwal keberangkatan kosong',
                        $exceptionHistory ? 'Sudah ada' : 'Belum ada',
                    ];
                }

                continue;
            }

            $expected = Carbon::parse(
                $shipment->expected_departure_at,
                $timezone
            );

            foreach ($events as $event) {
                $eventAt = Carbon::parse($event->event_at, $timezone);

                $isLate = $eventAt->greaterThan($expected);

                $delayMinutes = $isLate
                    ? (int) floor(
                        ($eventAt->getTimestamp() - $expected->getTimestamp()) / 60
                    )
                    : 0;

                $meetsThreshold = $isLate && $delayMinutes >= $threshold;

                $anomaly = '-';

                if (! $shipment->actual_departure_at) {
                    $anomaly = 'actual_departure_at kosong';
                } else {
                    $actual = Carbon::parse(
                        $shipment->actual_departure_at,
                        $timezone
                    );

                    $differenceSeconds = abs(
                        $actual->getTimestamp() - $eventAt->getTimestamp()
                    );

                    if ($differenceSeconds > 0) {
                        $differenceMinutes = round($differenceSeconds / 60, 1);
                        $anomaly = "Selisih {$differenceMinutes} menit";
                    }
                }

                if (! $meetsThreshold) {
                    $status = 'BELOW_THRESHOLD';
                } elseif ($exceptionHistory) {
                    $status = 'ALREADY_RECORDED';
                } elseif ($anomaly !== '-') {
                    $status = 'NEEDS_REVIEW';
                } else {
                    $status = 'READY';
                }

                $counts[$status]++;

                $rows[] = [
                    $shipment->shipment_number,
                    $event->id,
                    $expected->format('d-m-Y H:i'),
                    $eventAt->format('d-m-Y H:i'),
                    $delayMinutes . ' menit',
                    $status,
                    $anomaly,
                    $exceptionHistory ? 'Sudah ada' : 'Belum ada',
                ];
            }
        }

        $this->newLine();
        $this->info('CARGOTRACE - AUDIT REKONSILIASI KETERLAMBATAN');
        $this->line('MODE: READ ONLY / DRY RUN');
        $this->line('Zona waktu: ' . $timezone);
        $this->line('Ambang keterlambatan: ' . $threshold . ' menit');
        $this->line('Total shipment: ' . $shipments->count());

        $this->newLine();

        $this->table(
            [
                'Shipment',
                'Event ID',
                'Jadwal',
                'Waktu Event',
                'Terlambat',
                'Kategori',
                'Anomali',
                'Riwayat Exception',
            ],
            $rows
        );

        $this->newLine();
        $this->info('RINGKASAN HASIL AUDIT');

        $summaryRows = [];

        foreach ($counts as $status => $count) {
            $summaryRows[] = [$status, $count];
        }

        $this->table(['Kategori', 'Jumlah'], $summaryRows);

        $this->newLine();
        $this->comment(
            'Dry run selesai. Tidak ada shipment, ETA, exception, atau notifikasi yang diubah.'
        );

        return self::SUCCESS;
    }
}