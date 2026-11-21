@extends('layouts.app')

@section('title', 'Exception Management')
@section('header', 'Exception Management')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        Shipments
    </a>

    <a href="{{ route('exceptions.index') }}"
       class="{{ request()->routeIs('exceptions.*') ? 'active' : '' }}">
        Exceptions
    </a>
@endsection

@section('content')

<style>
    .exception-page {
        width: 100%;
        color: #172033;
    }

    .exception-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
    }

    .exception-heading h2 {
        margin: 0 0 7px;
        color: #172b4d;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .exception-heading p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .exception-panel {
        padding: 22px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
    }

    .exception-filters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 1px solid #edf0f5;
    }

    .exception-filters select,
    .exception-form select,
    .exception-form textarea {
        box-sizing: border-box;
        width: 100%;
        min-height: 41px;
        padding: 10px 12px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #334155;
        font-family: inherit;
        font-size: 11px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .exception-filters select {
        width: auto;
        min-width: 165px;
    }

    .exception-filters select:focus,
    .exception-form select:focus,
    .exception-form textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09);
    }

    .exception-filters button,
    .exception-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 9px 15px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease;
    }

    .exception-filters button:hover,
    .exception-btn:hover {
        background: #1d4ed8;
    }

    .exception-filters a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 12px;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        background: #fff;
        color: #64748b;
        font-size: 11px;
        font-weight: 650;
        text-decoration: none;
    }

    .exception-filters a:hover {
        background: #f8fafc;
        color: #334155;
    }

    .exception-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #edf0f5;
        border-radius: 9px;
    }

    .exception-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .exception-table th {
        padding: 14px 13px;
        border-bottom: 1px solid #e5eaf1;
        background: #f8fafc;
        color: #64748b;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .45px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .exception-table td {
        padding: 15px 13px;
        border-bottom: 1px solid #edf0f5;
        vertical-align: top;
        color: #334155;
        font-size: 11px;
        line-height: 1.6;
    }

    .exception-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .exception-table tbody tr:hover td {
        background: #fbfdff;
    }

    .exception-code {
        color: #2563eb;
        font-weight: 750;
        text-decoration: none;
    }

    .exception-code:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .exception-muted {
        display: block;
        margin-top: 5px;
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.5;
    }

    .exception-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .2px;
        white-space: nowrap;
    }

    .severity-low {
        background: #dcfce7;
        color: #166534;
    }

    .severity-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .severity-high {
        background: #ffedd5;
        color: #c2410c;
    }

    .severity-critical {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-open {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-in-progress {
        background: #fef3c7;
        color: #92400e;
    }

    .status-resolved {
        background: #dcfce7;
        color: #166534;
    }

    .exception-form {
        display: flex;
        min-width: 175px;
        flex-direction: column;
        gap: 8px;
    }

    .exception-form label {
        margin-top: 3px;
        color: #475569;
        font-size: 10px;
        font-weight: 750;
    }

    .exception-form textarea {
        min-height: 75px;
        resize: vertical;
        line-height: 1.5;
    }

    .exception-form textarea::placeholder {
        color: #a0aabc;
    }

    .exception-form .exception-btn {
        align-self: flex-start;
    }

    .exception-success {
        padding: 13px 16px;
        margin-bottom: 16px;
        border: 1px solid #bbf7d0;
        border-radius: 9px;
        background: #f0fdf4;
        color: #166534;
        font-size: 11px;
        line-height: 1.6;
    }

    .exception-error {
        padding: 13px 16px;
        margin-bottom: 16px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 11px;
        line-height: 1.7;
    }

    .exception-error ul {
        margin: 7px 0 0 18px;
        padding: 0;
    }

    .exception-empty {
        padding: 48px 20px;
        color: #94a3b8;
        text-align: center;
        font-size: 11px;
        line-height: 1.7;
    }

    .exception-empty strong {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 15px;
        font-weight: 750;
    }

    .exception-pagination {
        padding-top: 18px;
    }

    .exception-pagination nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    @media (max-width: 700px) {
        .exception-heading h2 {
            font-size: 21px;
        }

        .exception-panel {
            padding: 15px;
        }

        .exception-filters {
            align-items: stretch;
        }

        .exception-filters select {
            flex: 1 1 100%;
            width: 100%;
        }

        .exception-filters button,
        .exception-filters a {
            flex: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .exception-filters select,
        .exception-form select,
        .exception-form textarea,
        .exception-filters button,
        .exception-btn,
        .exception-filters a {
            transition: none;
        }
    }
</style>

<div class="exception-page">

    <div class="exception-heading">
        <div>
            <h2>Daftar Kendala Pengiriman</h2>
            <p>Pantau, tugaskan, dan selesaikan kendala dalam proses pengiriman.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="exception-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="exception-error">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin: 7px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="exception-panel">

        <form method="GET"
              action="{{ route('exceptions.index') }}"
              class="exception-filters">

            <select name="status">
                <option value="">Semua Status</option>
                @foreach (['OPEN', 'IN_PROGRESS', 'RESOLVED'] as $exceptionStatus)
                    <option value="{{ $exceptionStatus }}"
                        {{ $status === $exceptionStatus ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $exceptionStatus) }}
                    </option>
                @endforeach
            </select>

            <select name="severity">
                <option value="">Semua Severity</option>
                @foreach (['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $level)
                    <option value="{{ $level }}"
                        {{ $severity === $level ? 'selected' : '' }}>
                        {{ $level }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Terapkan Filter</button>

            @if ($status || $severity)
                <a href="{{ route('exceptions.index') }}">Reset</a>
            @endif
        </form>

        <div class="exception-table-wrap">
            <table class="exception-table">
                <thead>
                    <tr>
                        <th>Exception</th>
                        <th>Shipment</th>
                        <th>Customer</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Detected At</th>
                        <th>Assigned Operator</th>
                        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($exceptions as $exception)
                        <tr>
                            <td>
                                <span class="exception-code">
                                    {{ $exception->exception_code }}
                                </span>
                                <span class="exception-muted">
                                    {{ $exception->title }}
                                </span>
                                @if ($exception->description)
                                    <span class="exception-muted">
                                        {{ $exception->description }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('shipments.show', $exception->shipment_id) }}"
                                   class="exception-code"
                                   style="text-decoration: none;">
                                    {{ $exception->shipment->shipment_number ?? 'Shipment #' . $exception->shipment_id }}
                                </a>
                            </td>

                            <td>
                                {{ $exception->shipment->customer->name ?? '-' }}
                            </td>

                            <td>
                                <span class="exception-badge severity-{{ strtolower($exception->severity) }}">
                                    {{ $exception->severity }}
                                </span>
                            </td>

                            <td>
                                <span class="exception-badge status-{{ strtolower(str_replace('_', '-', $exception->status)) }}">
                                    {{ str_replace('_', ' ', $exception->status) }}
                                </span>
                                @if ($exception->resolved_at)
                                    <span class="exception-muted">
                                        Resolved: {{ $exception->resolved_at->format('d M Y H:i') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $exception->detected_at?->format('d M Y H:i') ?? '-' }}
                                <span class="exception-muted">
                                    Reported by: {{ $exception->reporter->name ?? 'System' }}
                                </span>
                            </td>

                            <td>
                                {{ $exception->assignee->name ?? 'Belum ditugaskan' }}
                            </td>

                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                <td>
                                    @if (auth()->user()->isAdmin() && $exception->status !== 'RESOLVED')
                                        <form method="POST"
                                              action="{{ route('exceptions.assign', $exception) }}"
                                              class="exception-form"
                                              style="margin-bottom: 15px;">
                                            @csrf
                                            @method('PATCH')

                                            <label>Assign Operator</label>

                                            <select name="assigned_to" required>
                                                <option value="">Pilih Operator</option>
                                                @foreach ($operators as $operator)
                                                    <option value="{{ $operator->id }}"
                                                        {{ (int) $exception->assigned_to === (int) $operator->id ? 'selected' : '' }}>
                                                        {{ $operator->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="submit" class="exception-btn">
                                                Assign
                                            </button>
                                        </form>
                                    @endif

                                    @if (!auth()->user()->isCustomer() && $exception->status !== 'RESOLVED')
                                        <form method="POST"
                                              action="{{ route('exceptions.update', $exception) }}"
                                              class="exception-form">
                                            @csrf
                                            @method('PATCH')

                                            <label>Update Status</label>

                                            <select name="status" required>
                                                <option value="IN_PROGRESS"
                                                    {{ $exception->status === 'IN_PROGRESS' ? 'selected' : '' }}>
                                                    IN PROGRESS
                                                </option>
                                                <option value="RESOLVED">
                                                    RESOLVED
                                                </option>
                                            </select>

                                            <label>Resolution Notes</label>
                                            <textarea name="resolution_notes"
                                                      maxlength="2000"
                                                      placeholder="Isi catatan jika Exception diselesaikan..."></textarea>

                                            <button type="submit" class="exception-btn">
                                                Update
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() || auth()->user()->isOperator() ? 8 : 7 }}">
                                <div class="exception-empty">
                                    <strong>Belum Ada Exception</strong>
                                    Tidak ada kendala pengiriman yang sesuai dengan filter saat ini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="exception-pagination">
            {{ $exceptions->links() }}
        </div>

    </div>
</div>

@endsection