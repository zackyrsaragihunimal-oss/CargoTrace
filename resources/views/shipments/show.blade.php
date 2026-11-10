
@extends('layouts.app')

@section('title', 'Shipment Detail')
@section('header', 'Shipment Overview')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        Shipments
    </a>
@endsection

@section('content')

<style>
    .ct-detail {
        color: #172b4d;
        max-width: 1450px;
        margin: auto;
    }

    .ct-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .ct-heading h1 {
        font-size: 27px;
        font-weight: 800;
        color: #102b55;
        margin: 0 0 6px;
    }

    .ct-heading p {
        color: #71839e;
        font-size: 13px;
        margin: 0;
    }

    .ct-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 17px;
        background: white;
        color: #2458a6;
        border: 1px solid #dce7f6;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: .2s;
    }

    .ct-back:hover {
        background: #edf5ff;
        transform: translateY(-2px);
    }

    .ct-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1.4fr .8fr;
        align-items: center;
        gap: 20px;
        padding: 30px;
        margin-bottom: 22px;
        border-radius: 20px;
        background: linear-gradient(120deg, #102b55, #1556a8 70%, #2589ed);
        color: white;
        box-shadow: 0 12px 30px rgba(16, 55, 110, .15);
    }

    .ct-hero::after {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        right: 12%;
        top: -180px;
    }

    .ct-eyebrow {
        display: inline-block;
        padding: 7px 12px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 20px;
        color: #dcecff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 13px;
    }

    .ct-hero h2 {
        color: white;
        font-size: clamp(22px, 3vw, 31px);
        font-weight: 800;
        overflow-wrap: anywhere;
        margin: 0 0 13px;
    }

    .ct-hero p {
        color: #d6e8ff;
        font-size: 13px;
        line-height: 1.7;
        margin: 0;
    }

    .ct-hero-status {
        display: inline-block;
        margin-top: 19px;
        padding: 8px 14px;
        border-radius: 30px;
        background: #e0f2fe;
        color: #075985;
        font-size: 11px;
        font-weight: 800;
    }

    .ct-hero-mascot {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .ct-hero-mascot img {
        width: 100%;
        max-width: 245px;
        max-height: 270px;
        object-fit: contain;
        filter: drop-shadow(0 12px 15px rgba(0,0,0,.15));
    }

    .ct-panel {
        background: white;
        border: 1px solid #e5edf7;
        border-radius: 17px;
        padding: 23px;
        margin-bottom: 20px;
        box-shadow: 0 5px 18px rgba(30, 64, 110, .035);
    }

    .ct-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .ct-panel-heading h3 {
        margin: 0;
        color: #17345f;
        font-size: 16px;
        font-weight: 800;
    }

    .ct-panel-heading span {
        color: #8a9bb2;
        font-size: 11px;
    }

    .ct-route {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 18px;
        padding: 23px;
        border: 1px solid #e7eef8;
        border-radius: 14px;
        background: #f8fbff;
    }

    .ct-route-label {
        color: #8a9bb2;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .ct-route-city {
        color: #17345f;
        font-size: 19px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .ct-route-country {
        color: #8495ac;
        font-size: 12px;
        margin-top: 5px;
    }

    .ct-route-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: white;
        font-size: 22px;
        background: linear-gradient(135deg, #1671df, #48a4ff);
        box-shadow: 0 5px 14px rgba(22,113,223,.2);
    }

    .ct-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .ct-info {
        padding: 17px;
        background: #f8fbff;
        border: 1px solid #edf2f9;
        border-radius: 12px;
        min-width: 0;
    }

    .ct-info small {
        display: block;
        color: #8798af;
        font-size: 11px;
        margin-bottom: 8px;
    }

    .ct-info strong {
        display: block;
        color: #24436e;
        font-size: 13px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .ct-schedule {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ct-schedule-item {
        padding: 19px;
        border-radius: 13px;
        border: 1px solid #e6eef9;
        background: linear-gradient(145deg, #f8fbff, #fff);
    }

    .ct-schedule-item small {
        display: block;
        color: #8294ad;
        font-size: 11px;
        margin-bottom: 8px;
    }

    .ct-schedule-item strong {
        color: #1d4f91;
        font-size: 15px;
        overflow-wrap: anywhere;
    }

    .ct-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .ct-table {
        width: 100%;
        min-width: 570px;
        border-collapse: collapse;
    }

    .ct-table th {
        padding: 14px;
        text-align: left;
        background: #f3f7fd;
        color: #71839d;
        font-size: 10px;
        letter-spacing: .6px;
        text-transform: uppercase;
        border-bottom: 1px solid #e5edf7;
    }

    .ct-table td {
        padding: 15px 14px;
        color: #405776;
        font-size: 12px;
        border-bottom: 1px solid #edf2f8;
    }

    .ct-table tbody tr:hover {
        background: #f9fbff;
    }

    .ct-muted {
        color: #93a2b6;
        font-size: 11px;
    }

    .ct-empty {
        text-align: center;
        padding: 27px 12px !important;
        color: #91a1b6 !important;
    }

    .ct-timeline {
        border-left: 2px solid #dceafa;
        margin-left: 8px;
        padding-left: 25px;
    }

    .ct-timeline-item {
        position: relative;
        padding-bottom: 25px;
    }

    .ct-timeline-item:last-child {
        padding-bottom: 0;
    }

    .ct-timeline-item::before {
        content: "";
        position: absolute;
        left: -32px;
        top: 2px;
        width: 12px;
        height: 12px;
        border: 3px solid #e4f0ff;
        border-radius: 50%;
        background: #1976e8;
        box-shadow: 0 0 0 2px white;
    }

    .ct-timeline-item strong {
        display: block;
        color: #24436e;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .ct-timeline-item p {
        color: #71839b;
        font-size: 12px;
        margin: 5px 0;
        line-height: 1.6;
    }

    .ct-timeline-item small {
        display: inline-block;
        color: #91a1b6;
        font-size: 10px;
        margin-top: 5px;
    }

    .ct-exception {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        background: #fff3df;
        color: #a65d00;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-footer-note {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 17px 20px;
        background: #edf6ff;
        border: 1px solid #dcecff;
        border-radius: 13px;
        color: #41658f;
        font-size: 12px;
        line-height: 1.6;
    }

    .ct-footer-note strong {
        color: #174f91;
    }

    @media (max-width: 900px) {
        .ct-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .ct-hero {
            grid-template-columns: 1fr;
            padding: 23px;
        }

        .ct-hero-mascot img {
            max-width: 180px;
            max-height: 190px;
        }

        .ct-route {
            gap: 9px;
            padding: 15px;
        }

        .ct-route-city {
            font-size: 15px;
        }

        .ct-route-icon {
            width: 37px;
            height: 37px;
            font-size: 17px;
        }

        .ct-grid,
        .ct-schedule {
            grid-template-columns: 1fr;
        }

        .ct-panel {
            padding: 17px;
        }

        .ct-heading h1 {
            font-size: 23px;
        }
    }
</style>

<div class="ct-detail">

    <div class="ct-heading">
        <div>
            <h1>Detail Pengiriman</h1>
            <p>Pantau informasi, jadwal, dan perjalanan cargo kamu.</p>
        </div>

        <a href="{{ route('shipments.index') }}" class="ct-back">
            &larr; Kembali ke Daftar
        </a>
    </div>

    {{-- HERO SHIPMENT --}}
    <section class="ct-hero">
        <div>
            <span class="ct-eyebrow">CARGOTRACE DELIVERY</span>

            <h2>{{ $shipment->shipment_number }}</h2>

            <p>
                Semua informasi pengiriman tersedia dalam satu tempat.
                Pantau detail perjalanan cargo kamu bersama CargoTrace.
            </p>

            <span class="ct-hero-status">
                {{ str_replace('_', ' ', $shipment->current_status) }}
            </span>
        </div>

        <div class="ct-hero-mascot">
            <img
                src="{{ asset('images/jek-package.png') }}"
                alt="JEK membawa paket CargoTrace"
            >
        </div>
    </section>

    {{-- ROUTE --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Rute Pengiriman</h3>
            <span>Origin & Destination</span>
        </div>

        <div class="ct-route">
            <div>
                <div class="ct-route-label">LOKASI ASAL</div>
                <div class="ct-route-city">
                    {{ $shipment->originLocation->city ?? '-' }}
                </div>
                <div class="ct-route-country">
                    {{ $shipment->originLocation->country ?? '-' }}
                </div>
            </div>

            <div class="ct-route-icon">&rarr;</div>

            <div>
                <div class="ct-route-label">LOKASI TUJUAN</div>
                <div class="ct-route-city">
                    {{ $shipment->destinationLocation->city ?? '-' }}
                </div>
                <div class="ct-route-country">
                    {{ $shipment->destinationLocation->country ?? '-' }}
                </div>
            </div>
        </div>
    </section>

    {{-- SHIPMENT INFORMATION --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Informasi Shipment</h3>
            <span>Shipment Information</span>
        </div>

        <div class="ct-grid">
            <div class="ct-info">
                <small>Customer</small>
                <strong>{{ $shipment->customer->name ?? '-' }}</strong>
            </div>

            <div class="ct-info">
                <small>Carrier</small>
                <strong>{{ $shipment->carrier->name ?? '-' }}</strong>
            </div>

            <div class="ct-info">
                <small>Jenis Cargo</small>
                <strong>{{ $shipment->cargo_type ?? '-' }}</strong>
            </div>

            <div class="ct-info">
                <small>Berat Total</small>
                <strong>{{ $shipment->total_weight_kg ?? '0' }} kg</strong>
            </div>

            <div class="ct-info">
                <small>Volume Total</small>
                <strong>{{ $shipment->total_volume_m3 ?? '0' }} m³</strong>
            </div>

            <div class="ct-info">
                <small>Dibuat Oleh</small>
                <strong>{{ $shipment->creator->name ?? '-' }}</strong>
            </div>
        </div>
    </section>

    {{-- SCHEDULE --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Jadwal Pengiriman</h3>
            <span>Shipment Schedule</span>
        </div>

        <div class="ct-schedule">
            <div class="ct-schedule-item">
                <small>Jadwal Keberangkatan</small>
                <strong>
                    {{ optional($shipment->expected_departure_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="ct-schedule-item">
                <small>Jadwal Kedatangan</small>
                <strong>
                    {{ optional($shipment->expected_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="ct-schedule-item">
                <small>Estimasi Kedatangan Terbaru</small>
                <strong>
                    {{ optional($shipment->estimated_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="ct-schedule-item">
                <small>Keberangkatan Aktual</small>
                <strong>
                    {{ optional($shipment->actual_departure_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="ct-schedule-item">
                <small>Kedatangan Aktual</small>
                <strong>
                    {{ optional($shipment->actual_arrival_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>

            <div class="ct-schedule-item">
                <small>Terakhir Diperbarui</small>
                <strong>
                    {{ optional($shipment->updated_at)->format('d M Y H:i') ?? '-' }}
                </strong>
            </div>
        </div>
    </section>

    {{-- CARGO ITEMS --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Daftar Barang Cargo</h3>
            <span>Cargo Items</span>
        </div>

        <div class="ct-table-wrap">
            <table class="ct-table">
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
                            <td><strong>{{ $item->item_name }}</strong></td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_weight_kg ?? '0' }} kg</td>
                            <td>{{ $item->unit_volume_m3 ?? '0' }} m³</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ct-empty">
                                Belum ada rincian barang cargo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- TRACKING --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Riwayat Tracking</h3>
            <span>Shipment Activity</span>
        </div>

        @if (session('success'))
            <div style="margin: 14px 0; padding: 12px 15px; border: 1px solid #a7f3d0; border-radius: 9px; background: #ecfdf5; color: #065f46;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="margin: 14px 0; padding: 12px 15px; border: 1px solid #fecaca; border-radius: 9px; background: #fef2f2; color: #991b1b;">
                <strong>Periksa kembali data tracking:</strong>
                <ul style="margin: 7px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'operator'], true))
            <form method="POST" action="{{ route('shipments.tracking.store', $shipment) }}"
                  style="display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:12px; margin:18px 0 22px; padding:16px; border:1px solid #dbe7f5; border-radius:12px; background:#f8fbff;">
                @csrf
                <div>
                    <label for="event_type" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700;">Jenis Aktivitas</label>
                    <select id="event_type" name="event_type" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:7px;">
                        <option value="STATUS_UPDATE">Pembaruan Status</option>
                        <option value="PICKUP">Barang Dijemput</option>
                        <option value="DEPARTURE">Keberangkatan</option>
                        <option value="ARRIVAL">Tiba di Lokasi</option>
                        <option value="DELIVERY">Pengantaran</option>
                        <option value="INSPECTION">Pemeriksaan</option>
                    </select>
                </div>
                <div>
                    <label for="tracking_status" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700;">Status Pengiriman</label>
                    <select id="tracking_status" name="status" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:7px;">
                        @foreach (['CREATED', 'PENDING', 'PICKED_UP', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED', 'CANCELLED'] as $trackingStatus)
                            <option value="{{ $trackingStatus }}" @selected($shipment->current_status === $trackingStatus)>{{ str_replace('_', ' ', $trackingStatus) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="event_at" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700;">Waktu Aktivitas</label>
                    <input id="event_at" type="datetime-local" name="event_at" value="{{ old('event_at', now()->format('Y-m-d\TH:i')) }}" required style="width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:7px;">
                </div>
                <div>
                    <label for="location_name" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700;">Lokasi</label>
                    <input id="location_name" type="text" name="location_name" value="{{ old('location_name') }}" maxlength="150" placeholder="Contoh: Gudang Medan" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:7px;">
                </div>
                <div style="grid-column:1/-1;">
                    <label for="tracking_description" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700;">Keterangan</label>
                    <textarea id="tracking_description" name="description" rows="2" maxlength="2000" placeholder="Tambahkan informasi perjalanan barang..." style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:7px; resize:vertical;">{{ old('description') }}</textarea>
                </div>
                <div style="grid-column:1/-1;">
                    <button type="submit" style="padding:10px 17px; border:0; border-radius:8px; background:#1556a8; color:white; font-weight:700; cursor:pointer;">Simpan Pembaruan Tracking</button>
                </div>
            </form>
        @endif

        @if ($shipment->trackingEvents->isNotEmpty())
            <div class="ct-timeline">
                @foreach ($shipment->trackingEvents->sortByDesc('event_at') as $event)
                    <div class="ct-timeline-item">
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
            <div class="ct-footer-note">
                <strong>JEK:</strong>
                Belum ada riwayat tracking untuk pengiriman ini.
            </div>
        @endif
    </section>

    {{-- EXCEPTIONS --}}
    <section class="ct-panel">
        <div class="ct-panel-heading">
            <h3>Informasi Kendala Pengiriman</h3>
            <span>Shipment Exceptions</span>
        </div>

        <div class="ct-table-wrap">
            <table class="ct-table">
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
                                <div class="ct-muted">
                                    {{ $exception->description ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $exception->severity }}</td>
                            <td>
                                <span class="ct-exception">
                                    {{ $exception->status }}
                                </span>
                            </td>
                            <td>
                                {{ optional($exception->detected_at)->format('d M Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ct-empty">
                                Tidak ada kendala pengiriman yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="ct-footer-note">
        <strong>CargoTrace</strong>
        <span>Track • Manage • Deliver — Informasi pengiriman dalam satu sistem.</span>
    </div>

</div>

@endsection
