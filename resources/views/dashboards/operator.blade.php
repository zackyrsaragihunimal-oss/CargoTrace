@extends('layouts.app')

@section('title', 'Operator Dashboard')
@section('header', 'Logistics Operator Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#exceptions">Assigned Exceptions</a>
    <a href="#tracking">My Tracking Updates</a>
    <a href="{{ route('shipments.index') }}">Shipments</a>
@endsection

@section('content')

<style>
    .ct-op { color: #142b4e; }
    .ct-op * { box-sizing: border-box; }

    .ct-op-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        min-height: 260px;
        padding: 35px;
        overflow: hidden;
        border-radius: 22px;
        color: #fff;
        background: linear-gradient(115deg, #102e59, #1556a5 68%, #2588e8);
        box-shadow: 0 15px 35px rgba(20, 60, 110, .15);
    }

    .ct-op-hero-content { max-width: 590px; }
    .ct-op-label {
        display: inline-block;
        padding: 8px 14px;
        margin-bottom: 17px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 30px;
        background: rgba(255,255,255,.12);
        color: #e4f3ff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
    }
    .ct-op-hero h1 {
        margin: 0 0 12px;
        color: #fff;
        font-size: clamp(26px, 3vw, 36px);
        font-weight: 800;
        line-height: 1.2;
    }
    .ct-op-hero p {
        margin: 0;
        color: #dcecff;
        font-size: 14px;
        line-height: 1.8;
    }
    .ct-op-access {
        display: inline-block;
        margin-top: 20px;
        padding: 9px 15px;
        border-radius: 30px;
        background: #dff2ff;
        color: #14518d;
        font-size: 12px;
        font-weight: 800;
    }
    .ct-op-mascot {
        flex: 0 0 215px;
        width: 215px;
        height: 215px;
        object-fit: contain;
        border-radius: 20px;
    }

    .ct-op-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 15px;
        margin-top: 24px;
    }
    .ct-op-stat {
        padding: 20px;
        border: 1px solid #e3ebf5;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(22, 50, 85, .045);
    }
    .ct-op-stat span {
        color: #718096;
        font-size: 11px;
        font-weight: 800;
    }
    .ct-op-stat strong {
        display: block;
        margin-top: 9px;
        color: #17365f;
        font-size: 29px;
    }

    .ct-op-section { margin-top: 30px; }
    .ct-op-section h2 {
        margin: 0 0 14px;
        color: #15345d;
        font-size: 20px;
        font-weight: 800;
    }
    .ct-op-panel {
        overflow-x: auto;
        padding: 18px;
        border: 1px solid #e3ebf5;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(22, 50, 85, .035);
    }
    .ct-op-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .ct-op-table th {
        padding: 12px 10px;
        background: #f5f8fc;
        color: #718096;
        text-align: left;
        white-space: nowrap;
    }
    .ct-op-table td {
        padding: 13px 10px;
        border-bottom: 1px solid #edf1f6;
        color: #42546b;
    }
    .ct-op-table tr:last-child td { border-bottom: 0; }
    .ct-op-table a {
        color: #1766b7;
        font-weight: 800;
        text-decoration: none;
    }
    .ct-op-pill {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        background: #e8f1fc;
        color: #245b91;
        font-size: 10px;
        font-weight: 800;
    }
    .ct-op-empty {
        padding: 24px;
        color: #8493a8;
        font-size: 13px;
        text-align: center;
    }
    .ct-op-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 25px;
    }
    .ct-op-actions a {
        padding: 11px 15px;
        border-radius: 10px;
        background: #edf5ff;
        color: #1766b7;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    @media(max-width:700px) {
        .ct-op-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 25px;
        }
        .ct-op-mascot {
            align-self: center;
            width: 185px;
            height: 185px;
        }
        .ct-op-panel { padding: 12px; }
    }
</style>

<div class="ct-op">

    <section class="ct-op-hero" id="overview">
        <div class="ct-op-hero-content">
            <span class="ct-op-label">CARGOTRACE - LOGISTICS OPERATIONS</span>

            <h1>Welcome, {{ $user->name }}!</h1>

            <p>
                Selamat datang di pusat operasional CargoTrace.
                Pantau exception yang ditugaskan kepada Anda,
                catat aktivitas tracking, dan bantu menjaga ketepatan
                informasi perjalanan pengiriman.
            </p>

            <span class="ct-op-access">OPERATOR ACCESS</span>
        </div>

        <img
            src="{{ asset('images/jek-hero.png') }}"
            alt="JEK - CargoTrace Mascot"
            class="ct-op-mascot"
        >
    </section>

    <section class="ct-op-stats">
        <article class="ct-op-stat">
            <span>ASSIGNED EXCEPTIONS</span>
            <strong>{{ number_format($assignedExceptionCount) }}</strong>
        </article>

        <article class="ct-op-stat">
            <span>OPEN / IN PROGRESS</span>
            <strong>{{ number_format($openExceptionCount) }}</strong>
        </article>

        <article class="ct-op-stat">
            <span>RESOLVED EXCEPTIONS</span>
            <strong>{{ number_format($resolvedExceptionCount) }}</strong>
        </article>

        <article class="ct-op-stat">
            <span>TRACKING UPDATES TODAY</span>
            <strong>{{ number_format($todayTrackingCount) }}</strong>
        </article>
    </section>

    <section class="ct-op-section" id="exceptions">
        <h2>My Assigned Exceptions</h2>

        <div class="ct-op-panel">
            @if ($recentAssignedExceptions->isEmpty())
                <div class="ct-op-empty">
                    Belum ada exception yang ditugaskan kepada Anda.
                </div>
            @else
                <table class="ct-op-table">
                    <thead>
                        <tr>
                            <th>Exception</th>
                            <th>Shipment</th>
                            <th>Customer</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Detected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentAssignedExceptions as $exception)
                            <tr>
                                <td>
                                    <a href="{{ route('exceptions.index') }}">
                                        {{ $exception->exception_code }}
                                    </a>
                                    <br>
                                    {{ $exception->title }}
                                </td>
                                <td>{{ $exception->shipment?->shipment_number ?? '-' }}</td>
                                <td>{{ $exception->shipment?->customer?->name ?? '-' }}</td>
                                <td><span class="ct-op-pill">{{ $exception->severity }}</span></td>
                                <td><span class="ct-op-pill">{{ str_replace('_', ' ', $exception->status) }}</span></td>
                                <td>{{ $exception->detected_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="ct-op-section" id="tracking">
        <h2>My Recent Tracking Updates</h2>

        <div class="ct-op-panel">
            @if ($recentTrackingEvents->isEmpty())
                <div class="ct-op-empty">
                    Belum ada tracking event yang dicatat oleh akun Anda.
                </div>
            @else
                <table class="ct-op-table">
                    <thead>
                        <tr>
                            <th>Shipment</th>
                            <th>Event</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Event Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTrackingEvents as $event)
                            <tr>
                                <td>
                                    @if ($event->shipment)
                                        <a href="{{ route('shipments.show', $event->shipment) }}">
                                            {{ $event->shipment->shipment_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ str_replace('_', ' ', $event->event_type) }}</td>
                                <td>{{ $event->status ?? '-' }}</td>
                                <td>{{ $event->location_name ?? '-' }}</td>
                                <td>{{ $event->event_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <div class="ct-op-actions">
        <a href="{{ route('shipments.index') }}">View Shipments</a>
        <a href="{{ route('exceptions.index') }}">Manage Exceptions</a>
        <a href="{{ route('notifications.index') }}">View Notifications</a>
    </div>

</div>

@endsection