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
    .ct-customer {
        color: #172033;
        width: 100%;
    }

    .ct-customer * {
        box-sizing: border-box;
    }

    .ct-customer-hero {
        position: relative;
        overflow: hidden;
        padding: 28px 30px;
        margin-bottom: 22px;
        border: 1px solid #e5eaf1;
        border-radius: 16px;
        background: #fff;
    }

    .ct-customer-hero h1 {
        margin: 0 0 9px;
        color: #172b4d;
        font-size: 25px;
        font-weight: 750;
        letter-spacing: -.6px;
    }

    .ct-customer-hero p {
        max-width: 680px;
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .ct-customer-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 26px;
    }

    .ct-customer-stat {
        min-width: 0;
        padding: 19px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
        transition: border-color .2s ease, transform .2s ease;
    }

    .ct-customer-stat:hover {
        border-color: #cbd8ea;
        transform: translateY(-2px);
    }

    .ct-customer-stat span {
        display: block;
        color: #64748b;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .45px;
        line-height: 1.5;
    }

    .ct-customer-stat strong {
        display: block;
        margin-top: 10px;
        color: #172b4d;
        font-size: 27px;
        font-weight: 750;
        line-height: 1.2;
    }

    .ct-customer-section {
        margin-bottom: 25px;
    }

    .ct-customer-section h2 {
        margin: 0 0 13px;
        color: #172b4d;
        font-size: 17px;
        font-weight: 750;
        letter-spacing: -.2px;
    }

    .ct-customer-panel {
        overflow-x: auto;
        padding: 17px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
    }

    .ct-customer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        white-space: nowrap;
    }

    .ct-customer-table th {
        padding: 12px 11px;
        background: #f6f8fb;
        color: #64748b;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .4px;
        text-align: left;
    }

    .ct-customer-table th:first-child {
        border-radius: 7px 0 0 7px;
    }

    .ct-customer-table th:last-child {
        border-radius: 0 7px 7px 0;
    }

    .ct-customer-table td {
        padding: 14px 11px;
        border-bottom: 1px solid #edf0f5;
        color: #475569;
        vertical-align: middle;
    }

    .ct-customer-table tr:last-child td {
        border-bottom: 0;
    }

    .ct-customer-table a {
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .ct-customer-table a:hover {
        text-decoration: underline;
    }

    .ct-customer-pill {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid #dbeafe;
        border-radius: 20px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 750;
        white-space: nowrap;
    }

    .ct-customer-empty {
        padding: 28px 18px;
        color: #94a3b8;
        font-size: 12px;
        text-align: center;
    }

    .ct-customer-notification {
        padding: 15px 5px;
        border-bottom: 1px solid #edf0f5;
    }

    .ct-customer-notification:last-child {
        border-bottom: 0;
    }

    .ct-customer-notification h3 {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin: 0 0 7px;
        color: #172b4d;
        font-size: 13px;
        font-weight: 700;
    }

    .ct-customer-notification p {
        margin: 0 0 8px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .ct-customer-notification small {
        color: #94a3b8;
        font-size: 10px;
    }

    .ct-customer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 17px;
    }

    .ct-customer-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 13px;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease;
    }

    .ct-customer-actions a:hover {
        background: #dbeafe;
    }

    @media (max-width: 1100px) {
        .ct-customer-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .ct-customer-hero-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .ct-customer-hero-copy {
        min-width: 0;
        flex: 1;
    }

    .ct-customer-hero-mascot {
        display: block;
        width: clamp(110px, 18vw, 175px);
        max-height: 175px;
        object-fit: contain;
        flex: 0 0 auto;
    }
    @media (max-width: 700px) {
        .ct-customer-hero {
            padding: 22px;
        }

        .ct-customer-hero-inner {
            gap: 10px;
        }

        .ct-customer-hero-mascot {
            width: 105px;
            max-height: 125px;
        }

        .ct-customer-hero h1 {
            font-size: 22px;
        }

        .ct-customer-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .ct-customer-stat {
            padding: 15px;
        }

        .ct-customer-stat strong {
            font-size: 24px;
        }

        .ct-customer-panel {
            padding: 12px;
        }
    }

    @media (max-width: 380px) {
        .ct-customer-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-customer-stat,
        .ct-customer-actions a {
            transition: none;
        }
    }
</style>

<div class="ct-customer">

    <section class="ct-customer-hero" id="overview">
        <div class="ct-customer-hero-inner">
            <div class="ct-customer-hero-copy">
                <h1>Welcome, {{ $user->name }}!</h1>
                <p>
                    Selamat datang di CargoTrace. Pantau seluruh pengiriman
                    milik Anda, lihat perkembangan status, dan dapatkan
                    informasi terbaru mengenai perjalanan barang.
                </p>
            </div>
            <img
                class="ct-customer-hero-mascot"
                src="{{ asset('images/CargoTrace_JEK_Mascot_Pack/04_Dashboard_Roles/jek-customer.png') }}"
                alt="JEK CargoTrace Customer"
            >
        </div>
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
        <div class="ct-customer-panel" data-customer-notification-list>
            @if ($recentNotifications->isEmpty())
                <div class="ct-customer-empty" data-customer-notification-empty>
                    Belum ada notifikasi untuk akun Anda.
                </div>
            @else
                @foreach ($recentNotifications as $notification)
                    <article class="ct-customer-notification" data-notification-id="{{ $notification->id }}">
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
