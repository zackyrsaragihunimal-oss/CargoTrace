@extends('layouts.guest')

@section('title', __('auth.register_title'))

@section('content')

<div class="auth-card">
    <div class="auth-heading">
        <h1>{{ __('auth.register_title') }}</h1>
        <p>{{ __('auth.register_description') }}</p>
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
            <label for="name">{{ __('auth.full_name') }}</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name') }}"
                placeholder="{{ __('auth.name_placeholder') }}"
                autocomplete="name"
                maxlength="150"
                required
            >
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="{{ __('auth.email_placeholder') }}"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">{{ __('auth.phone') }}</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                class="form-control"
                value="{{ old('phone') }}"
                placeholder="{{ __('auth.phone_placeholder') }}"
                autocomplete="tel"
                maxlength="25"
            >
            @error('phone')
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
                autocomplete="new-password"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{ __('auth.password_confirmation') }}</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                placeholder="{{ __('auth.password_confirmation_placeholder') }}"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit" class="btn-primary">
            {{ __('auth.register_button') }}
        </button>
    </form>

    <div class="auth-footer">
        {{ __('auth.have_account') }}
        <a href="{{ route('login') }}">{{ __('auth.login_link') }}</a>
    </div>
</div>

@endsection