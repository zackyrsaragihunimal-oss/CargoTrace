
@extends('layouts.app')

@section('title', 'Tambah Shipment')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="sidebar-link">
        <span>Shipment List</span>
    </a>
@endsection

@section('content')
<style>
    .create-shipment {
        max-width: 1100px;
        margin: 0 auto;
        color: #172554;
    }

    .page-heading {
        margin-bottom: 24px;
    }

    .page-heading h2 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 700;
        color: #172554;
    }

    .page-heading p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 28px;
        margin-bottom: 22px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
    }

    .form-section-title {
        font-size: 17px;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0 0 22px;
        padding-bottom: 13px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #1e293b;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
    }

    textarea.form-control {
        min-height: 90px;
        resize: vertical;
    }

    .form-hint {
        font-size: 12px;
        color: #64748b;
    }

    .field-error {
        font-size: 12px;
        color: #dc2626;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #334155;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .alert-error {
        padding: 14px 18px;
        margin-bottom: 20px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 13px;
    }

    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }

        .form-card {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="create-shipment">

    <div class="page-heading">
        <h2>Tambah Shipment</h2>
        <p>Masukkan informasi pengiriman baru ke dalam sistem CargoTrace.</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <strong>Data belum dapat disimpan.</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shipments.store') }}" method="POST">
        @csrf

        <!-- Informasi Utama -->
        <div class="form-card">
            <h3 class="form-section-title">Informasi Utama</h3>

            <div class="form-grid">

                <div class="form-group">
                    <label for="customer_id">
                        Customer <span class="required">*</span>
                    </label>

                    <select name="customer_id" id="customer_id" class="form-control" required>
                        <option value="">-- Pilih Customer --</option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('customer_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="carrier_id">
                        Carrier <span class="required">*</span>
                    </label>

                    <select name="carrier_id" id="carrier_id" class="form-control" required>
                        <option value="">-- Pilih Carrier --</option>

                        @foreach ($carriers as $carrier)
                            <option value="{{ $carrier->id }}"
                                {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('carrier_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="cargo_type">
                        Jenis Cargo <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="cargo_type"
                        id="cargo_type"
                        class="form-control"
                        placeholder="Contoh: Electronic Equipment"
                        value="{{ old('cargo_type') }}"
                        maxlength="100"
                        required
                    >

                    @error('cargo_type')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Rute Pengiriman -->
        <div class="form-card">
            <h3 class="form-section-title">Rute Pengiriman</h3>

            <div class="form-grid">

                <div class="form-group">
                    <label for="origin_location_id">
                        Lokasi Asal <span class="required">*</span>
                    </label>

                    <select name="origin_location_id" id="origin_location_id" class="form-control" required>
                        <option value="">-- Pilih Lokasi Asal --</option>

                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ old('origin_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>

                    @error('origin_location_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="destination_location_id">
                        Lokasi Tujuan <span class="required">*</span>
                    </label>

                    <select name="destination_location_id" id="destination_location_id" class="form-control" required>
                        <option value="">-- Pilih Lokasi Tujuan --</option>

                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ old('destination_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>

                    @error('destination_location_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Detail Cargo -->
        <div class="form-card">
            <h3 class="form-section-title">Detail Cargo</h3>

            <div class="form-grid">

                <div class="form-group">
                    <label for="total_weight_kg">Total Berat (KG)</label>

                    <input
                        type="number"
                        name="total_weight_kg"
                        id="total_weight_kg"
                        class="form-control"
                        placeholder="Contoh: 150"
                        value="{{ old('total_weight_kg') }}"
                        min="0.001"
                        step="0.001"
                    >

                    <span class="form-hint">Opsional. Masukkan berat dalam kilogram.</span>

                    @error('total_weight_kg')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="total_volume_m3">Total Volume (M³)</label>

                    <input
                        type="number"
                        name="total_volume_m3"
                        id="total_volume_m3"
                        class="form-control"
                        placeholder="Contoh: 2.5"
                        value="{{ old('total_volume_m3') }}"
                        min="0.001"
                        step="0.001"
                    >

                    <span class="form-hint">Opsional. Masukkan volume dalam meter kubik.</span>

                    @error('total_volume_m3')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Jadwal Pengiriman -->
        <div class="form-card">
            <h3 class="form-section-title">Jadwal Pengiriman</h3>

            <div class="form-grid">

                <div class="form-group">
                    <label for="expected_departure_at">
                        Jadwal Keberangkatan <span class="required">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        name="expected_departure_at"
                        id="expected_departure_at"
                        class="form-control"
                        value="{{ old('expected_departure_at') }}"
                        required
                    >

                    @error('expected_departure_at')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="expected_arrival_at">
                        Jadwal Kedatangan <span class="required">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        name="expected_arrival_at"
                        id="expected_arrival_at"
                        class="form-control"
                        value="{{ old('expected_arrival_at') }}"
                        required
                    >

                    @error('expected_arrival_at')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="form-actions">
            <a href="{{ route('shipments.index') }}" class="btn btn-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                Simpan Shipment
            </button>
        </div>

    </form>
</div>
@endsection
