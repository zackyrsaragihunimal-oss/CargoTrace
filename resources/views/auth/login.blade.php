@extends('layouts.guest')

@section('title', 'Login')

@section('content')

<div class="auth-card">

    <div class="auth-heading">
        <h1>Welcome Back</h1>
        <p>Masuk ke akun CargoTrace untuk mengelola dan memantau aktivitas pengiriman Anda.</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                autocomplete="username"
                required
                autofocus
            >
            @error('email')
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
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            Ingat saya
        </label>

        <button type="submit" class="btn-primary">
            Sign In to CargoTrace
        </button>
    </form>

    <div class="auth-footer">
        Belum memiliki akun?
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

</div>

@endsection
