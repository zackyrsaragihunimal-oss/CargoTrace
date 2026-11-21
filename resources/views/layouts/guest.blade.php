<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F3F7F4">

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
            background: #F3F7F4;
            color: #142B45;
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
            background: linear-gradient(145deg, #142B45 0%, #1D3D52 55%, #287A57 100%);
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
            color: #78C7A0;
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
            color: #D4E1DA;
            font-size: 13px;
            line-height: 1.8;
        }

        .guest-mascot {
            display: block;
            width: min(100%, 380px);
            max-height: 390px;
            object-fit: contain;
            object-position: center;
            margin: 20px auto 0;
            background: transparent;
            filter: drop-shadow(0 12px 18px rgba(0,0,0,.16));
        }

        .guest-visual-footer {
            position: relative;
            z-index: 1;
            color: #C2D4CA;
            font-size: 11px;
        }

        .guest-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 48px 35px;
            background: #F3F7F4;
        }

        .guest-form-top {
            display: none;
            width: 100%;
            max-width: 460px;
            margin-bottom: 30px;
            color: #142B45;
            font-size: 21px;
            font-weight: 850;
        }

        .guest-form-top span {
            color: #287A57;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            padding: 35px;
            border: 1px solid #E1EAE4;
            border-radius: 14px;
            background: #fff;
        }

        .auth-heading {
            margin-bottom: 27px;
        }

        .auth-heading h1 {
            margin-bottom: 9px;
            color: #142B45;
            font-size: 25px;
            font-weight: 850;
            letter-spacing: -.5px;
        }

        .auth-heading p {
            color: #64766C;
            font-size: 12px;
            line-height: 1.8;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334B40;
            font-size: 11px;
            font-weight: 750;
        }

        .form-control {
            display: block;
            width: 100%;
            min-height: 43px;
            padding: 11px 13px;
            border: 1px solid #CBD9D0;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #142B45;
            font-family: inherit;
            font-size: 12px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control::placeholder {
            color: #91A198;
        }

        .form-control:focus {
            border-color: #4D9478;
            box-shadow: 0 0 0 3px rgba(40,122,87,.14);
        }

        .btn-primary {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 12px 16px;
            border: 0;
            border-radius: 8px;
            background: #287A57;
            color: #fff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-primary:hover {
            background: #1D6245;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #64766C;
            font-size: 11px;
        }

        .remember input {
            accent-color: #287A57;
        }

        .auth-footer {
            margin-top: 23px;
            color: #64766C;
            font-size: 11px;
            text-align: center;
        }

        .auth-footer a {
            color: #287A57;
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
            color: #91A198;
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

        /* ===== CARGOTRACE PREMIUM AUTHENTICATION ===== */

        .guest-visual {
            background:
                radial-gradient(circle at 15% 85%, rgba(78,163,119,.22), transparent 36%),
                linear-gradient(145deg, #10283F 0%, #173D4A 55%, #287A57 100%);
        }

        .guest-visual-content h1 {
            font-size: clamp(30px, 3.2vw, 42px);
            line-height: 1.18;
            letter-spacing: -1.2px;
        }

        .guest-mascot {
            filter: drop-shadow(0 16px 24px rgba(0,0,0,.20));
            transition: transform .3s ease, filter .3s ease;
        }

        .guest-mascot:hover {
            transform: translateY(-4px);
            filter: drop-shadow(0 20px 26px rgba(0,0,0,.24));
        }

        .guest-form-panel {
            background:
                radial-gradient(circle at 95% 5%, rgba(40,122,87,.07), transparent 32%),
                #F3F7F4;
        }

        .auth-card {
            padding: 40px;
            border: 1px solid rgba(203,217,208,.85);
            border-radius: 20px;
            background: rgba(255,255,255,.97);
            box-shadow: 0 18px 55px rgba(20,43,69,.075);
            transition: box-shadow .25s ease;
        }

        .auth-heading {
            margin-bottom: 30px;
        }

        .auth-heading h1 {
            font-size: clamp(25px, 2.4vw, 30px);
            line-height: 1.25;
            letter-spacing: -.8px;
        }

        .auth-heading p {
            font-size: 13px;
            line-height: 1.75;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            margin-bottom: 9px;
            font-size: 12px;
        }

        .form-control {
            min-height: 48px;
            padding: 12px 15px;
            border: 1px solid #D5E1D9;
            border-radius: 11px;
            font-size: 13px;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .form-control:hover {
            border-color: #91B6A0;
        }

        .form-control:focus {
            border-color: #287A57;
            background: #FCFEFC;
            box-shadow: 0 0 0 4px rgba(40,122,87,.12);
        }

        .btn-primary {
            min-height: 49px;
            border-radius: 11px;
            background: linear-gradient(110deg, #287A57, #318A64);
            box-shadow: 0 7px 16px rgba(40,122,87,.16);
            font-size: 13px;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(110deg, #1D6245, #287A57);
            box-shadow: 0 10px 20px rgba(40,122,87,.22);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .remember,
        .auth-footer {
            font-size: 12px;
        }

        .auth-footer a {
            transition: color .2s ease;
        }

        .auth-footer a:hover {
            color: #1D6245;
        }

        .alert-error {
            border-radius: 11px;
        }

        @media (max-width: 900px) {
            .auth-card {
                padding: 30px 24px;
            }
        }

        @media (max-width: 680px) {
            .auth-card {
                border-radius: 17px;
                box-shadow: 0 12px 35px rgba(20,43,69,.07);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .guest-mascot,
            .btn-primary,
            .form-control,
            .auth-footer a {
                transition: none !important;
            }
        }

        /* ===== END PREMIUM AUTHENTICATION ===== */
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
                    src="{{ asset('images/CargoTrace_JEK_Mascot_Pack/01_General_Authentication/jek-welcome.png') }}"
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