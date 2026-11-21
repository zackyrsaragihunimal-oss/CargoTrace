
@extends('layouts.app')

@section('header', 'Shipment Management')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        Shipments
    </a>
@endsection

@section('content')

<style>
    .shipment-page {
        width: 100%;
        color: #172033;
    }

    .shipment-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .shipment-heading h2 {
        margin: 0;
        color: #172b4d;
        font-size: 23px;
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .shipment-heading p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .shipment-panel {
        overflow: hidden;
        padding: 19px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
    }

    .shipment-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }

    .shipment-filters input,
    .shipment-filters select {
        min-height: 41px;
        padding: 9px 12px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #243b53;
        font-size: 12px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .shipment-filters input {
        flex: 1;
        min-width: 210px;
    }

    .shipment-filters input:focus,
    .shipment-filters select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09);
    }

    .shipment-filters button {
        min-height: 41px;
        padding: 10px 19px;
        border: 0;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s ease;
    }

    .shipment-filters button:hover {
        background: #1d4ed8;
    }

    .shipment-filters a {
        display: inline-flex;
        align-items: center;
        min-height: 41px;
        padding: 9px 12px;
        border-radius: 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .shipment-filters a:hover {
        background: #f1f5f9;
        color: #172b4d;
    }

    .shipment-table-wrapper {
        overflow-x: auto;
        border: 1px solid #edf0f5;
        border-radius: 9px;
    }

    .shipment-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .shipment-table th {
        padding: 13px 12px;
        border-bottom: 1px solid #e5eaf1;
        background: #f6f8fb;
        color: #64748b;
        font-size: 10px;
        font-weight: 750;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .45px;
        white-space: nowrap;
    }

    .shipment-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #edf0f5;
        color: #475569;
        font-size: 12px;
        vertical-align: middle;
    }

    .shipment-table tbody tr {
        transition: background .15s ease;
    }

    .shipment-table tbody tr:hover {
        background: #fafcff;
    }

    .shipment-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .shipment-number {
        color: #2563eb;
        font-weight: 750;
        text-decoration: none;
    }

    .shipment-number:hover {
        text-decoration: underline;
    }

    .shipment-route {
        color: #243b53;
        font-weight: 700;
    }

    .shipment-muted {
        display: block;
        margin-top: 5px;
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.5;
    }

    .shipment-status {
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

    .shipment-empty {
        padding: 48px 20px;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.7;
        text-align: center;
    }

    .shipment-empty strong {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 15px;
    }

    .shipment-pagination {
        padding-top: 18px;
    }

    @media (max-width: 700px) {
        .shipment-heading h2 {
            font-size: 20px;
        }

        .shipment-panel {
            padding: 12px;
        }

        .shipment-filters {
            align-items: stretch;
        }

        .shipment-filters input {
            flex-basis: 100%;
            min-width: 0;
        }

        .shipment-filters select {
            flex: 1;
            min-width: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .shipment-table tbody tr,
        .shipment-filters input,
        .shipment-filters select,
        .shipment-filters button {
            transition: none;
        }
    }
</style>

<div class="shipment-page">

    <div class="shipment-heading">
        <div>
            <h2>Daftar Pengiriman</h2>
            <p>Kelola dan pantau seluruh informasi pengiriman CargoTrace.</p>
        </div>
    </div>

    <div class="shipment-panel">

        <form method="GET"
              action="{{ route('shipments.index') }}"
              class="shipment-filters">

            <input
                type="text"
                name="search"
                placeholder="Cari nomor shipment atau jenis cargo..."
                value="{{ $search }}"
            >

            <select name="status">
                <option value="">Semua Status</option>
                @foreach ([
                    'CREATED',
                    'BOOKED',
                    'PENDING',
                    'PICKED_UP',
                    'IN_TRANSIT',
                    'ARRIVED',
                    'DELIVERED',
                    'CANCELLED'
                ] as $shipmentStatus)
                    <option value="{{ $shipmentStatus }}"
                        {{ $status === $shipmentStatus ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $shipmentStatus) }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Cari</button>

            @if ($search || $status)
                <a href="{{ route('shipments.index') }}">Reset</a>
            @endif

        </form>

        <div class="shipment-table-wrapper">

            <table class="shipment-table">
                <thead>
                    <tr>
                        <th>Shipment</th>
                        <th>Customer</th>
                        <th>Rute Pengiriman</th>
                        <th>Jenis Cargo</th>
                        <th>Carrier</th>
                        <th>Estimasi Tiba</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($shipments as $shipment)
                        <tr>
                            <td>
                                <a href="{{ route('shipments.show', $shipment->id) }}" class="shipment-number" style="text-decoration: none;">
                                    {{ $shipment->shipment_number }}
                                </a>
                                <span class="shipment-muted">
                                    ID: {{ $shipment->id }}
                                </span>
                            </td>

                            <td>
                                {{ $shipment->customer->name ?? 'Tidak tersedia' }}
                            </td>

                            <td>
                                <span class="shipment-route">
                                    {{ $shipment->originLocation->city ?? '-' }}
                                    →
                                    {{ $shipment->destinationLocation->city ?? '-' }}
                                </span>
                                <span class="shipment-muted">
                                    {{ $shipment->originLocation->country ?? '-' }}
                                    ke
                                    {{ $shipment->destinationLocation->country ?? '-' }}
                                </span>
                            </td>

                            <td>{{ $shipment->cargo_type }}</td>

                            <td>{{ $shipment->carrier->name ?? '-' }}</td>

                            <td>
                                {{ optional($shipment->estimated_arrival_at ?? $shipment->expected_arrival_at)->format('d M Y H:i') ?? '-' }}
                            </td>

                            <td>
                                <span class="shipment-status">
                                    {{ str_replace('_', ' ', $shipment->current_status) }}
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="shipment-empty">
                                    <strong>Belum Ada Data Shipment</strong>
                                    Belum ada pengiriman yang dapat ditampilkan.
                                    Data akan muncul setelah shipment dibuat.
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>

        <div class="shipment-pagination">
            {{ $shipments->links() }}
        </div>

    </div>
</div>

@endsection
