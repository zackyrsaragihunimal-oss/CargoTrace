@extends('layouts.app')

@section('header', 'Shipment Detail')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        Shipments
    </a>
@endsection

@section('content')

<style>
    .detail-page {
        color: #172b4d;
    }

    .detail-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .detail-heading h2 {
        margin: 0;
        font-size: 23px;
        font-weight: 700;
    }

    .detail-heading p {
        margin: 6px 0 0;
        color: #718096;
        font-size: 13px;
    }

    .back-link {
        display: inline-block;
        padding: 10px 15px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        color: #52647a;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        background: #fff;
    }

    .detail-panel {
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        padding: 21px;
        margin-bottom: 18px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, .04);
    }

    .detail-panel h3 {
        margin: 0 0 18px;
        font-size: 16px;
        color: #243b53;
    }

    .shipment-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        padding: 20px;
        border-radius: 10px;
        background: #f1f6ff;
        margin-bottom: 20px;
    }

    .shipment-banner small {
        display: block;
        color: #718096;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .shipment-banner strong {
        color: #1769e0;
        font-size: 20px;
    }

    .detail-status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        background: #e2edff;
        color: #245fc2;
        font-size: 11px;
        font-weight: 700;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .detail-item small {
        display: block;
        color: #8492a6;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .detail-item strong {
        display: block;
        color: #334155;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .route-box {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e7edf5;
        border-radius: 10px;
    }

    .route-location small {
        display: block;
        color: #8492a6;
        font-size: 11px;
        margin-bottom: 6px;
    }

    .route-location strong {
        color: #243b53;
        font-size: 14px;
    }

    .route-arrow {
        color: #1769e0;
        font-size: 22px;
        font-weight: 700;
    }

    .detail-table-wrapper {
        overflow-x: auto;
    }

    .detail-table {
        width: 100%;
        min-width: 550px;
        border-collapse: collapse;
    }

    .detail-table th {
        padding: 12px;
        text-align: left;
        background: #f5f8fc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        border-bottom: 1px solid #e7edf5;
    }

    .detail-table td {
        padding: 13px 12px;
        color: #334155;
        font-size: 12px;
        border-bottom: 1px solid #edf1f6;
    }

    .detail-muted {
        color: #8492a6;
        font-size: 11px;
    }

    .timeline {
        border-left: 2px solid #dce8fa;
        margin-left: 7px;
        padding-left: 22px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 22px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -29px;
        top: 3px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #1769e0;
        border: 3px solid #e7f0ff;
    }

    .timeline-item strong {
        display: block;
        color: #243b53;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .timeline-item p {
        margin: 5px 0;
        color: #64748b;
        font-size: 12px;
    }

    .timeline-item small {
        color: #8492a6;
        font-size: 11px;
    }

    .empty-message {
        padding: 25px 10px;
        text-align: center;
        color: #8492a6;
        font-size: 13px;
    }

    .exception-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 15px;
        background: #fff4e5;
        color: #a15c00;
        font-size: 11px;
        font-weight: 700;
    }

    @media (max-width: 650px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .route-box {
            gap: 8px;
        }

        .detail-panel {
            padding: 15px;
        }
    }
</style>

