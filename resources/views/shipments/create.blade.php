@extends('layouts.app')

@section('title', 'Tambah Shipment')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}" class="sidebar-link">
        <span>Shipment List</span>
    </a>
@endsection

@section('content')
<style>
    .ct-create {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        color: #172033;
    }

    .ct-create-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
    }

    .ct-create-heading h1 {
        margin: 0 0 7px;
        color: #172b4d;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .ct-create-heading p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .ct-create-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        background: #fff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease;
    }

    .ct-create-back:hover {
        background: #eff6ff;
    }

    .ct-create-intro {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        margin-bottom: 18px;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        background: #eff6ff;
    }

    .ct-create-intro-icon {
        display: grid;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        place-items: center;
        border-radius: 10px;
        background: #dbeafe;
        color: #2563eb;
        font-size: 20px;
        font-weight: 800;
    }

    .ct-create-intro strong {
        display: block;
        margin-bottom: 4px;
        color: #1e3a5f;
        font-size: 13px;
    }

    .ct-create-intro p {
        margin: 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .ct-create-alert {
        padding: 14px 17px;
        margin-bottom: 18px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 12px;
    }

    .ct-create-alert strong {
        display: block;
        margin-bottom: 7px;
    }

    .ct-create-alert ul {
        margin: 0;
        padding-left: 20px;
        line-height: 1.8;
    }

    .ct-create-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ct-create-card {
        padding: 22px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
    }

    .ct-create-section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 18px;
        border-bottom: 1px solid #edf0f5;
    }

    .ct-create-section-number {
        display: grid;
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #dbeafe;
        border-radius: 9px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
    }

    .ct-create-section-heading h2 {
        margin: 0 0 4px;
        color: #172b4d;
        font-size: 14px;
        font-weight: 750;
    }

    .ct-create-section-heading p {
        margin: 0;
        color: #94a3b8;
        font-size: 10px;
    }

    .ct-create-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .ct-create-field {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 8px;
    }

    .ct-create-field.full-width {
        grid-column: 1 / -1;
    }

    .ct-create-field label {
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    .ct-create-required {
        color: #dc2626;
    }

    .ct-create-control {
        box-sizing: border-box;
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #dce3ed;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #334155;
        font-family: inherit;
        font-size: 12px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .ct-create-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09);
    }

    .ct-create-control::placeholder {
        color: #a0aabc;
    }

    .ct-create-hint {
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.5;
    }

    .ct-create-error {
        color: #dc2626;
        font-size: 10px;
        line-height: 1.5;
    }

    .ct-create-route-note {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 12px;
        margin-top: 17px;
        border: 1px solid #e0f2fe;
        border-radius: 8px;
        background: #f0f9ff;
        color: #475569;
        font-size: 10px;
        line-height: 1.6;
    }

    .ct-create-route-note strong {
        color: #0369a1;
    }

    .ct-create-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 3px;
    }

    .ct-create-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 18px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease;
    }

    .ct-create-button-secondary {
        border-color: #dce3ed;
        background: #fff;
        color: #475569;
    }

    .ct-create-button-secondary:hover {
        background: #f8fafc;
    }

    .ct-create-button-primary {
        background: #2563eb;
        color: #fff;
    }

    .ct-create-button-primary:hover {
        background: #1d4ed8;
    }

    @media (max-width: 700px) {
        .ct-create-heading h1 {
            font-size: 21px;
        }

        .ct-create-grid {
            grid-template-columns: 1fr;
        }

        .ct-create-field.full-width {
            grid-column: auto;
        }

        .ct-create-card {
            padding: 16px;
        }

        .ct-create-actions {
            flex-direction: column-reverse;
        }

        .ct-create-button {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-create-back,
        .ct-create-control,
        .ct-create-button {
            transition: none;
        }
    }
</style>

<div class="ct-create">

    <div class="ct-create-heading">
        <div>
            <h1>Buat Shipment Baru</h1>
            <p>Lengkapi informasi pengiriman untuk mendaftarkan shipment ke CargoTrace.</p>
        </div>

        <a href="{{ route('shipments.index') }}" class="ct-create-back">
            <span>&larr;</span>
            Kembali ke Shipment List
        </a>
    </div>

    <div class="ct-create-intro">
        <div class="ct-create-intro-icon">+</div>
        <div>
            <strong>Registrasi Pengiriman</strong>
            <p>Isi setiap bagian formulir dengan informasi yang benar. Nomor shipment akan dibuat otomatis oleh sistem setelah data berhasil disimpan.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="ct-create-alert">
            <strong>Data belum dapat disimpan. Periksa kembali informasi berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shipments.store') }}" method="POST" class="ct-create-form">
        @csrf

        <!-- Informasi Utama -->
        <section class="ct-create-card">
            <div class="ct-create-section-heading">
                <div class="ct-create-section-number">01</div>
                <div>
                    <h2>Informasi Utama</h2>
                    <p>Identitas customer, carrier, dan jenis pengiriman.</p>
                </div>
            </div>

            <div class="ct-create-grid">

                <div class="ct-create-field">
                    <label for="customer_id">
                        Customer <span class="ct-create-required">*</span>
                    </label>

                    <select name="customer_id" id="customer_id" class="ct-create-control" required>
                        <option value="">-- Pilih Customer --</option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('customer_id')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-create-field">
                    <label for="carrier_id">
                        Carrier <span class="ct-create-required">*</span>
                    </label>

                    <select name="carrier_id" id="carrier_id" class="ct-create-control" required>
                        <option value="">-- Pilih Carrier --</option>

                        @foreach ($carriers as $carrier)
                            <option value="{{ $carrier->id }}"
                                {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('carrier_id')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-create-field full-width">
                    <label for="cargo_type">
                        Jenis Cargo <span class="ct-create-required">*</span>
                    </label>

                    <input
                        type="text"
                        name="cargo_type"
                        id="cargo_type"
                        class="ct-create-control"
                        placeholder="Contoh: Electronic Equipment"
                        value="{{ old('cargo_type') }}"
                        maxlength="100"
                        required
                    >

                    @error('cargo_type')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        <!-- Rute Pengiriman -->
        <section class="ct-create-card">
            <div class="ct-create-section-heading">
                <div class="ct-create-section-number">02</div>
                <div>
                    <h2>Rute Pengiriman</h2>
                    <p>Tentukan lokasi awal dan tujuan shipment.</p>
                </div>
            </div>

            <div class="ct-create-grid">

                <div class="ct-create-field">
                    <label for="origin_location_id">
                        Lokasi Asal <span class="ct-create-required">*</span>
                    </label>

                    <select name="origin_location_id" id="origin_location_id" class="ct-create-control" required>
                        <option value="">-- Pilih Lokasi Asal --</option>

                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ old('origin_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>

                    @error('origin_location_id')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-create-field">
                    <label for="destination_location_id">
                        Lokasi Tujuan <span class="ct-create-required">*</span>
                    </label>

                    <select name="destination_location_id" id="destination_location_id" class="ct-create-control" required>
                        <option value="">-- Pilih Lokasi Tujuan --</option>

                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ old('destination_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>

                    @error('destination_location_id')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="ct-create-route-note">
                <strong>Catatan:</strong>
                Lokasi asal dan tujuan harus berbeda. Pilih lokasi yang sesuai dengan rute pengiriman.
            </div>
        </section>

        <!-- Detail Cargo -->
        <section class="ct-create-card">
            <div class="ct-create-section-heading">
                <div class="ct-create-section-number">03</div>
                <div>
                    <h2>Detail Cargo</h2>
                    <p>Masukkan berat dan volume total barang yang dikirim.</p>
                </div>
            </div>

            <div class="ct-create-grid">

                <div class="ct-create-field">
                    <label for="total_weight_kg">Total Berat (KG)</label>

                    <input
                        type="number"
                        name="total_weight_kg"
                        id="total_weight_kg"
                        class="ct-create-control"
                        placeholder="Contoh: 150"
                        value="{{ old('total_weight_kg') }}"
                        min="0.001"
                        step="0.001"
                    >

                    <span class="ct-create-hint">Opsional. Masukkan berat dalam kilogram.</span>

                    @error('total_weight_kg')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-create-field">
                    <label for="total_volume_m3">Total Volume (M³)</label>

                    <input
                        type="number"
                        name="total_volume_m3"
                        id="total_volume_m3"
                        class="ct-create-control"
                        placeholder="Contoh: 2.5"
                        value="{{ old('total_volume_m3') }}"
                        min="0.001"
                        step="0.001"
                    >

                    <span class="ct-create-hint">Opsional. Masukkan volume dalam meter kubik.</span>

                    @error('total_volume_m3')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        <!-- Jadwal Pengiriman -->
        <section class="ct-create-card">
            <div class="ct-create-section-heading">
                <div class="ct-create-section-number">04</div>
                <div>
                    <h2>Jadwal Pengiriman</h2>
                    <p>Tentukan estimasi waktu keberangkatan dan kedatangan.</p>
                </div>
            </div>

            <div class="ct-create-grid">

                <div class="ct-create-field">
                    <label for="expected_departure_at">
                        Jadwal Keberangkatan <span class="ct-create-required">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        name="expected_departure_at"
                        id="expected_departure_at"
                        class="ct-create-control"
                        value="{{ old('expected_departure_at') }}"
                        required
                    >

                    @error('expected_departure_at')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ct-create-field">
                    <label for="expected_arrival_at">
                        Jadwal Kedatangan <span class="ct-create-required">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        name="expected_arrival_at"
                        id="expected_arrival_at"
                        class="ct-create-control"
                        value="{{ old('expected_arrival_at') }}"
                        required
                    >

                    @error('expected_arrival_at')
                        <span class="ct-create-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        <div class="ct-create-actions">
            <a href="{{ route('shipments.index') }}" class="ct-create-button ct-create-button-secondary">
                Batal
            </a>

            <button type="submit" class="ct-create-button ct-create-button-primary">
                Simpan Shipment
                <span>&rarr;</span>
            </button>
        </div>

    </form>
</div>
@endsection