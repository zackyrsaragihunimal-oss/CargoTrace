@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Administrator Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#shipments">Recent Shipments</a>
    <a href="#activities">Recent Activities</a>
    <a href="#management">System Management</a>
@endsection

@section('content')

<style>
    .ct-dashboard { color: #17304f; }
    .ct-dashboard * { box-sizing: border-box; }
    .ct-welcome {
        padding: 27px;
        margin-bottom: 22px;
        border-radius: 18px;
        background: linear-gradient(115deg, #102e59, #1766b7);
        color: white;
    }
    .ct-welcome h1 { margin: 0 0 8px; color: white; font-size: 27px; }
    .ct-welcome p { margin: 0; color: #dcecff; font-size: 14px; line-height: 1.7; }
    .ct-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 15px;
        margin-bottom: 28px;
    }
    .ct-stat {
        padding: 20px;
        border: 1px solid #e5edf6;
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 18px rgba(20, 45, 75, .04);
    }
    .ct-stat-label { color: #718096; font-size: 12px; font-weight: 700; }
    .ct-stat-value { margin-top: 9px; color: #15345d; font-size: 29px; font-weight: 800; }
    .ct-stat-note { margin-top: 5px; color: #8493a8; font-size: 11px; }
    .ct-section { margin-bottom: 28px; }
    .ct-section h2 { margin: 0 0 14px; color: #17365f; font-size: 19px; }
    .ct-panel {
        overflow-x: auto;
        padding: 20px;
        border: 1px solid #e5edf6;
        border-radius: 15px;
        background: white;
    }
    .ct-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ct-table th {
        padding: 12px 10px;
        color: #718096;
        background: #f7f9fc;
        text-align: left;
        white-space: nowrap;
    }
    .ct-table td { padding: 13px 10px; border-bottom: 1px solid #edf1f6; color: #42546b; }
    .ct-table tr:last-child td { border-bottom: 0; }
    .ct-table a { color: #1766b7; font-weight: 700; text-decoration: none; }
    .ct-pill {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        background: #e8f1fc;
        color: #245b91;
        font-size: 10px;
        font-weight: 800;
    }
    .ct-status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
    }
    .ct-status-item { padding: 15px; border-radius: 12px; background: #f5f8fc; }
    .ct-status-item strong { display: block; color: #17365f; font-size: 22px; }
    .ct-status-item span { color: #718096; font-size: 11px; }
    .ct-empty { padding: 24px; color: #8493a8; text-align: center; font-size: 13px; }
    .ct-links { display: flex; flex-wrap: wrap; gap: 10px; }
    .ct-links a {
        padding: 11px 15px;
        border-radius: 10px;
        background: #edf5ff;
        color: #1766b7;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }
    @media(max-width:600px) {
        .ct-welcome { padding: 22px; }
        .ct-welcome h1 { font-size: 23px; }
        .ct-panel { padding: 12px; }
    }
</style>

<div class="ct-dashboard">

    <section class="ct-welcome" id="overview">
        <h1>Welcome, {{ $user->name }}!</h1>
        <p>
            Centralized monitoring of CargoTrace logistics operations.
            Berikut ringkasan kondisi sistem berdasarkan data terbaru.
        </p>
    </section>

    <section class="ct-stats">
        <article class="ct-stat">
            <div class="ct-stat-label">TOTAL SHIPMENTS</div>
            <div class="ct-stat-value">{{ number_format($totalShipments) }}</div>
            <div class="ct-stat-note">Seluruh pengiriman terdaftar</div>
        </article>

        <article class="ct-stat">
            <div class="ct-stat-label">ACTIVE USERS</div>
            <div class="ct-stat-value">{{ number_format($activeUsers) }}</div>
            <div class="ct-stat-note">Semua role dengan akun aktif</div>
        </article>

        <article class="ct-stat">
            <div class="ct-stat-label">CUSTOMERS</div>
            <div class="ct-stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="ct-stat-note">Total akun customer</div>
        </article>

        <article class="ct-stat">
            <div class="ct-stat-label">OPERATORS</div>
            <div class="ct-stat-value">{{ number_format($totalOperators) }}</div>
            <div class="ct-stat-note">Total akun operator</div>
        </article>

        <article class="ct-stat">
            <div class="ct-stat-label">OPEN EXCEPTIONS</div>
            <div class="ct-stat-value">{{ number_format($openExceptions) }}</div>
            <div class="ct-stat-note">Open dan sedang ditangani</div>
        </article>
    </section>

    <section class="ct-section">
        <h2>Shipment Status Overview</h2>
        <div class="ct-panel">
            <div class="ct-status-grid">
                @forelse (['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED'] as $status)
                    <div class="ct-status-item">
                        <strong>{{ number_format((int) ($shipmentStatusCounts[$status] ?? 0)) }}</strong>
                        <span>{{ str_replace('_', ' ', $status) }}</span>
                    </div>
                @empty
                    <div class="ct-empty">Belum ada data status pengiriman.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="ct-section" id="shipments">
        <h2>Recent Shipments</h2>
        <div class="ct-panel">
            @if ($recentShipments->isEmpty())
                <div class="ct-empty">Belum ada pengiriman yang tercatat.</div>
            @else
                <table class="ct-table">
                    <thead>
                        <tr>
                            <th>Shipment Number</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentShipments as $shipment)
                            <tr>
                                <td>
                                    <a href="{{ route('shipments.show', $shipment) }}">
                                        {{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td>{{ $shipment->customer?->name ?? '-' }}</td>
                                <td>
                                    {{ $shipment->originLocation?->city ?? '-' }}
                                    &rarr;
                                    {{ $shipment->destinationLocation?->city ?? '-' }}
                                </td>
                                <td><span class="ct-pill">{{ str_replace('_', ' ', $shipment->current_status) }}</span></td>
                                <td>{{ $shipment->created_at?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="ct-section" id="activities">
        <h2>Recent System Activities</h2>
        <div class="ct-panel">
            @if ($recentActivities->isEmpty())
                <div class="ct-empty">Belum ada aktivitas audit yang tercatat.</div>
            @else
                <table class="ct-table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>User</th>
                            <th>Entity</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentActivities as $activity)
                            <tr>
                                <td><span class="ct-pill">{{ $activity->action }}</span></td>
                                <td>{{ $activity->user?->name ?? 'System / Unknown' }}</td>
                                <td>{{ $activity->entity_type }} #{{ $activity->entity_id ?? '-' }}</td>
                                <td>{{ $activity->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="ct-section" id="management">
        <h2>System Management</h2>
        <div class="ct-panel">
            <div class="ct-links">
                <a href="{{ route('shipments.index') }}">Manage Shipments</a>
                <a href="{{ route('exceptions.index') }}">Manage Exceptions</a>
                <a href="{{ route('notifications.index') }}">View Notifications</a>
            </div>
        </div>
    </section>

</div>
@endsection