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
    .ct-op {
        --op-primary: #2563eb;
        --op-text: #172033;
        --op-muted: #64748b;
        --op-border: #e5eaf1;
        color: var(--op-text);
    }

    .ct-op * { box-sizing: border-box; }

    .ct-op-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        min-height: 230px;
        padding: 30px;
        border: 1px solid var(--op-border);
        border-radius: 16px;
        background: #fff;
    }

    .ct-op-hero-content { max-width: 620px; }

    .ct-op-label {
        display: inline-block;
        margin-bottom: 14px;
        color: var(--op-primary);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .ct-op-hero h1 {
        margin: 0 0 10px;
        color: var(--op-text);
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        line-height: 1.2;
    }

    .ct-op-hero p {
        margin: 0;
        color: var(--op-muted);
        font-size: 14px;
        line-height: 1.8;
    }

    .ct-op-access {
        display: inline-block;
        margin-top: 18px;
        padding: 7px 11px;
        border: 1px solid #dbeafe;
        border-radius: 7px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-op-mascot {
        flex: 0 0 190px;
        width: 190px;
        height: 190px;
        object-fit: contain;
    }

    .ct-op-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }

    .ct-op-stat {
        min-width: 0;
        padding: 19px;
        border: 1px solid var(--op-border);
        border-radius: 12px;
        background: #fff;
    }

    .ct-op-stat span {
        color: var(--op-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .ct-op-stat strong {
        display: block;
        margin-top: 10px;
        color: var(--op-text);
        font-size: 29px;
        font-weight: 800;
    }

    .ct-op-section { margin-top: 28px; }

    .ct-op-section h2 {
        margin: 0 0 13px;
        color: var(--op-text);
        font-size: 17px;
        font-weight: 800;
    }

    .ct-op-panel {
        overflow-x: auto;
        border: 1px solid var(--op-border);
        border-radius: 12px;
        background: #fff;
    }

    .ct-op-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .ct-op-table th {
        padding: 13px 15px;
        background: #f8fafc;
        color: var(--op-muted);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        white-space: nowrap;
    }

    .ct-op-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #edf1f6;
        color: #475569;
        vertical-align: middle;
    }

    .ct-op-table tr:last-child td { border-bottom: 0; }

    .ct-op-table a {
        color: var(--op-primary);
        font-weight: 800;
        text-decoration: none;
    }

    .ct-op-table a:hover { text-decoration: underline; }

    .ct-op-pill {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
        color: #475569;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-op-empty {
        padding: 28px 18px;
        color: var(--op-muted);
        font-size: 13px;
        text-align: center;
    }

    .ct-op-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .ct-op-actions a {
        padding: 10px 14px;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .ct-op-actions a:hover { background: #dbeafe; }

    @media (max-width: 900px) {
        .ct-op-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 600px) {
        .ct-op-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 22px;
        }

        .ct-op-mascot {
            align-self: center;
            width: 155px;
            height: 155px;
        }

        .ct-op-panel { padding: 0; }
        .ct-op-table th, .ct-op-table td { padding: 12px; }
        .ct-op-stat { padding: 15px; }
        .ct-op-stat strong { font-size: 25px; }
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