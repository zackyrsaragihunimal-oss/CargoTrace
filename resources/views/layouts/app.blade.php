<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | CargoTrace</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-notification-user="{{ auth()->id() }}">
<div class="app-layout">
    <aside class="sidebar" id="appSidebar">
        <div class="brand">CARGO<span>TRACE</span></div>

        <div class="menu-label">MAIN MENU</div>

        <nav class="menu" aria-label="Main navigation">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            @php
                $unreadNotificationCount = auth()->user()
                    ->notifications()
                    ->whereNull('read_at')
                    ->count();
            @endphp

            <a href="{{ route('notifications.index') }}"
   class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
    <span>Notifications</span>
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
                <button type="submit" class="btn-logout">Sign Out</button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Toggle sidebar"
                aria-controls="appSidebar"
                aria-expanded="true"
                title="Show or hide navigation"
            >
                <span aria-hidden="true">☰</span>
            </button>

            <div class="topbar-title">
                <h2>@yield('header', 'Dashboard')</h2>
            </div>

            <div class="user-info">
                <strong>{{ auth()->user()->name }}</strong>
                {{ ucfirst(auth()->user()->role) }}
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>

        <footer class="app-footer">
            &copy; {{ date('Y') }} CargoTrace Logistics Management System.
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