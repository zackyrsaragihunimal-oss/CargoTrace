@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')

<div class="auth-card">

    <div class="auth-heading">
        <h1>{{ __('auth.login_title') }}</h1>
        <p>{{ __('auth.login_description') }}</p>
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
            <label for="email">{{ __('auth.email') }}</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="{{ __('auth.email_placeholder') }}"
                autocomplete="username"
                required
                autofocus
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('auth.password') }}</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="{{ __('auth.password_placeholder') }}"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            {{ __('auth.remember_me') }}
        </label>

        <button type="submit" class="btn-primary">
            {{ __('auth.login_button') }}
        </button>
    </form>

    <div class="auth-footer">
        {{ __('auth.no_account') }}
        <a href="{{ route('register') }}">{{ __('auth.register_link') }}</a>
    </div>

</div>

@endsection