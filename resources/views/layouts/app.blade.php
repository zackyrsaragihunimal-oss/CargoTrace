<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.dashboard')) | CargoTrace</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    data-notification-user="{{ auth()->id() }}"
    data-notification-locale="{{ app()->getLocale() }}"
    data-notification-unread-count="{{ __('notifications.javascript.unread_count') }}"
    data-notification-unread="{{ __('notifications.javascript.unread') }}"
    data-notification-view-shipment="{{ __('notifications.javascript.view_shipment') }}"
    data-notification-mark-read="{{ __('notifications.javascript.mark_read') }}"
    data-notification-new-notification="{{ __('notifications.javascript.new_notification') }}"
    data-notification-shipment="{{ __('notifications.shipment') }}"
>
<div class="app-layout">
    <aside class="sidebar" id="appSidebar">
        <div class="brand">CARGO<span>TRACE</span></div>

        <div class="menu-label">{{ __('app.main_menu') }}</div>

        <nav class="menu" aria-label="{{ __('app.main_menu') }}">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                {{ __('app.dashboard') }}
            </a>

            @php
                $unreadNotificationCount = auth()->user()
                    ->notifications()
                    ->whereNull('read_at')
                    ->count();
            @endphp

            <a href="{{ route('notifications.index') }}"
               class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <span>{{ __('app.notifications') }}</span>
                <span
                    data-notification-count
                    @if ($unreadNotificationCount === 0) hidden @endif
                    style="display:{{ $unreadNotificationCount > 0 ? 'inline-flex' : 'none' }}; background:#ef4444; color:white; border-radius:20px; padding:3px 8px; font-size:10px; font-weight:700;"
                >{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
            </a>

            @yield('sidebar-menu')
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    {{ __('app.sign_out') }}
                </button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="{{ __('app.toggle_sidebar') }}"
                aria-controls="appSidebar"
                aria-expanded="true"
                title="{{ __('app.toggle_sidebar') }}"
            >
                <span aria-hidden="true">&#9776;</span>
            </button>

            <div class="topbar-title">
                <h2>@yield('header', __('app.dashboard'))</h2>
            </div>

            <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                <div class="language-switch" style="display:flex; align-items:center; gap:5px; padding:4px; border:1px solid #dbe5e1; border-radius:9px; background:#fff;">
                    @foreach (['id' => 'ID', 'en' => 'EN'] as $locale => $label)
                        <form method="POST" action="{{ route('language.update') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $locale }}">
                            <button
                                type="submit"
                                aria-label="{{ $locale === 'id' ? __('app.indonesian') : __('app.english') }}"
                                @if (app()->getLocale() === $locale) aria-current="true" @endif
                                style="min-width:36px; padding:6px 9px; border:0; border-radius:6px; cursor:pointer; font-size:11px; font-weight:800; background:{{ app()->getLocale() === $locale ? '#1556a8' : 'transparent' }}; color:{{ app()->getLocale() === $locale ? '#fff' : '#526477' }};"
                            >{{ $label }}</button>
                        </form>
                    @endforeach
                </div>

                <div class="user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    {{ ucfirst(auth()->user()->role) }}
                </div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>

        <footer class="app-footer">
            &copy; {{ date('Y') }} {{ __('app.footer') }}
        </footer>
    </div>
</div>
<div
    data-notification-toast
    role="status"
    aria-live="polite"
    style="display:none; position:fixed; right:24px; bottom:24px; z-index:9999; width:min(360px, calc(100% - 32px)); padding:16px 18px; border:1px solid #dbe5f1; border-left:4px solid #10b981; border-radius:12px; background:#ffffff; color:#172033; box-shadow:0 12px 35px rgba(15,23,42,.16);"
>
    <strong data-notification-toast-title style="display:block; margin-bottom:6px; font-size:13px;"></strong>
    <span data-notification-toast-message style="font-size:12px; line-height:1.6; color:#64748b;"></span>
</div>
</body>
</html>