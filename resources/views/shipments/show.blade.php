
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
        width: 100%;
        max-width: 1450px;
        margin: 0 auto;
        color: #172033;
    }

    .ct-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }

    .ct-heading h1 {
        margin: 0 0 6px;
        color: #172b4d;
        font-size: 24px;
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .ct-heading p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .ct-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        background: #fff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease, border-color .2s ease;
    }

    .ct-back:hover {
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .ct-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(180px, .65fr);
        align-items: center;
        gap: 20px;
        overflow: hidden;
        padding: 25px 28px;
        margin-bottom: 18px;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: linear-gradient(115deg, #eff6ff, #fff 75%);
    }

    .ct-eyebrow {
        display: inline-block;
        margin-bottom: 12px;
        padding: 6px 10px;
        border: 1px solid #dbeafe;
        border-radius: 20px;
        background: #fff;
        color: #2563eb;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .7px;
    }

    .ct-hero h2 {
        margin: 0 0 10px;
        color: #172b4d;
        font-size: clamp(22px, 3vw, 29px);
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .ct-hero p {
        max-width: 560px;
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .ct-hero-status {
        display: inline-block;
        margin-top: 15px;
        padding: 7px 12px;
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-hero-mascot {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .ct-hero-mascot img {
        display: block;
        width: 100%;
        max-width: 190px;
        max-height: 190px;
        object-fit: contain;
    }

    .ct-panel {
        padding: 20px;
        margin-bottom: 16px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
    }

    .ct-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .ct-panel-heading h3 {
        margin: 0;
        color: #172b4d;
        font-size: 15px;
        font-weight: 750;
    }

    .ct-panel-heading span {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 600;
    }

    .ct-route {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        align-items: center;
        gap: 18px;
        padding: 20px;
        border: 1px solid #edf0f5;
        border-radius: 10px;
        background: #f8fafc;
    }

    .ct-route-label {
        margin-bottom: 7px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .7px;
    }

    .ct-route-city {
        color: #172b4d;
        font-size: 18px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .ct-route-country {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
    }

    .ct-route-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #dbeafe;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 20px;
    }

    .ct-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .ct-info {
        min-width: 0;
        padding: 15px;
        border: 1px solid #edf0f5;
        border-radius: 9px;
        background: #fafbfc;
    }

    .ct-info small {
        display: block;
        margin-bottom: 7px;
        color: #94a3b8;
        font-size: 10px;
    }

    .ct-info strong {
        display: block;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .ct-schedule {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .ct-schedule-item {
        padding: 15px;
        border: 1px solid #edf0f5;
        border-radius: 9px;
        background: #fafbfc;
    }

    .ct-schedule-item small {
        display: block;
        margin-bottom: 8px;
        color: #94a3b8;
        font-size: 10px;
    }

    .ct-schedule-item strong {
        color: #1e40af;
        font-size: 12px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .ct-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #edf0f5;
        border-radius: 9px;
    }

    .ct-table {
        width: 100%;
        min-width: 570px;
        border-collapse: collapse;
    }

    .ct-table th {
        padding: 12px 13px;
        border-bottom: 1px solid #e5eaf1;
        background: #f6f8fb;
        color: #64748b;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ct-table td {
        padding: 13px;
        border-bottom: 1px solid #edf0f5;
        color: #475569;
        font-size: 11px;
        vertical-align: middle;
    }

    .ct-table tbody tr {
        transition: background .15s ease;
    }

    .ct-table tbody tr:hover {
        background: #fafcff;
    }

    .ct-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ct-table td strong {
        color: #334155;
        font-weight: 700;
    }

    .ct-muted {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.5;
    }

    .ct-empty {
        padding: 28px 12px !important;
        color: #94a3b8 !important;
        text-align: center;
    }

    .ct-timeline {
        margin-left: 8px;
        padding-left: 23px;
        border-left: 2px solid #dbeafe;
    }

    .ct-timeline-item {
        position: relative;
        padding-bottom: 23px;
    }

    .ct-timeline-item:last-child {
        padding-bottom: 0;
    }

    .ct-timeline-item::before {
        position: absolute;
        top: 2px;
        left: -30px;
        width: 10px;
        height: 10px;
        border: 3px solid #dbeafe;
        border-radius: 50%;
        background: #2563eb;
        content: "";
    }

    .ct-timeline-item strong {
        display: block;
        margin-bottom: 7px;
        color: #1e3a5f;
        font-size: 12px;
        font-weight: 750;
    }

    .ct-timeline-item p {
        margin: 5px 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .ct-timeline-item small {
        display: inline-block;
        margin-top: 5px;
        color: #94a3b8;
        font-size: 10px;
    }

    .ct-exception {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid #fed7aa;
        border-radius: 20px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 10px;
        font-weight: 750;
    }

    .ct-footer-note {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 17px;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        background: #eff6ff;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    .ct-footer-note strong {
        flex-shrink: 0;
        color: #1d4ed8;
        font-weight: 750;
    }

    @media (max-width: 900px) {
        .ct-grid,
        .ct-schedule {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .ct-heading h1 {
            font-size: 21px;
        }

        .ct-hero {
            grid-template-columns: 1fr;
            padding: 20px;
        }

        .ct-hero-mascot img {
            max-width: 145px;
            max-height: 145px;
        }

        .ct-route {
            gap: 9px;
            padding: 14px;
        }

        .ct-route-city {
            font-size: 14px;
        }

        .ct-route-icon {
            width: 34px;
            height: 34px;
            font-size: 16px;
        }

        .ct-grid,
        .ct-schedule {
            grid-template-columns: 1fr;
        }

        .ct-panel {
            padding: 14px;
        }

        .ct-footer-note {
            align-items: flex-start;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-back,
        .ct-table tbody tr {
            transition: none;
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
                src="{{ asset('images/CargoTrace_JEK_Mascot_Pack/02_Shipment_Delivery/jek-package.png') }}"
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
                        <option value="BOOKING">Konfirmasi Booking</option>
                        <option value="PICKUP">Barang Dijemput</option>
                        <option value="DEPARTURE">Keberangkatan</option>
                        <option value="ARRIVAL">Tiba di Lokasi</option>
                        <option value="DELIVERY">Pengantaran</option>
                        <option value="INSPECTION">Pemeriksaan</option>
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
                @foreach ($shipment->trackingEvents->sortBy('event_at') as $event)
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
