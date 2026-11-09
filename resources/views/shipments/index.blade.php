
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
        color: #172b4d;
    }

    .shipment-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .shipment-heading h2 {
        margin: 0;
        font-size: 23px;
        font-weight: 700;
    }

    .shipment-heading p {
        color: #718096;
        margin: 6px 0 0;
        font-size: 13px;
    }

    .shipment-panel {
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, 0.04);
    }

    .shipment-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .shipment-filters input,
    .shipment-filters select {
        min-height: 42px;
        padding: 9px 12px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        background: #fff;
        color: #243b53;
        font-size: 13px;
    }

    .shipment-filters input {
        flex: 1;
        min-width: 190px;
    }

    .shipment-filters button {
        padding: 10px 18px;
        border: 0;
        border-radius: 8px;
        background: #1769e0;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .shipment-filters a {
        display: inline-flex;
        align-items: center;
        padding: 10px 14px;
        color: #52647a;
        text-decoration: none;
        font-size: 13px;
    }

    .shipment-table-wrapper {
        overflow-x: auto;
    }

    .shipment-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .shipment-table th {
        padding: 13px 12px;
        text-align: left;
        background: #f5f8fc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid #e7edf5;
    }

    .shipment-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #edf1f6;
        font-size: 13px;
        color: #334155;
    }

    .shipment-number {
        color: #1769e0;
        font-weight: 700;
    }

    .shipment-route {
        font-weight: 600;
        color: #243b53;
    }

    .shipment-muted {
        display: block;
        color: #8492a6;
        font-size: 11px;
        margin-top: 4px;
    }

    .shipment-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        background: #edf3ff;
        color: #245fc2;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .shipment-empty {
        text-align: center;
        padding: 55px 20px;
        color: #718096;
    }

    .shipment-empty strong {
        display: block;
        color: #334155;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .shipment-pagination {
        padding-top: 18px;
    }

    @media (max-width: 600px) {
        .shipment-panel {
            padding: 13px;
        }

        .shipment-heading h2 {
            font-size: 20px;
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
                                <span class="shipment-number">
                                    {{ $shipment->shipment_number }}
                                </span>
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
                                {{ $shipment->estimated_arrival_at
                                    ? $shipment->estimated_arrival_at->format('d M Y H:i')
                                    : $shipment->expected_arrival_at->format('d M Y H:i') }}
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
