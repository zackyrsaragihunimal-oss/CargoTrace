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
    .ct-admin-dashboard {
        --admin-ink: #142B45;
        --admin-muted: #64766C;
        --admin-border: #E1EAE4;
        --admin-surface: #FFFFFF;
        --admin-bg: #F3F7F4;
        --admin-blue: #287A57;
        --admin-green: #287A57;
        --admin-amber: #b45309;
        --admin-red: #b91c1c;
        color: var(--admin-ink);
    }

    .ct-admin-dashboard,
    .ct-admin-dashboard * {
        box-sizing: border-box;
    }

    .ct-admin-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 22px;
    }

    .ct-admin-heading-copy {
        flex: 1;
        min-width: 0;
    }

    .ct-admin-heading-side {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 18px;
        flex-shrink: 0;
    }

    .ct-admin-mascot {
        display: block;
        width: 112px;
        height: 112px;
        object-fit: contain;
        object-position: center bottom;
        background: transparent;
    }

    .ct-admin-eyebrow {
        margin: 0 0 7px;
        color: var(--admin-blue);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .ct-admin-heading h1 {
        margin: 0;
        color: var(--admin-ink);
        font-size: clamp(23px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .ct-admin-heading p {
        margin: 7px 0 0;
        color: var(--admin-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .ct-admin-date {
        flex-shrink: 0;
        padding: 9px 12px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: var(--admin-surface);
        color: #526B60;
        font-size: 12px;
        font-weight: 700;
    }

    .ct-admin-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 25px;
    }

    .ct-admin-stat {
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--admin-border);
        border-radius: 13px;
        background: var(--admin-surface);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
    }

    .ct-admin-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .ct-admin-stat-label {
        color: var(--admin-muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .055em;
        line-height: 1.5;
        text-transform: uppercase;
    }

    .ct-admin-stat-mark {
        display: inline-flex;
        width: 29px;
        height: 29px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #E8F3EC;
        color: var(--admin-blue);
        font-size: 13px;
        font-weight: 900;
    }

    .ct-admin-stat:nth-child(2) .ct-admin-stat-mark,
    .ct-admin-stat:nth-child(3) .ct-admin-stat-mark {
        background: #E8F3EC;
        color: var(--admin-green);
    }

    .ct-admin-stat:nth-child(5) .ct-admin-stat-mark {
        background: #fff7ed;
        color: var(--admin-amber);
    }

    .ct-admin-stat-value {
        margin-top: 13px;
        color: var(--admin-ink);
        font-size: 29px;
        font-weight: 800;
        letter-spacing: -.045em;
        line-height: 1;
        overflow-wrap: anywhere;
    }

    .ct-admin-stat-note {
        margin-top: 8px;
        color: #91A198;
        font-size: 11px;
        line-height: 1.45;
    }

    .ct-admin-section {
        margin-bottom: 23px;
    }

    .ct-admin-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .ct-admin-section-heading h2 {
        margin: 0;
        color: var(--admin-ink);
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .ct-admin-section-caption {
        color: var(--admin-muted);
        font-size: 11px;
    }

    .ct-admin-panel {
        overflow: hidden;
        border: 1px solid var(--admin-border);
        border-radius: 13px;
        background: var(--admin-surface);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
    }

    .ct-admin-status-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        padding: 15px;
    }

    .ct-admin-status-item {
        min-width: 0;
        padding: 15px 12px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: var(--admin-bg);
    }

    .ct-admin-status-item strong {
        display: block;
        color: var(--admin-ink);
        font-size: 23px;
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .ct-admin-status-item span {
        display: block;
        margin-top: 5px;
        color: var(--admin-muted);
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .ct-admin-table-wrap {
        overflow-x: auto;
        width: 100%;
    }

    .ct-admin-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 12px;
        white-space: nowrap;
    }

    .ct-admin-table th {
        padding: 13px 16px;
        border-bottom: 1px solid var(--admin-border);
        background: #F7FAF8;
        color: #7b8798;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .ct-admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #E1EAE4;
        color: #526B60;
        vertical-align: middle;
    }

    .ct-admin-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ct-admin-table tbody tr:hover {
        background: #F3F7F4;
    }

    .ct-admin-table a {
        color: var(--admin-blue);
        font-weight: 800;
        text-decoration: none;
    }

    .ct-admin-table a:hover {
        text-decoration: underline;
    }

    .ct-admin-route {
        color: #526B60;
        font-weight: 600;
    }

    .ct-admin-route-arrow {
        padding: 0 5px;
        color: #91A198;
    }

    .ct-admin-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border: 1px solid #C9E3D3;
        border-radius: 20px;
        background: #E8F3EC;
        color: #1D6245;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .025em;
    }

    .ct-admin-empty {
        padding: 30px 18px;
        color: #91A198;
        font-size: 12px;
        text-align: center;
    }

    .ct-admin-management {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px;
    }

    .ct-admin-management-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-width: 0;
        padding: 15px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: #fff;
        color: var(--admin-ink);
        text-decoration: none;
        transition: border-color .18s ease, background .18s ease, transform .18s ease;
    }

    .ct-admin-management-link:hover {
        transform: translateY(-2px);
        border-color: #B5D9C2;
        background: #F7FAF8;
    }

    .ct-admin-management-link strong {
        display: block;
        font-size: 12px;
        font-weight: 800;
    }

    .ct-admin-management-link small {
        display: block;
        margin-top: 4px;
        color: var(--admin-muted);
        font-size: 10px;
        line-height: 1.4;
    }

    .ct-admin-management-arrow {
        color: var(--admin-blue);
        font-size: 17px;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .ct-admin-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ct-admin-status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .ct-admin-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .ct-admin-heading-side {
            width: 100%;
            justify-content: space-between;
        }

        .ct-admin-mascot {
            width: 88px;
            height: 88px;
        }

        .ct-admin-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .ct-admin-stat {
            padding: 14px;
        }

        .ct-admin-stat-value {
            font-size: 25px;
        }

        .ct-admin-status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 10px;
        }

        .ct-admin-management {
            grid-template-columns: 1fr;
            padding: 10px;
        }

        .ct-admin-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-admin-management-link {
            transition: none;
        }
    }

    /* ===== PREMIUM ADMIN DASHBOARD V1 ===== */

    .ct-admin-heading {
        position: relative;
        overflow: hidden;
        min-height: 190px;
        padding: 28px 30px;
        align-items: center;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 20px;
        background:
            radial-gradient(circle at 85% 15%, rgba(110,190,145,.25), transparent 30%),
            linear-gradient(120deg, #142B45 0%, #1D4650 58%, #287A57 100%);
        box-shadow: 0 12px 28px rgba(20,43,69,.13);
    }

    .ct-admin-heading::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 100px;
        bottom: -165px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        pointer-events: none;
    }

    .ct-admin-heading-copy,
    .ct-admin-heading-side {
        position: relative;
        z-index: 1;
    }

    .ct-admin-eyebrow {
        color: #B9E8CE;
        letter-spacing: .16em;
    }

    .ct-admin-heading h1 {
        color: #FFFFFF;
        font-size: clamp(25px, 3vw, 34px);
    }

    .ct-admin-heading p {
        color: rgba(255,255,255,.78);
    }

    .ct-admin-date {
        border-color: rgba(255,255,255,.2);
        background: rgba(255,255,255,.12);
        color: #FFFFFF;
        backdrop-filter: blur(8px);
    }

    .ct-admin-mascot {
        filter: drop-shadow(0 8px 10px rgba(0,0,0,.18));
        transition: transform .25s ease;
    }

    .ct-admin-mascot:hover {
        transform: translateY(-5px);
    }

    .ct-admin-stat {
        position: relative;
        overflow: hidden;
        padding: 20px;
        border-radius: 16px;
        border-top: 3px solid #287A57;
        box-shadow: 0 5px 15px rgba(20,43,69,.045);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .ct-admin-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(20,43,69,.09);
    }

    .ct-admin-stat:nth-child(2) {
        border-top-color: #4385B8;
    }

    .ct-admin-stat:nth-child(3) {
        border-top-color: #4D9478;
    }

    .ct-admin-stat:nth-child(4) {
        border-top-color: #8B78BB;
    }

    .ct-admin-stat:nth-child(5) {
        border-top-color: #D99445;
    }

    .ct-admin-stat:nth-child(2) .ct-admin-stat-mark {
        background: #EAF3FB;
        color: #4385B8;
    }

    .ct-admin-stat:nth-child(3) .ct-admin-stat-mark {
        background: #E8F3EC;
        color: #287A57;
    }

    .ct-admin-stat:nth-child(4) .ct-admin-stat-mark {
        background: #F0EDFA;
        color: #806AB0;
    }

    .ct-admin-stat:nth-child(5) .ct-admin-stat-mark {
        background: #FFF3E5;
        color: #B96C22;
    }

    .ct-admin-stat-value {
        font-size: 32px;
    }

    .ct-admin-status-item {
        border-left: 3px solid #91A198;
        border-radius: 11px;
        background: linear-gradient(145deg, #FFFFFF, #F3F7F4);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .ct-admin-status-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 16px rgba(20,43,69,.07);
    }

    .ct-admin-status-item:nth-child(1) {
        border-left-color: #91A198;
    }

    .ct-admin-status-item:nth-child(2) {
        border-left-color: #4385B8;
    }

    .ct-admin-status-item:nth-child(3) {
        border-left-color: #8B78BB;
    }

    .ct-admin-status-item:nth-child(4) {
        border-left-color: #D99445;
    }

    .ct-admin-status-item:nth-child(5) {
        border-left-color: #287A57;
    }

    .ct-admin-status-item:nth-child(6) {
        border-left-color: #1D6245;
    }

    .ct-admin-panel {
        border-radius: 16px;
        box-shadow: 0 5px 16px rgba(20,43,69,.035);
    }

    .ct-admin-management-link {
        min-height: 76px;
        border-radius: 13px;
        background: linear-gradient(145deg, #FFFFFF, #F8FBF9);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .ct-admin-management-link:hover {
        transform: translateY(-4px);
        border-color: #8FC4A5;
        background: #FFFFFF;
        box-shadow: 0 9px 20px rgba(40,122,87,.09);
    }

    .ct-admin-management-link strong {
        color: #142B45;
        font-size: 13px;
    }

    .ct-admin-management-arrow {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #E8F3EC;
        color: #287A57;
        transition: transform .2s ease, background .2s ease;
    }

    .ct-admin-management-link:hover .ct-admin-management-arrow {
        transform: translateX(3px);
        background: #287A57;
        color: #FFFFFF;
    }

    @media (max-width: 650px) {
        .ct-admin-heading {
            min-height: 0;
            padding: 22px 19px;
            border-radius: 16px;
        }

        .ct-admin-heading h1 {
            font-size: 25px;
        }

        .ct-admin-stat {
            padding: 15px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-admin-stat,
        .ct-admin-status-item,
        .ct-admin-mascot,
        .ct-admin-management-link,
        .ct-admin-management-arrow {
            transition: none;
        }
    }

    /* ===== END PREMIUM ADMIN DASHBOARD V1 ===== */
</style>

<div class="ct-admin-dashboard">
    <section class="ct-admin-heading" id="overview">
        <div class="ct-admin-heading-copy">
            <p class="ct-admin-eyebrow">CargoTrace / Control Center</p>
            <h1>Welcome back, {{ $user->name }}.</h1>
            <p>Monitor platform activity, shipment progress, and system operations from one place.</p>
        </div>

        <div class="ct-admin-heading-side">
            <div class="ct-admin-date">{{ now()->format('d M Y') }}</div>
            <img
                src="{{ asset('images/CargoTrace_JEK_Mascot_Pack/04_Dashboard_Roles/jek-admin.png') }}"
                alt="JEK - CargoTrace Administrator Mascot"
                class="ct-admin-mascot"
            >
        </div>
    </section>

    <section class="ct-admin-stats" aria-label="Platform statistics">
        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">Total Shipments</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">S</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($totalShipments) }}</div>
            <div class="ct-admin-stat-note">All registered shipments</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">Active Users</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">U</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($activeUsers) }}</div>
            <div class="ct-admin-stat-note">Accounts currently active</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">Customers</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">C</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="ct-admin-stat-note">Registered customer accounts</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">Operators</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">O</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($totalOperators) }}</div>
            <div class="ct-admin-stat-note">Registered operator accounts</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">Open Exceptions</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">!</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($openExceptions) }}</div>
            <div class="ct-admin-stat-note">Open and in progress</div>
        </article>
    </section>

    <section class="ct-admin-section" aria-labelledby="shipment-status-heading">
        <div class="ct-admin-section-heading">
            <h2 id="shipment-status-heading">Shipment Status Overview</h2>
            <span class="ct-admin-section-caption">Current platform distribution</span>
        </div>
        <div class="ct-admin-panel">
            <div class="ct-admin-status-grid">
                @foreach (['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED'] as $status)
                    <div class="ct-admin-status-item">
                        <strong>{{ number_format((int) ($shipmentStatusCounts[$status] ?? 0)) }}</strong>
                        <span>{{ str_replace('_', ' ', $status) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ct-admin-section" id="shipments">
        <div class="ct-admin-section-heading">
            <h2>Recent Shipments</h2>
            <span class="ct-admin-section-caption">Latest 6 registered shipments</span>
        </div>
        <div class="ct-admin-panel">
            @if ($recentShipments->isEmpty())
                <div class="ct-admin-empty">No shipments have been recorded yet.</div>
            @else
                <div class="ct-admin-table-wrap">
                    <table class="ct-admin-table">
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
                                        <span class="ct-admin-route">{{ $shipment->originLocation?->city ?? '-' }}</span>
                                        <span class="ct-admin-route-arrow">&rarr;</span>
                                        <span class="ct-admin-route">{{ $shipment->destinationLocation?->city ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="ct-admin-pill">{{ str_replace('_', ' ', $shipment->current_status) }}</span>
                                    </td>
                                    <td>{{ $shipment->created_at?->format('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <section class="ct-admin-section" id="activities">
        <div class="ct-admin-section-heading">
            <h2>Recent System Activities</h2>
            <span class="ct-admin-section-caption">Latest 6 audit log records</span>
        </div>
        <div class="ct-admin-panel">
            @if ($recentActivities->isEmpty())
                <div class="ct-admin-empty">No system activities have been recorded yet.</div>
            @else
                <div class="ct-admin-table-wrap">
                    <table class="ct-admin-table">
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
                                    <td><span class="ct-admin-pill">{{ $activity->action }}</span></td>
                                    <td>{{ $activity->user?->name ?? 'System / Unknown' }}</td>
                                    <td>{{ $activity->entity_type }} #{{ $activity->entity_id ?? '-' }}</td>
                                    <td>{{ $activity->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <section class="ct-admin-section" id="management">
        <div class="ct-admin-section-heading">
            <h2>System Management</h2>
            <span class="ct-admin-section-caption">Quick access to administration tools</span>
        </div>
        <div class="ct-admin-panel">
            <div class="ct-admin-management">
                <a class="ct-admin-management-link" href="{{ route('shipments.index') }}">
                    <span>
                        <strong>Manage Shipments</strong>
                        <small>Review and manage shipment records</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
                <a class="ct-admin-management-link" href="{{ route('exceptions.index') }}">
                    <span>
                        <strong>Manage Exceptions</strong>
                        <small>Review and assign shipment issues</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
                <a class="ct-admin-management-link" href="{{ route('notifications.index') }}">
                    <span>
                        <strong>View Notifications</strong>
                        <small>Open the system notification center</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
