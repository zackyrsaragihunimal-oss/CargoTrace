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

<div class="page-heading">
    <h1>Welcome, {{ $user->name }}</h1>
    <p>Operational workspace for shipment processing and logistics monitoring.</p>
</div>

<section id="overview" style="margin-bottom: 28px;">
    <div class="card">
        <h3 style="margin-bottom: 10px; color: #10233f;">
            Operational Overview
        </h3>
        <p style="font-size: 14px; color: #64748b; line-height: 1.7;">
            Dashboard operator digunakan untuk menjalankan aktivitas
            operasional pengiriman, memperbarui informasi perjalanan,
            dan memastikan setiap proses pengiriman tercatat dengan baik.
        </p>
        <div style="margin-top: 18px;">
            <span style="background: #dbeafe; color: #1e40af; padding: 7px 12px; border-radius: 20px; font-size: 12px;">
                Operator Access
            </span>
        </div>
    </div>
</section>

<section id="shipments" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Shipment Operations
    </h3>
    <div class="card">
        <h3 style="margin-bottom: 10px;">Shipment Processing</h3>
        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Mengelola proses operasional pengiriman, memeriksa informasi
            kargo, memperbarui status, dan memantau perjalanan barang.
        </p>
    </div>
</section>

<section id="tracking" style="margin-bottom: 28px;">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Tracking Updates
    </h3>
    <div class="card">
        <h3 style="margin-bottom: 10px;">Shipment Tracking</h3>
        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Mencatat peristiwa tracking, lokasi terkini, waktu pembaruan,
            serta informasi perkembangan pengiriman.
        </p>
    </div>
</section>

<section id="exceptions">
    <h3 style="margin-bottom: 15px; color: #10233f;">
        Shipment Exceptions
    </h3>
    <div class="card">
        <h3 style="margin-bottom: 10px;">Exception Handling</h3>
        <p style="font-size: 13px; color: #64748b; line-height: 1.7;">
            Mencatat dan menindaklanjuti kendala pengiriman,
            termasuk keterlambatan, kerusakan, dan masalah operasional lainnya.
        </p>
    </div>
</section>

@endsection
