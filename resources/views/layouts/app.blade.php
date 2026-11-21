<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | CargoTrace</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
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

                @if ($unreadNotificationCount > 0)
                    <span style="background:#ef4444; color:white; border-radius:20px; padding:3px 8px; font-size:10px; font-weight:700;">
                        {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                    </span>
                @endif
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
</body>
</html>