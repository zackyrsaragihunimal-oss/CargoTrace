 @extends('layouts.app')

@section('title', 'Operator Dashboard')
@section('header', 'Logistics Operator Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#shipments">Shipment Operations</a>
    <a href="#tracking">Tracking Updates</a>
    <a href="#exceptions">Shipment Exceptions</a>
@endsection

@section('content')

<style>
    .ct-op {
        color: #142b4e;
    }

    .ct-op * {
        box-sizing: border-box;
    }

    .ct-op-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        min-height: 290px;
        padding: 38px;
        overflow: hidden;
        border-radius: 25px;
        color: #fff;
        background: linear-gradient(115deg, #102e59, #1556a5 68%, #2588e8);
        box-shadow: 0 15px 35px rgba(20, 60, 110, .15);
    }

    .ct-op-hero::before,
    .ct-op-hero::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        pointer-events: none;
    }

    .ct-op-hero::before {
        width: 320px;
        height: 320px;
        right: 15%;
        top: -210px;
    }

    .ct-op-hero::after {
        width: 240px;
        height: 240px;
        right: -80px;
        bottom: -150px;
    }

    .ct-op-hero-content {
        position: relative;
        z-index: 1;
        max-width: 590px;
    }

    .ct-op-label {
        display: inline-block;
        padding: 8px 14px;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 30px;
        background: rgba(255,255,255,.12);
        color: #e4f3ff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .ct-op-hero h1 {
        margin: 0 0 12px;
        color: #fff;
        font-size: clamp(27px, 3vw, 38px);
        font-weight: 800;
        line-height: 1.2;
    }

    .ct-op-hero p {
        max-width: 540px;
        margin: 0;
        color: #dcecff;
        font-size: 14px;
        line-height: 1.8;
    }

    .ct-op-access {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 22px;
        padding: 9px 15px;
        border-radius: 30px;
        background: #dff2ff;
        color: #14518d;
        font-size: 12px;
        font-weight: 800;
    }

    .ct-op-mascot {
        position: relative;
        z-index: 1;
        flex: 0 0 230px;
        width: 230px;
        height: 230px;
        object-fit: contain;
        border-radius: 20px;
    }

    .ct-op-section {
        margin-top: 32px;
    }

    .ct-op-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 17px;
    }

    .ct-op-section-heading h2 {
        margin: 0;
        color: #15345d;
        font-size: 21px;
        font-weight: 800;
    }

    .ct-op-section-heading p {
        margin: 5px 0 0;
        color: #71839d;
        font-size: 13px;
    }

    .ct-op-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .ct-op-card {
        display: flex;
        flex-direction: column;
        min-height: 220px;
        padding: 23px;
        border: 1px solid #e3ebf5;
        border-radius: 19px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(22, 50, 85, .045);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .ct-op-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(22, 50, 85, .09);
    }

    .ct-op-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 47px;
        height: 47px;
        margin-bottom: 18px;
        border-radius: 14px;
        background: #e7f2ff;
        color: #1766b7;
        font-size: 22px;
        font-weight: 800;
    }

    .ct-op-card h3 {
        margin: 0 0 9px;
        color: #17365f;
        font-size: 16px;
        font-weight: 800;
    }

    .ct-op-card p {
        margin: 0;
        color: #718096;
        font-size: 13px;
        line-height: 1.75;
    }

    .ct-op-card a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        width: fit-content;
        margin-top: auto;
        padding-top: 17px;
        color: #1766b7;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .ct-op-card a:hover {
        color: #0b3d77;
    }

    .ct-op-info {
        display: flex;
        align-items: center;
        gap: 17px;
        margin-top: 28px;
        padding: 21px 23px;
        border: 1px solid #d9e9fb;
        border-radius: 18px;
        background: linear-gradient(100deg, #edf6ff, #fff);
    }

    .ct-op-info-icon {
        display: flex;
        flex: 0 0 43px;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        border-radius: 13px;
        background: #d8ebff;
        color: #155da8;
        font-size: 20px;
    }

    .ct-op-info h3 {
        margin: 0 0 5px;
        color: #17365f;
        font-size: 14px;
        font-weight: 800;
    }

    .ct-op-info p {
        margin: 0;
        color: #718096;
        font-size: 12px;
        line-height: 1.6;
    }

    @media (max-width: 850px) {
        .ct-op-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ct-op-hero {
            padding: 28px;
        }

        .ct-op-mascot {
            flex-basis: 175px;
            width: 175px;
            height: 190px;
        }
    }

    @media (max-width: 600px) {
        .ct-op-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 25px;
        }

        .ct-op-mascot {
            align-self: center;
            width: 190px;
            height: 190px;
        }

        .ct-op-grid {
            grid-template-columns: 1fr;
        }

        .ct-op-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="ct-op">

    <section class="ct-op-hero" id="overview">
        <div class="ct-op-hero-content">
            <span class="ct-op-label">CARGOTRACE · LOGISTICS OPERATIONS</span>

            <h1>Welcome, {{ $user->name }}!</h1>

            <p>
                Selamat datang di pusat operasional CargoTrace.
                Kelola proses pengiriman, pantau perjalanan kargo,
                dan pastikan setiap aktivitas tercatat dengan baik.
            </p>

            <span class="ct-op-access">
                ✓ &nbsp; OPERATOR ACCESS
            </span>
        </div>

        <img
            src="{{ asset('images/jek-hero.png') }}"
            alt="JEK - CargoTrace Mascot"
            class="ct-op-mascot"
        >
    </section>

    <section class="ct-op-section" id="shipments">
        <div class="ct-op-section-heading">
            <div>
                <h2>Operational Workspace</h2>
                <p>Akses fitur utama untuk menjalankan aktivitas operasional.</p>
            </div>
        </div>

        <div class="ct-op-grid">

            <article class="ct-op-card">
                <div class="ct-op-icon">📦</div>
                <h3>Shipment Processing</h3>
                <p>
                    Mengelola proses pengiriman, memeriksa informasi kargo,
                    dan memantau data setiap shipment.
                </p>
                <a href="{{ route('shipments.index') }}">
                    Kelola Pengiriman →
                </a>
            </article>

            <article class="ct-op-card" id="tracking">
                <div class="ct-op-icon">⌖</div>
                <h3>Shipment Tracking</h3>
                <p>
                    Memantau perkembangan perjalanan barang, lokasi,
                    waktu pembaruan, dan peristiwa tracking.
                </p>
                <a href="{{ route('shipments.index') }}">
                    Lihat Pengiriman →
                </a>
            </article>

            <article class="ct-op-card" id="exceptions">
                <div class="ct-op-icon">!</div>
                <h3>Exception Handling</h3>
                <p>
                    Memantau dan menindaklanjuti kendala seperti
                    keterlambatan, kerusakan, dan masalah operasional.
                </p>
                <a href="{{ route('shipments.index') }}">
                    Periksa Pengiriman →
                </a>
            </article>

        </div>
    </section>

    <section class="ct-op-info">
        <div class="ct-op-info-icon">✓</div>
        <div>
            <h3>Operational Reminder</h3>
            <p>
                Pastikan setiap perubahan informasi pengiriman dicatat
                dengan benar agar proses pemantauan CargoTrace tetap akurat.
            </p>
        </div>
    </section>

</div>

@endsection