<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f6f8fb">

    <title>@yield('title', 'Authentication') | CargoTrace</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: Inter, Arial, Helvetica, sans-serif;
            background: #f6f8fb;
            color: #172033;
        }

        .guest-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(440px, .9fr);
        }

        .guest-visual {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            overflow: hidden;
            padding: 42px 8%;
            background: linear-gradient(145deg, #10233f 0%, #172f54 55%, #1e4777 100%);
            color: #fff;
        }

        .guest-visual::before,
        .guest-visual::after {
            position: absolute;
            content: "";
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 50%;
            pointer-events: none;
        }

        .guest-visual::before {
            width: 470px;
            height: 470px;
            right: -180px;
            top: 12%;
        }

        .guest-visual::after {
            width: 620px;
            height: 620px;
            right: -255px;
            top: 3%;
        }

        .guest-brand {
            position: relative;
            z-index: 1;
            font-size: 23px;
            font-weight: 850;
            letter-spacing: .7px;
        }

        .guest-brand span {
            color: #60a5fa;
        }

        .guest-visual-content {
            position: relative;
            z-index: 1;
            max-width: 540px;
            margin: 35px auto;
            text-align: center;
        }

        .guest-visual-content h1 {
            margin-bottom: 14px;
            font-size: clamp(27px, 3vw, 39px);
            line-height: 1.2;
            font-weight: 850;
            letter-spacing: -.8px;
        }

        .guest-visual-content p {
            max-width: 440px;
            margin: 0 auto;
            color: #cbd5e1;
            font-size: 13px;
            line-height: 1.8;
        }

        .guest-mascot {
            display: block;
            width: min(100%, 340px);
            max-height: 350px;
            object-fit: contain;
            margin: 20px auto 0;
        }

        .guest-visual-footer {
            position: relative;
            z-index: 1;
            color: #a9bad0;
            font-size: 11px;
        }

        .guest-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 48px 35px;
            background: #f6f8fb;
        }

        .guest-form-top {
            display: none;
            width: 100%;
            max-width: 460px;
            margin-bottom: 30px;
            color: #172b4d;
            font-size: 21px;
            font-weight: 850;
        }

        .guest-form-top span {
            color: #2563eb;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            padding: 35px;
            border: 1px solid #e5eaf1;
            border-radius: 14px;
            background: #fff;
        }

        .auth-heading {
            margin-bottom: 27px;
        }

        .auth-heading h1 {
            margin-bottom: 9px;
            color: #172b4d;
            font-size: 25px;
            font-weight: 850;
            letter-spacing: -.5px;
        }

        .auth-heading p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.8;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 11px;
            font-weight: 750;
        }

        .form-control {
            display: block;
            width: 100%;
            min-height: 43px;
            padding: 11px 13px;
            border: 1px solid #dbe3ed;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #172033;
            font-family: inherit;
            font-size: 12px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control::placeholder {
            color: #a0acbc;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        }

        .btn-primary {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 12px 16px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #64748b;
            font-size: 11px;
        }

        .remember input {
            accent-color: #2563eb;
        }

        .auth-footer {
            margin-top: 23px;
            color: #64748b;
            font-size: 11px;
            text-align: center;
        }

        .auth-footer a {
            color: #2563eb;
            font-weight: 750;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-error {
            padding: 13px 15px;
            margin-bottom: 18px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 11px;
            line-height: 1.7;
        }

        .field-error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 10px;
        }

        .guest-bottom {
            margin-top: 28px;
            color: #94a3b8;
            font-size: 10px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .guest-page {
                grid-template-columns: minmax(0, 1fr) minmax(380px, .9fr);
            }

            .guest-visual {
                padding: 35px 7%;
            }

            .guest-mascot {
                max-height: 280px;
            }

            .guest-form-panel {
                padding: 35px 24px;
            }

            .auth-card {
                padding: 28px 23px;
            }
        }

        @media (max-width: 680px) {
            .guest-page {
                display: block;
            }

            .guest-visual {
                display: none;
            }

            .guest-form-panel {
                min-height: 100vh;
                padding: 35px 20px;
            }

            .guest-form-top {
                display: block;
            }

            .auth-card {
                padding: 27px 22px;
            }
        }

        @media (max-width: 380px) {
            .guest-form-panel {
                padding: 24px 14px;
            }

            .auth-card {
                padding: 23px 17px;
            }

            .auth-heading h1 {
                font-size: 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="guest-page">

        <aside class="guest-visual">
            <div class="guest-brand">CARGO<span>TRACE</span></div>

            <div class="guest-visual-content">
                <h1>Smarter Logistics.<br>Clearer Tracking.</h1>

                <p>
                    Kelola pengiriman, pantau perjalanan barang,
                    dan dapatkan informasi terbaru dalam satu platform.
                </p>

                <img
                    src="{{ asset('images/jek-hero.png') }}"
                    alt="JEK - CargoTrace Mascot"
                    class="guest-mascot"
                >
            </div>

            <div class="guest-visual-footer">
                Logistics Management System
            </div>
        </aside>

        <main class="guest-form-panel">
            <div class="guest-form-top">CARGO<span>TRACE</span></div>

            <div class="auth-container">
                @yield('content')
            </div>

            <footer class="guest-bottom">
                &copy; {{ date('Y') }} CargoTrace. All rights reserved.
            </footer>
        </main>

    </div>
</body>
</html>