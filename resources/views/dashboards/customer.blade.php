@extends('layouts.app')

@section('title', 'Customer Dashboard')
@section('header', 'Customer Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#my-shipments">My Shipments</a>
    <a href="#tracking">Shipment Tracking</a>
    <a href="#notifications">Notifications</a>
@endsection

@section('content')

<div class="page-heading">
    <h1>Welcome, {{ $user->name }}</h1>
    <p>Monitor your shipments and stay informed about delivery progress.</p>
</div>

<section id="overview" style="margin-bottom: 28px;">
    <div class="card">
        <h3 style="margin-bottom: 10px; color: #10233f;">
            Your Shipment Overview
        </h3>

        <p style="font-size: 14px; color: #64748b; line-height: 1.7;">
            Selamat datang di CargoTrace. Melalui dashboard ini,
            Anda dapat memantau informasi pengiriman, melihat
            perkembangan perjalanan barang, dan menerima informasi
            terbaru terkait pengiriman Anda.
        </p>

        <div style="margin-top: 18px;">
            <span style="background: #dcfce7; color: #166534; padding: 7px 12px; border-radius: 20px; font-size: 12px;">
                Customer Account
            </span>
        </div>
    </div>
</section>

<section id="my-shipments" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        My Shipments
    </h3>

    <div class="card">
        <h3 style="margin-bottom: 10px;">Shipment History</h3>

        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Melihat daftar pengiriman yang terdaftar atas akun Anda,
            termasuk nomor resi, informasi barang, dan status pengiriman.
        </p>
    </div>
</section>

<section id="tracking" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Shipment Tracking
    </h3>

    <div class="card">
        <h3 style="margin-bottom: 10px;">Track Your Cargo</h3>

        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Memantau perjalanan barang berdasarkan riwayat tracking,
            lokasi terakhir, dan pembaruan status dari operator.
        </p>
    </div>
</section>

<section id="notifications">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Notifications
    </h3>

    <div class="card">
        <h3 style="margin-bottom: 10px;">Shipment Notifications</h3>

        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Mendapatkan informasi mengenai perubahan status,
            perkembangan perjalanan, dan pemberitahuan penting
            terkait pengiriman Anda.
        </p>
    </div>
</section>

@endsection
