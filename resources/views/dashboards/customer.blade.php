@extends('layouts.app')

@section('title', 'Customer Dashboard')
@section('header', 'Customer Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#my-shipments">My Shipments</a>
    <a href="#notifications">Notifications</a>
    <a href="{{ route('exceptions.index') }}">Shipment Exceptions</a>
@endsection

@section('content')

<style>
    .ct-customer { color: #17304f; }
    .ct-customer * { box-sizing: border-box; }

    .ct-customer-hero {
        padding: 28px;
        margin-bottom: 22px;
        border-radius: 19px;
        background: linear-gradient(115deg, #102e59, #1766b7);
        color: #fff;
    }
    .ct-customer-hero h1 {
        margin: 0 0 9px;
        color: #fff;
        font-size: 27px;
    }
    .ct-customer-hero p {
        margin: 0;
        color: #dcecff;
        font-size: 14px;
        line-height: 1.8;
    }

    .ct-customer-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
        gap: 15px;
        margin-bottom: 28px;
    }
    .ct-customer-stat {
        padding: 20px;
        border: 1px solid #e5edf6;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(20, 45, 75, .04);
    }
    .ct-customer-stat span {
        color: #718096;
        font-size: 11px;
        font-weight: 800;
    }
    .ct-customer-stat strong {
        display: block;
        margin-top: 9px;
        color: #15345d;
        font-size: 29px;
    }

    .ct-customer-section { margin-bottom: 28px; }
    .ct-customer-section h2 {
        margin: 0 0 14px;
        color: #17365f;
        font-size: 19px;
    }
    .ct-customer-panel {
        overflow-x: auto;
        padding: 19px;
        border: 1px solid #e5edf6;
        border-radius: 15px;
        background: #fff;
    }
    .ct-customer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .ct-customer-table th {
        padding: 12px 10px;
        background: #f7f9fc;
        color: #718096;
        text-align: left;
        white-space: nowrap;
    }
    .ct-customer-table td {
        padding: 13px 10px;
        border-bottom: 1px solid #edf1f6;
        color: #42546b;
    }
    .ct-customer-table tr:last-child td { border-bottom: 0; }
    .ct-customer-table a {
        color: #1766b7;
        font-weight: 800;
        text-decoration: none;
    }
    .ct-customer-pill {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        background: #e8f1fc;
        color: #245b91;
        font-size: 10px;
        font-weight: 800;
    }
    .ct-customer-empty {
        padding: 24px;
        color: #8493a8;
        font-size: 13px;
        text-align: center;
    }
    .ct-customer-notification {
        padding: 14px 4px;
        border-bottom: 1px solid #edf1f6;
    }
    .ct-customer-notification:last-child { border-bottom: 0; }
    .ct-customer-notification h3 {
        margin: 0 0 6px;
        color: #17365f;
        font-size: 14px;
    }
    .ct-customer-notification p {
        margin: 0 0 7px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }
    .ct-customer-notification small {
        color: #94a3b8;
        font-size: 11px;
    }
    .ct-customer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }
    .ct-customer-actions a {
        padding: 11px 15px;
        border-radius: 10px;
        background: #edf5ff;
        color: #1766b7;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    @media(max-width:600px) {
        .ct-customer-hero { padding: 22px; }
        .ct-customer-hero h1 { font-size: 23px; }
        .ct-customer-panel { padding: 12px; }
    }
</style>

<div class="ct-customer">

    <section class="ct-customer-hero" id="overview">
        <h1>Welcome, {{ $user->name }}!</h1>
        <p>
            Selamat datang di CargoTrace. Pantau seluruh pengiriman
            milik Anda, lihat perkembangan status, dan dapatkan
            informasi terbaru mengenai perjalanan barang.
        </p>
    </section>

    <section class="ct-customer-stats">
        <article class="ct-customer-stat">
            <span>TOTAL MY SHIPMENTS</span>
            <strong>{{ number_format($totalShipments) }}</strong>
        </article>

        <article class="ct-customer-stat">
            <span>ACTIVE SHIPMENTS</span>
            <strong>{{ number_format($activeShipments) }}</strong>
        </article>

        <article class="ct-customer-stat">
            <span>DELIVERED SHIPMENTS</span>
            <strong>{{ number_format($deliveredShipments) }}</strong>
        </article>

        <article class="ct-customer-stat">
            <span>OPEN EXCEPTIONS</span>
            <strong>{{ number_format($openExceptions) }}</strong>
        </article>

        <article class="ct-customer-stat">
            <span>UNREAD NOTIFICATIONS</span>
            <strong>{{ number_format($unreadNotifications) }}</strong>
        </article>
    </section>

    <section class="ct-customer-section">
        <h2>My Shipment Status</h2>
        <div class="ct-customer-panel">
            <div class="ct-customer-stats" style="margin: 0;">
                @forelse (['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED'] as $status)
                    <article class="ct-customer-stat">
                        <span>{{ str_replace('_', ' ', $status) }}</span>
                        <strong>{{ number_format((int) ($shipmentStatusCounts[$status] ?? 0)) }}</strong>
                    </article>
                @empty
                    <div class="ct-customer-empty">Belum ada data status pengiriman.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="ct-customer-section" id="my-shipments">
        <h2>My Recent Shipments</h2>
        <div class="ct-customer-panel">
            @if ($recentShipments->isEmpty())
                <div class="ct-customer-empty">
                    Anda belum memiliki pengiriman terdaftar.
                </div>
            @else
                <table class="ct-customer-table">
                    <thead>
                        <tr>
                            <th>Shipment Number</th>
                            <th>Cargo</th>
                            <th>Route</th>
                            <th>Carrier</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentShipments as $shipment)
                            <tr>
                                <td>{{ $shipment->shipment_number }}</td>
                                <td>{{ $shipment->cargo_type }}</td>
                                <td>
                                    {{ $shipment->originLocation?->city ?? '-' }}
                                    &rarr;
                                    {{ $shipment->destinationLocation?->city ?? '-' }}
                                </td>
                                <td>{{ $shipment->carrier?->name ?? '-' }}</td>
                                <td>
                                    <span class="ct-customer-pill">
                                        {{ str_replace('_', ' ', $shipment->current_status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('shipments.show', $shipment) }}">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="ct-customer-section" id="notifications">
        <h2>Recent Notifications</h2>
        <div class="ct-customer-panel">
            @if ($recentNotifications->isEmpty())
                <div class="ct-customer-empty">
                    Belum ada notifikasi untuk akun Anda.
                </div>
            @else
                @foreach ($recentNotifications as $notification)
                    <article class="ct-customer-notification">
                        <h3>
                            {{ $notification->title }}
                            @if (!$notification->read_at)
                                <span class="ct-customer-pill">UNREAD</span>
                            @endif
                        </h3>

                        <p>{{ $notification->message }}</p>

                        <small>
                            {{ $notification->created_at?->format('d M Y H:i') ?? '-' }}
                            @if ($notification->shipment)
                                · Shipment: {{ $notification->shipment->shipment_number }}
                            @endif
                        </small>
                    </article>
                @endforeach
            @endif

            <div class="ct-customer-actions">
                <a href="{{ route('notifications.index') }}">View All Notifications</a>
                <a href="{{ route('shipments.index') }}">View All My Shipments</a>
            </div>
        </div>
    </section>

</div>

@endsection