@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Administrator Dashboard')

@section('sidebar-menu')
    <a href="#overview" class="active">Overview</a>
    <a href="#management">System Management</a>
    <a href="#operations">Logistics Operations</a>
    <a href="#master-data">Master Data</a>
    <a href="#security">Security & Access</a>
@endsection

@section('content')

<div class="page-heading">
    <h1>Welcome, {{ $user->name }}</h1>
    <p>Centralized control and monitoring of CargoTrace logistics operations.</p>
</div>

<section id="overview" style="margin-bottom: 28px;">
    <div class="card">
        <h3 style="margin-bottom: 10px; color: #10233f;">
            System Overview
        </h3>

        <p style="font-size: 14px; color: #64748b; line-height: 1.7;">
            Selamat datang di pusat administrasi CargoTrace.
            Melalui dashboard ini, administrator dapat mengawasi
            aktivitas sistem, mengelola akses pengguna, dan memantau
            keseluruhan proses pengiriman.
        </p>

        <div style="margin-top: 18px; display: flex; gap: 10px; flex-wrap: wrap;">
            <span style="background: #dcfce7; color: #166534; padding: 7px 12px; border-radius: 20px; font-size: 12px;">
                Account Active
            </span>

            <span style="background: #dbeafe; color: #1e40af; padding: 7px 12px; border-radius: 20px; font-size: 12px;">
                Administrator Access
            </span>
        </div>
    </div>
</section>

<section id="management" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        System Management
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">

        <div class="card">
            <h3 style="margin-bottom: 10px;">User Management</h3>
            <p style="font-size: 13px; color: #64748b; line-height: 1.6;">
                Pengelolaan akun administrator, operator, dan customer,
                termasuk status serta hak akses pengguna.
            </p>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 10px;">Shipment Management</h3>
            <p style="font-size: 13px; color: #64748b; line-height: 1.6;">
                Pengawasan data pengiriman, informasi kargo,
                nomor resi, dan status perjalanan.
            </p>
        </div>

    </div>
</section>

<section id="operations" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Logistics Operations
    </h3>

    <div class="card">
        <h3 style="margin-bottom: 10px;">Operational Monitoring</h3>
        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Pemantauan aktivitas tracking, pembaruan lokasi,
            riwayat perjalanan, dan penanganan kendala pengiriman.
        </p>
    </div>
</section>

<section id="master-data" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Master Data
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">

        <div class="card">
            <h3 style="margin-bottom: 8px;">Carriers</h3>
            <p style="font-size: 13px; color: #64748b;">
                Data perusahaan jasa pengangkutan.
            </p>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 8px;">Locations</h3>
            <p style="font-size: 13px; color: #64748b;">
                Data lokasi asal, tujuan, dan titik perjalanan.
            </p>
        </div>

    </div>
</section>

<section id="security">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Security & Access
    </h3>

    <div class="card">
        <h3 style="margin-bottom: 10px;">Audit & Notifications</h3>
        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Pengawasan aktivitas pengguna melalui audit log,
            serta pengelolaan notifikasi dan keamanan akses sistem.
        </p>
    </div>
</section>

@endsection