<div class="detail-page">

    <div class="detail-heading">
        <div>
            <h2>Detail Pengiriman</h2>
            <p>Informasi lengkap dan riwayat pengiriman CargoTrace.</p>
        </div>

        <a href="{{ route('shipments.index') }}" class="back-link">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <div class="detail-panel">

        <div class="shipment-banner">
            <div>
                <small>Nomor Shipment</small>
                <strong>{{ $shipment->shipment_number }}</strong>
            </div>

            <div>
                <span class="detail-status">
                    {{ str_replace('_', ' ', $shipment->current_status) }}
                </span>
            </div>
        </div>

        <h3>Rute Pengiriman</h3>

        <div class="route-box">
            <div class="route-location">
                <small>LOKASI ASAL</small>
                <strong>{{ $shipment->originLocation->city ?? '-' }}</strong>
                <div class="detail-muted">
                    {{ $shipment->originLocation->country ?? '-' }}
                </div>
            </div>

            <div class="route-arrow">&rarr;</div>

            <div class="route-location">
                <small>LOKASI TUJUAN</small>
                <strong>{{ $shipment->destinationLocation->city ?? '-' }}</strong>
                <div class="detail-muted">
                    {{ $shipment->destinationLocation->country ?? '-' }}
                </div>
            </div>
        </div>

    </div>

    <div class="detail-panel">
        <h3>Informasi Shipment</h3>

        <div class="detail-grid">

            <div class="detail-item">
                <small>Customer</small>
                <strong>{{ $shipment->customer->name ?? '-' }}</strong>
            </div>

            <div class="detail-item">
                <small>Carrier</small>
                <strong>{{ $shipment->carrier->name ?? '-' }}</strong>
            </div>

            <div class="detail-item">
                <small>Jenis Cargo</small>
                <strong>{{ $shipment->cargo_type ?? '-' }}</strong>
            </div>

            <div class="detail-item">
                <small>Berat Total</small>
                <strong>{{ $shipment->total_weight_kg ?? '0' }} kg</strong>
            </div>

            <div class="detail-item">
                <small>Volume Total</small>
                <strong>{{ $shipment->total_volume_m3 ?? '0' }} m³</strong>
            </div>

            <div class="detail-item">
                <small>Dibuat Oleh</small>
                <strong>{{ $shipment->creator->name ?? '-' }}</strong>
            </div>

        </div>
    </div>

    <div class="detail-panel">
        <h3>Jadwal Pengiriman</h3>

        <div class="detail-grid">

            <div class="detail-item">
                <small>Jadwal Keberangkatan</small>
                <strong>
                    {{ optional($shipment->expected_departure_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Jadwal Kedatangan</small>
                <strong>
                    {{ optional($shipment->expected_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Estimasi Kedatangan Terbaru</small>
                <strong>
                    {{ optional($shipment->estimated_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Keberangkatan Aktual</small>
                <strong>
                    {{ optional($shipment->actual_departure_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Kedatangan Aktual</small>
                <strong>
                    {{ optional($shipment->actual_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Terakhir Diperbarui</small>
                <strong>
                    {{ optional($shipment->updated_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

        </div>
    </div>

    <div class="detail-panel">
        <h3>Daftar Barang Cargo</h3>

        <div class="detail-table-wrapper">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Berat / Unit</th>
                        <th>Volume / Unit</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($shipment->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_weight_kg ?? '0' }} kg</td>
                            <td>{{ $item->unit_volume_m3 ?? '0' }} m³</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-message">
                                    Belum ada rincian barang cargo.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="detail-panel">
        <h3>Riwayat Tracking</h3>

        @if ($shipment->trackingEvents->isNotEmpty())
            <div class="timeline">
                @foreach ($shipment->trackingEvents->sortByDesc('event_at') as $event)
                    <div class="timeline-item">
                        <strong>
                            {{ $event->event_type ?? 'Aktivitas Pengiriman' }}
                        </strong>

                        <p>
                            Status:
                            {{ str_replace('_', ' ', $event->status ?? '-') }}
                        </p>

                        <p>
                            Lokasi: {{ $event->location_name ?? '-' }}
                        </p>

                        @if ($event->description)
                            <p>{{ $event->description }}</p>
                        @endif

                        <small>
                            {{ optional($event->event_at)->format('d M Y H:i') ?? '-' }}
                        </small>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-message">
                Belum ada riwayat tracking untuk pengiriman ini.
            </div>
        @endif
    </div>

    <div class="detail-panel">
        <h3>Informasi Kendala Pengiriman</h3>

        <div class="detail-table-wrapper">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Kode Kendala</th>
                        <th>Judul</th>
                        <th>Tingkat</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($shipment->exceptions->sortByDesc('detected_at') as $exception)
                        <tr>
                            <td>{{ $exception->exception_code }}</td>
                            <td>
                                <strong>{{ $exception->title }}</strong>
                                <div class="detail-muted">
                                    {{ $exception->description ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $exception->severity }}</td>
                            <td>
                                <span class="exception-status">
                                    {{ $exception->status }}
                                </span>
                            </td>
                            <td>
                                {{ optional($exception->detected_at)->format('d M Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-message">
                                    Tidak ada kendala pengiriman yang tercatat.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection