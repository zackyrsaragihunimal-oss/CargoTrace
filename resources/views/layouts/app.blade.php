<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | CargoTrace</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #10233f;
            color: white;
            padding: 25px 18px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            padding: 5px 12px 30px;
        }

        .brand span { color: #38bdf8; }

        .menu-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 12px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu a {
            display: block;
            padding: 12px;
            border-radius: 7px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #1e3a5f;
            color: white;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            min-height: 72px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .topbar h2 {
            font-size: 18px;
            color: #10233f;
        }

        .user-info {
            font-size: 13px;
            color: #64748b;
            text-align: right;
        }

        .user-info strong {
            display: block;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .content {
            padding: 32px;
            flex: 1;
        }

        .page-heading { margin-bottom: 25px; }

        .page-heading h1 {
            font-size: 25px;
            color: #10233f;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #64748b;
            font-size: 14px;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.03);
        }

        .btn-logout {
            width: 100%;
            padding: 11px;
            border: 1px solid #475569;
            border-radius: 7px;
            background: transparent;
            color: #e2e8f0;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-logout:hover { background: #1e3a5f; }

        .app-footer {
            padding: 18px 32px;
            color: #94a3b8;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }

        @media (max-width: 760px) {
            .sidebar { width: 190px; padding: 20px 12px; }
            .topbar { padding: 0 18px; }
            .content { padding: 22px 16px; }
        }

        @media (max-width: 520px) {
            .app-layout { flex-direction: column; }
            .sidebar { width: 100%; padding: 15px; }
            .brand { padding: 5px 8px 15px; }
            .menu { flex-direction: row; flex-wrap: wrap; }
            .menu a { padding: 9px; font-size: 12px; }
            .sidebar-bottom { margin-top: 15px; padding-top: 10px; }
            .btn-logout { width: auto; padding: 9px 15px; }
            .topbar { min-height: 65px; }
            .user-info { font-size: 11px; }
        }
    </style>
</head>

<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="brand">CARGO<span>TRACE</span></div>

        <div class="menu-label">MAIN MENU</div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
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
            <h2>@yield('header', 'Dashboard')</h2>

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
