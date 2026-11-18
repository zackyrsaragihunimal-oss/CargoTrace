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
    .exception-page { color: #172b4d; }

    .exception-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .exception-heading h2 {
        font-size: 23px;
        margin-bottom: 6px;
    }

    .exception-heading p {
        color: #718096;
        font-size: 13px;
    }

    .exception-panel {
        background: white;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, .04);
    }

    .exception-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .exception-filters select,
    .exception-form select,
    .exception-form textarea {
        min-height: 40px;
        padding: 9px 11px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        background: white;
        color: #243b53;
        font-size: 13px;
        width: 100%;
    }

    .exception-filters select {
        width: auto;
        min-width: 160px;
    }

    .exception-filters button,
    .exception-btn {
        border: 0;
        border-radius: 8px;
        padding: 10px 15px;
        background: #1769e0;
        color: white;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .exception-filters a {
        display: inline-flex;
        align-items: center;
        padding: 9px;
        color: #64748b;
        text-decoration: none;
        font-size: 13px;
    }

    .exception-table-wrap { overflow-x: auto; }

    .exception-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .exception-table th {
        padding: 13px 12px;
        background: #f5f8fc;
        color: #64748b;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 1px solid #e7edf5;
    }

    .exception-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #edf1f6;
        vertical-align: top;
        font-size: 13px;
        color: #334155;
    }

    .exception-code {
        color: #1769e0;
        font-weight: 700;
    }

    .exception-muted {
        display: block;
        color: #8492a6;
        font-size: 11px;
        margin-top: 5px;
    }

    .exception-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .severity-low { background: #dcfce7; color: #166534; }
    .severity-medium { background: #fef3c7; color: #92400e; }
    .severity-high { background: #ffedd5; color: #c2410c; }
    .severity-critical { background: #fee2e2; color: #b91c1c; }

    .status-open { background: #dbeafe; color: #1d4ed8; }
    .status-in-progress { background: #fef3c7; color: #92400e; }
    .status-resolved { background: #dcfce7; color: #166534; }

    .exception-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 175px;
    }

    .exception-form label {
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
    }

    .exception-form textarea {
        min-height: 65px;
        resize: vertical;
    }

    .exception-form .exception-btn { align-self: flex-start; }

    .exception-success {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 8px;
        background: #dcfce7;
        color: #166534;
        font-size: 13px;
    }

    .exception-error {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 8px;
        background: #fee2e2;
        color: #991b1b;
        font-size: 13px;
    }

    .exception-empty {
        padding: 45px 20px;
        text-align: center;
        color: #718096;
    }

    .exception-empty strong {
        display: block;
        color: #334155;
        margin-bottom: 7px;
        font-size: 16px;
    }

    .exception-pagination { padding-top: 18px; }

    @media (max-width: 600px) {
        .exception-panel { padding: 13px; }
        .exception-heading h2 { font-size: 20px; }
        .exception-filters select { flex: 1; }
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