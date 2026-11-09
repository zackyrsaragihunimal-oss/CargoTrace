
@extends('layouts.guest')

@section('title', 'Registrasi')

@section('content')

<div class="auth-card">
    <div class="auth-heading">
        <h1>Create Your Account</h1>
        <p>Daftarkan akun CargoTrace untuk memantau status dan informasi pengiriman barang Anda.</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name') }}"
                placeholder="Nama lengkap"
                autocomplete="name"
                maxlength="150"
                required
            >
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                class="form-control"
                value="{{ old('phone') }}"
                placeholder="08xxxxxxxxxx"
                autocomplete="tel"
                maxlength="25"
            >
            @error('phone')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                placeholder="Ulangi password"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit" class="btn-primary">
            Create CargoTrace Account
        </button>
    </form>

    <div class="auth-footer">
        Sudah memiliki akun?
        <a href="{{ route('login') }}">Login sekarang</a>
    </div>
</div>

@endsection
