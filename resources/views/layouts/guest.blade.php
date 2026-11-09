<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CargoTrace') | CargoTrace</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #172033;
            min-height: 100vh;
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            height: 72px;
            background: #10233f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8%;
        }

        .brand {
            font-size: 23px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .brand span {
            color: #38bdf8;
        }

        .nav-caption {
            color: #cbd5e1;
            font-size: 13px;
        }

        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 45px 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: white;
            padding: 36px;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
        }

        .auth-heading {
            margin-bottom: 25px;
        }

        .auth-heading h1 {
            font-size: 25px;
            margin-bottom: 9px;
            color: #10233f;
        }

        .auth-heading p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #0369a1;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #075985;
        }

        .auth-footer {
            text-align: center;
            margin-top: 23px;
            font-size: 13px;
            color: #64748b;
        }

        .auth-footer a {
            color: #0369a1;
            text-decoration: none;
            font-weight: 600;
        }

        .alert-error {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
        }

        .field-error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 6px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .bottom {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 500px) {
            .navbar {
                padding: 0 20px;
            }

            .auth-card {
                padding: 25px 20px;
            }

            .nav-caption {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <header class="navbar">
            <div class="brand">CARGO<span>TRACE</span></div>
            <div class="nav-caption">Logistics Management System</div>
        </header>

        <main class="main">
            <div class="auth-container">
                @yield('content')
            </div>
        </main>

        <footer class="bottom">
            &copy; {{ date('Y') }} CargoTrace. All rights reserved.
        </footer>

    </div>
</body>
</html>
