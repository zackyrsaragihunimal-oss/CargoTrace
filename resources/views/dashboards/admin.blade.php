@extends('layouts.app')

@section('title', __('admin.title'))
@section('header', __('admin.header'))

@section('sidebar-menu')
    <a href="#overview" class="active">{{ __('admin.overview') }}</a>
    <a href="#shipments">{{ __('admin.recent_shipments') }}</a>
    <a href="#activities">{{ __('admin.recent_activities') }}</a>
    <a href="#management">{{ __('admin.system_management') }}</a>
@endsection

@section('content')
<style>
    .ct-admin-dashboard {
        --admin-ink: #142B45;
        --admin-muted: #64766C;
        --admin-border: #E1EAE4;
        --admin-surface: #FFFFFF;
        --admin-bg: #F3F7F4;
        --admin-blue: #287A57;
        --admin-green: #287A57;
        --admin-amber: #b45309;
        --admin-red: #b91c1c;
        color: var(--admin-ink);
    }

    .ct-admin-dashboard,
    .ct-admin-dashboard * {
        box-sizing: border-box;
    }

    .ct-admin-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 22px;
    }

    .ct-admin-heading-copy {
        flex: 1;
        min-width: 0;
    }

    .ct-admin-heading-side {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 18px;
        flex-shrink: 0;
    }

    .ct-admin-mascot {
        display: block;
        width: 112px;
        height: 112px;
        object-fit: contain;
        object-position: center bottom;
        background: transparent;
    }

    .ct-admin-eyebrow {
        margin: 0 0 7px;
        color: var(--admin-blue);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .ct-admin-heading h1 {
        margin: 0;
        color: var(--admin-ink);
        font-size: clamp(23px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .ct-admin-heading p {
        margin: 7px 0 0;
        color: var(--admin-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .ct-admin-date {
        flex-shrink: 0;
        padding: 9px 12px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: var(--admin-surface);
        color: #526B60;
        font-size: 12px;
        font-weight: 700;
    }

    .ct-admin-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 25px;
    }

    .ct-admin-stat {
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--admin-border);
        border-radius: 13px;
        background: var(--admin-surface);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
    }

    .ct-admin-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .ct-admin-stat-label {
        color: var(--admin-muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .055em;
        line-height: 1.5;
        text-transform: uppercase;
    }

    .ct-admin-stat-mark {
        display: inline-flex;
        width: 29px;
        height: 29px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #E8F3EC;
        color: var(--admin-blue);
        font-size: 13px;
        font-weight: 900;
    }

    .ct-admin-stat:nth-child(2) .ct-admin-stat-mark,
    .ct-admin-stat:nth-child(3) .ct-admin-stat-mark {
        background: #E8F3EC;
        color: var(--admin-green);
    }

    .ct-admin-stat:nth-child(5) .ct-admin-stat-mark {
        background: #fff7ed;
        color: var(--admin-amber);
    }

    .ct-admin-stat-value {
        margin-top: 13px;
        color: var(--admin-ink);
        font-size: 29px;
        font-weight: 800;
        letter-spacing: -.045em;
        line-height: 1;
        overflow-wrap: anywhere;
    }

    .ct-admin-stat-note {
        margin-top: 8px;
        color: #91A198;
        font-size: 11px;
        line-height: 1.45;
    }

    .ct-admin-section {
        margin-bottom: 23px;
    }

    .ct-admin-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .ct-admin-section-heading h2 {
        margin: 0;
        color: var(--admin-ink);
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .ct-admin-section-caption {
        color: var(--admin-muted);
        font-size: 11px;
    }

    .ct-admin-panel {
        overflow: hidden;
        border: 1px solid var(--admin-border);
        border-radius: 13px;
        background: var(--admin-surface);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
    }

    .ct-admin-status-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        padding: 15px;
    }

    .ct-admin-status-item {
        min-width: 0;
        padding: 15px 12px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: var(--admin-bg);
    }

    .ct-admin-status-item strong {
        display: block;
        color: var(--admin-ink);
        font-size: 23px;
        font-weight: 800;
        letter-spacing: -.04em;
    }

    .ct-admin-status-item span {
        display: block;
        margin-top: 5px;
        color: var(--admin-muted);
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .ct-admin-table-wrap {
        overflow-x: auto;
        width: 100%;
    }

    .ct-admin-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 12px;
        white-space: nowrap;
    }

    .ct-admin-table th {
        padding: 13px 16px;
        border-bottom: 1px solid var(--admin-border);
        background: #F7FAF8;
        color: #7b8798;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .ct-admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #E1EAE4;
        color: #526B60;
        vertical-align: middle;
    }

    .ct-admin-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ct-admin-table tbody tr:hover {
        background: #F3F7F4;
    }

    .ct-admin-table a {
        color: var(--admin-blue);
        font-weight: 800;
        text-decoration: none;
    }

    .ct-admin-table a:hover {
        text-decoration: underline;
    }

    .ct-admin-route {
        color: #526B60;
        font-weight: 600;
    }

    .ct-admin-route-arrow {
        padding: 0 5px;
        color: #91A198;
    }

    .ct-admin-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border: 1px solid #C9E3D3;
        border-radius: 20px;
        background: #E8F3EC;
        color: #1D6245;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .025em;
    }

    .ct-admin-empty {
        padding: 30px 18px;
        color: #91A198;
        font-size: 12px;
        text-align: center;
    }

    .ct-admin-management {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px;
    }

    .ct-admin-management-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-width: 0;
        padding: 15px;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        background: #fff;
        color: var(--admin-ink);
        text-decoration: none;
        transition: border-color .18s ease, background .18s ease, transform .18s ease;
    }

    .ct-admin-management-link:hover {
        transform: translateY(-2px);
        border-color: #B5D9C2;
        background: #F7FAF8;
    }

    .ct-admin-management-link strong {
        display: block;
        font-size: 12px;
        font-weight: 800;
    }

    .ct-admin-management-link small {
        display: block;
        margin-top: 4px;
        color: var(--admin-muted);
        font-size: 10px;
        line-height: 1.4;
    }

    .ct-admin-management-arrow {
        color: var(--admin-blue);
        font-size: 17px;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .ct-admin-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ct-admin-status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .ct-admin-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .ct-admin-heading-side {
            width: 100%;
            justify-content: space-between;
        }

        .ct-admin-mascot {
            width: 88px;
            height: 88px;
        }

        .ct-admin-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .ct-admin-stat {
            padding: 14px;
        }

        .ct-admin-stat-value {
            font-size: 25px;
        }

        .ct-admin-status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 10px;
        }

        .ct-admin-management {
            grid-template-columns: 1fr;
            padding: 10px;
        }

        .ct-admin-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-admin-management-link {
            transition: none;
        }
    }

    /* ===== PREMIUM ADMIN DASHBOARD V1 ===== */

    .ct-admin-heading {
        position: relative;
        overflow: hidden;
        min-height: 190px;
        padding: 28px 30px;
        align-items: center;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 20px;
        background:
            radial-gradient(circle at 85% 15%, rgba(110,190,145,.25), transparent 30%),
            linear-gradient(120deg, #142B45 0%, #1D4650 58%, #287A57 100%);
        box-shadow: 0 12px 28px rgba(20,43,69,.13);
    }

    .ct-admin-heading::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 100px;
        bottom: -165px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        pointer-events: none;
    }

    .ct-admin-heading-copy,
    .ct-admin-heading-side {
        position: relative;
        z-index: 1;
    }

    .ct-admin-eyebrow {
        color: #B9E8CE;
        letter-spacing: .16em;
    }

    .ct-admin-heading h1 {
        color: #FFFFFF;
        font-size: clamp(25px, 3vw, 34px);
    }

    .ct-admin-heading p {
        color: rgba(255,255,255,.78);
    }

    .ct-admin-date {
        border-color: rgba(255,255,255,.2);
        background: rgba(255,255,255,.12);
        color: #FFFFFF;
        backdrop-filter: blur(8px);
    }

    .ct-admin-mascot {
        filter: drop-shadow(0 8px 10px rgba(0,0,0,.18));
        transition: transform .25s ease;
    }

    .ct-admin-mascot:hover {
        transform: translateY(-5px);
    }

    .ct-admin-stat {
        position: relative;
        overflow: hidden;
        padding: 20px;
        border-radius: 16px;
        border-top: 3px solid #287A57;
        box-shadow: 0 5px 15px rgba(20,43,69,.045);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .ct-admin-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(20,43,69,.09);
    }

    .ct-admin-stat:nth-child(2) {
        border-top-color: #4385B8;
    }

    .ct-admin-stat:nth-child(3) {
        border-top-color: #4D9478;
    }

    .ct-admin-stat:nth-child(4) {
        border-top-color: #8B78BB;
    }

    .ct-admin-stat:nth-child(5) {
        border-top-color: #D99445;
    }

    .ct-admin-stat:nth-child(2) .ct-admin-stat-mark {
        background: #EAF3FB;
        color: #4385B8;
    }

    .ct-admin-stat:nth-child(3) .ct-admin-stat-mark {
        background: #E8F3EC;
        color: #287A57;
    }

    .ct-admin-stat:nth-child(4) .ct-admin-stat-mark {
        background: #F0EDFA;
        color: #806AB0;
    }

    .ct-admin-stat:nth-child(5) .ct-admin-stat-mark {
        background: #FFF3E5;
        color: #B96C22;
    }

    .ct-admin-stat-value {
        font-size: 32px;
    }

    .ct-admin-status-item {
        border-left: 3px solid #91A198;
        border-radius: 11px;
        background: linear-gradient(145deg, #FFFFFF, #F3F7F4);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .ct-admin-status-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 16px rgba(20,43,69,.07);
    }

    .ct-admin-status-item:nth-child(1) {
        border-left-color: #91A198;
    }

    .ct-admin-status-item:nth-child(2) {
        border-left-color: #4385B8;
    }

    .ct-admin-status-item:nth-child(3) {
        border-left-color: #8B78BB;
    }

    .ct-admin-status-item:nth-child(4) {
        border-left-color: #D99445;
    }

    .ct-admin-status-item:nth-child(5) {
        border-left-color: #287A57;
    }

    .ct-admin-status-item:nth-child(6) {
        border-left-color: #1D6245;
    }

    .ct-admin-panel {
        border-radius: 16px;
        box-shadow: 0 5px 16px rgba(20,43,69,.035);
    }

    .ct-admin-management-link {
        min-height: 76px;
        border-radius: 13px;
        background: linear-gradient(145deg, #FFFFFF, #F8FBF9);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .ct-admin-management-link:hover {
        transform: translateY(-4px);
        border-color: #8FC4A5;
        background: #FFFFFF;
        box-shadow: 0 9px 20px rgba(40,122,87,.09);
    }

    .ct-admin-management-link strong {
        color: #142B45;
        font-size: 13px;
    }

    .ct-admin-management-arrow {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #E8F3EC;
        color: #287A57;
        transition: transform .2s ease, background .2s ease;
    }

    .ct-admin-management-link:hover .ct-admin-management-arrow {
        transform: translateX(3px);
        background: #287A57;
        color: #FFFFFF;
    }

    @media (max-width: 650px) {
        .ct-admin-heading {
            min-height: 0;
            padding: 22px 19px;
            border-radius: 16px;
        }

        .ct-admin-heading h1 {
            font-size: 25px;
        }

        .ct-admin-stat {
            padding: 15px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ct-admin-stat,
        .ct-admin-status-item,
        .ct-admin-mascot,
        .ct-admin-management-link,
        .ct-admin-management-arrow {
            transition: none;
        }
    }

    /* ===== END PREMIUM ADMIN DASHBOARD V1 ===== */

    /* ===== PREMIUM ADMIN DASHBOARD V2 ===== */

    /* Welcome banner */
    .ct-admin-heading {
        min-height: 205px;
        padding: 32px 34px;
        border-radius: 22px;
        background:
            radial-gradient(ellipse at 8% 110%, rgba(76,157,112,.30), transparent 38%),
            radial-gradient(circle at 90% 10%, rgba(157,220,183,.22), transparent 28%),
            linear-gradient(115deg, #10283F 0%, #173D4A 48%, #287A57 100%);
        box-shadow: 0 16px 35px rgba(20,43,69,.16);
    }

    .ct-admin-heading::before {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        right: 5%;
        top: -205px;
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 50%;
        box-shadow:
            0 0 0 28px rgba(255,255,255,.025),
            0 0 0 58px rgba(255,255,255,.02);
        pointer-events: none;
    }

    .ct-admin-heading::after {
        width: 260px;
        height: 260px;
        right: 17%;
        bottom: -215px;
        border-color: rgba(255,255,255,.14);
    }

    .ct-admin-heading h1 {
        font-size: clamp(27px, 3.2vw, 37px);
        letter-spacing: -.045em;
        text-shadow: 0 2px 12px rgba(0,0,0,.12);
    }

    .ct-admin-heading p {
        max-width: 560px;
        font-size: 13px;
    }

    .ct-admin-mascot {
        width: 132px;
        height: 132px;
        filter: drop-shadow(0 12px 13px rgba(0,0,0,.22));
    }

    .ct-admin-date {
        padding: 11px 15px;
        border-radius: 12px;
        font-size: 12px;
        letter-spacing: .02em;
    }

    /* Statistics cards */
    .ct-admin-stats {
        gap: 15px;
        margin-top: 4px;
        margin-bottom: 30px;
    }

    .ct-admin-stat {
        min-height: 150px;
        padding: 20px 19px;
        border: 1px solid #E1EAE4;
        border-top: 3px solid #287A57;
        border-radius: 17px;
        background: linear-gradient(150deg, #FFFFFF 55%, #F1F8F3 100%);
        box-shadow: 0 6px 18px rgba(20,43,69,.045);
    }

    .ct-admin-stat:nth-child(2) {
        background: linear-gradient(150deg, #FFFFFF 55%, #EFF6FC 100%);
    }

    .ct-admin-stat:nth-child(3) {
        background: linear-gradient(150deg, #FFFFFF 55%, #EFF7F2 100%);
    }

    .ct-admin-stat:nth-child(4) {
        background: linear-gradient(150deg, #FFFFFF 55%, #F4F1FA 100%);
    }

    .ct-admin-stat:nth-child(5) {
        background: linear-gradient(150deg, #FFFFFF 55%, #FFF6EC 100%);
    }

    .ct-admin-stat-mark {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        font-size: 14px;
    }

    .ct-admin-stat-value {
        margin-top: 17px;
        font-size: 34px;
    }

    .ct-admin-stat-note {
        font-size: 11px;
        color: #71877B;
    }

    /* Section hierarchy */
    .ct-admin-section-heading {
        margin-bottom: 15px;
    }

    .ct-admin-section-heading h2 {
        position: relative;
        padding-left: 12px;
        font-size: 17px;
    }

    .ct-admin-section-heading h2::before {
        content: "";
        position: absolute;
        left: 0;
        top: 3px;
        bottom: 3px;
        width: 3px;
        border-radius: 5px;
        background: linear-gradient(180deg, #287A57, #75B58E);
    }

    .ct-admin-section-caption {
        font-size: 11px;
        color: #71877B;
    }

    /* Shipment monitoring panel */
    .ct-admin-panel {
        border-radius: 17px;
        box-shadow: 0 7px 20px rgba(20,43,69,.045);
    }

    .ct-admin-status-grid {
        gap: 11px;
        padding: 17px;
        background:
            radial-gradient(circle at 100% 0%, rgba(40,122,87,.045), transparent 35%),
            #FFFFFF;
    }

    .ct-admin-status-item {
        min-height: 88px;
        padding: 15px 13px;
        border-radius: 12px;
        background: linear-gradient(145deg, #FFFFFF, #F1F6F3);
    }

    .ct-admin-status-item strong {
        font-size: 27px;
    }

    .ct-admin-status-item span {
        margin-top: 8px;
        font-size: 10px;
        letter-spacing: .025em;
    }

    .ct-admin-status-item:nth-child(2) {
        background: linear-gradient(145deg, #FFFFFF, #F0F6FC);
    }

    .ct-admin-status-item:nth-child(3) {
        background: linear-gradient(145deg, #FFFFFF, #F4F1FA);
    }

    .ct-admin-status-item:nth-child(4) {
        background: linear-gradient(145deg, #FFFFFF, #FFF6EC);
    }

    /* Recent shipments table */
    .ct-admin-table th {
        padding-top: 15px;
        padding-bottom: 15px;
        background: #F0F6F2;
        color: #526B60;
    }

    .ct-admin-table td {
        padding-top: 16px;
        padding-bottom: 16px;
    }

    .ct-admin-table tbody tr {
        transition: background .18s ease;
    }

    .ct-admin-pill {
        padding: 6px 10px;
        font-size: 9px;
    }

    /* Management shortcuts */
    .ct-admin-management {
        gap: 14px;
        padding: 17px;
    }

    .ct-admin-management-link {
        min-height: 88px;
        padding: 17px;
        border-radius: 14px;
        box-shadow: 0 3px 10px rgba(20,43,69,.025);
    }

    .ct-admin-management-link strong {
        font-size: 13px;
    }

    .ct-admin-management-link small {
        font-size: 11px;
        line-height: 1.55;
    }

    .ct-admin-management-arrow {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        font-size: 19px;
    }

    @media (max-width: 1100px) {
        .ct-admin-heading {
            padding: 27px;
        }

        .ct-admin-mascot {
            width: 112px;
            height: 112px;
        }
    }

    @media (max-width: 650px) {
        .ct-admin-heading {
            padding: 22px 19px;
            min-height: 0;
        }

        .ct-admin-heading h1 {
            font-size: 26px;
        }

        .ct-admin-heading-side {
            align-items: center;
        }

        .ct-admin-mascot {
            width: 94px;
            height: 94px;
        }

        .ct-admin-status-grid {
            gap: 8px;
            padding: 11px;
        }

        .ct-admin-status-item {
            min-height: 78px;
            padding: 12px 10px;
        }

        .ct-admin-management {
            gap: 10px;
        }
    }

    /* ===== END PREMIUM ADMIN DASHBOARD V2 ===== */

    /* ===== LIVE OPERATIONAL ANALYTICS ===== */
    .ct-live-analytics {
        margin-bottom: 27px;
    }

    .ct-live-analytics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }

    .ct-live-metric {
        position: relative;
        overflow: hidden;
        min-width: 0;
        padding: 19px 20px;
        border: 1px solid #E1EAE4;
        border-radius: 16px;
        background: #FFFFFF;
        box-shadow: 0 5px 17px rgba(20,43,69,.045);
    }

    .ct-live-metric::after {
        content: "";
        position: absolute;
        width: 75px;
        height: 75px;
        right: -25px;
        bottom: -35px;
        border-radius: 50%;
        background: var(--metric-tint, #E8F3EC);
        opacity: .75;
    }

    .ct-live-metric-label {
        color: #718276;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .045em;
        text-transform: uppercase;
    }

    .ct-live-metric-value {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        color: #142B45;
        font-size: 31px;
        font-weight: 850;
        letter-spacing: -.045em;
        line-height: 1.1;
    }

    .ct-live-metric-note {
        position: relative;
        z-index: 1;
        margin-top: 8px;
        color: #819087;
        font-size: 10px;
    }

    .ct-live-metric-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        margin-bottom: 13px;
        border-radius: 11px;
        background: var(--metric-tint, #E8F3EC);
        color: var(--metric-color, #287A57);
        font-size: 15px;
        font-weight: 900;
    }

    .ct-live-analytics-lower {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(230px, .85fr);
        gap: 16px;
    }

    .ct-live-panel {
        min-width: 0;
        padding: 21px;
        border: 1px solid #E1EAE4;
        border-radius: 17px;
        background: #FFFFFF;
        box-shadow: 0 5px 17px rgba(20,43,69,.04);
    }

    .ct-live-panel-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
    }

    .ct-live-panel-heading h3 {
        margin: 0;
        color: #142B45;
        font-size: 14px;
        font-weight: 850;
    }

    .ct-live-panel-heading p {
        margin: 5px 0 0;
        color: #819087;
        font-size: 10px;
    }

    .ct-live-period {
        flex-shrink: 0;
        padding: 7px 10px;
        border: 1px solid #DCEAE1;
        border-radius: 9px;
        background: #F3F8F5;
        color: #287A57;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-live-chart {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        align-items: end;
        gap: 12px;
        min-height: 175px;
        padding: 12px 5px 0;
        border-bottom: 1px solid #E8EEE9;
        background: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent 42px,
            #EEF3EF 43px,
            transparent 44px
        );
    }

    .ct-live-chart-column {
        display: flex;
        min-width: 0;
        height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .ct-live-chart-value {
        color: #536B5D;
        font-size: 10px;
        font-weight: 800;
    }

    .ct-live-chart-track {
        display: flex;
        width: min(100%, 32px);
        height: 112px;
        align-items: flex-end;
        justify-content: center;
    }

    .ct-live-chart-bar {
        width: 100%;
        min-height: 5px;
        border-radius: 7px 7px 3px 3px;
        background: linear-gradient(180deg, #54A77A, #287A57);
        box-shadow: 0 3px 8px rgba(40,122,87,.15);
        transition: height .3s ease;
    }

    .ct-live-chart-label {
        padding-bottom: 10px;
        color: #819087;
        font-size: 10px;
        font-weight: 700;
    }

    .ct-live-insight-list {
        display: grid;
        gap: 12px;
    }

    .ct-live-insight {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 12px;
        border: 1px solid #E8EEE9;
        border-radius: 12px;
        background: #FAFCFA;
    }

    .ct-live-insight-dot {
        width: 9px;
        height: 9px;
        flex-shrink: 0;
        border-radius: 50%;
        background: var(--insight-color, #287A57);
        box-shadow: 0 0 0 4px var(--insight-tint, #E8F3EC);
    }

    .ct-live-insight-copy {
        min-width: 0;
        flex: 1;
    }

    .ct-live-insight-copy strong {
        display: block;
        color: #243D32;
        font-size: 11px;
        font-weight: 800;
    }

    .ct-live-insight-copy span {
        display: block;
        margin-top: 4px;
        color: #819087;
        font-size: 10px;
    }

    .ct-live-insight-number {
        color: #142B45;
        font-size: 17px;
        font-weight: 850;
    }

    @media (max-width: 1000px) {
        .ct-live-analytics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ct-live-analytics-lower {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .ct-live-analytics-grid {
            gap: 9px;
        }

        .ct-live-metric {
            padding: 15px;
        }

        .ct-live-metric-value {
            font-size: 26px;
        }

        .ct-live-panel {
            padding: 15px;
        }

        .ct-live-chart {
            gap: 5px;
        }

        .ct-live-chart-track {
            width: 22px;
        }
    }
    /* ===== END LIVE OPERATIONAL ANALYTICS ===== */
</style>

<div class="ct-admin-dashboard" data-admin-dashboard data-locale="{{ app()->getLocale() }}" data-live-connected="{{ __('admin.live_connected') }}" data-live-unavailable="{{ __('admin.live_unavailable') }}" data-updated-prefix="{{ __('admin.updated_prefix') }}">
    <section class="ct-admin-heading" id="overview">
        <div class="ct-admin-heading-copy">
            <p class="ct-admin-eyebrow">{{ __('admin.control_center') }}</p>
            <h1>{{ __('admin.welcome', ['name' => $user->name]) }}</h1>
            <p>{{ __('admin.intro') }}</p>
        </div>

        <div class="ct-admin-heading-side">
            <div class="ct-admin-date">{{ now()->format('d M Y') }}</div>
            <img
                src="{{ asset('images/CargoTrace_JEK_Mascot_Pack/04_Dashboard_Roles/jek-admin.png') }}"
                alt="JEK - CargoTrace Administrator Mascot"
                class="ct-admin-mascot"
            >
        </div>
    </section>

    <section class="ct-admin-stats" aria-label="{{ __('admin.platform_statistics') }}">
        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">{{ __('admin.total_shipments') }}</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">S</span>
            </div>
            <div class="ct-admin-stat-value" data-stat="totalShipments">{{ number_format($totalShipments) }}</div>
            <div class="ct-admin-stat-note">{{ __('admin.all_registered_shipments') }}</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">{{ __('admin.active_users') }}</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">U</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($activeUsers) }}</div>
            <div class="ct-admin-stat-note">{{ __('admin.currently_active_accounts') }}</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">{{ __('admin.customers') }}</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">C</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="ct-admin-stat-note">{{ __('admin.registered_customers') }}</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">{{ __('admin.operators') }}</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">O</span>
            </div>
            <div class="ct-admin-stat-value">{{ number_format($totalOperators) }}</div>
            <div class="ct-admin-stat-note">{{ __('admin.registered_operators') }}</div>
        </article>

        <article class="ct-admin-stat">
            <div class="ct-admin-stat-top">
                <span class="ct-admin-stat-label">{{ __('admin.open_exceptions') }}</span>
                <span class="ct-admin-stat-mark" aria-hidden="true">!</span>
            </div>
            <div class="ct-admin-stat-value" data-stat="openExceptions">{{ number_format($openExceptions) }}</div>
            <div class="ct-admin-stat-note">{{ __('admin.open_and_progress') }}</div>
        </article>
    </section>

    <section class="ct-live-analytics" aria-label="{{ __('admin.operational_analytics') }}">
        <div class="ct-admin-section-heading">
            <h2>{{ __('admin.operational_performance') }}</h2>
            <span class="ct-admin-section-caption">{{ __('admin.shipment_monitoring') }}</span>
            <span class="ct-admin-section-caption" data-stats-updated aria-live="polite">{{ __('admin.connecting_live') }}</span>
        </div>

        <div class="ct-live-analytics-grid">
            <article class="ct-live-metric" style="--metric-tint:#E8F3EC;--metric-color:#287A57;">
                <span class="ct-live-metric-icon" aria-hidden="true">A</span>
                <div class="ct-live-metric-label">{{ __('admin.active_shipments') }}</div>
                <div class="ct-live-metric-value" data-stat="activeShipments">{{ number_format($activeShipments) }}</div>
                <div class="ct-live-metric-note">{{ __('admin.not_delivered_cancelled') }}</div>
            </article>

            <article class="ct-live-metric" style="--metric-tint:#EAF3FB;--metric-color:#4385B8;">
                <span class="ct-live-metric-icon" aria-hidden="true">T</span>
                <div class="ct-live-metric-label">{{ __('admin.in_transit') }}</div>
                <div class="ct-live-metric-value" data-stat="inTransitShipments">{{ number_format($inTransitShipments) }}</div>
                <div class="ct-live-metric-note">{{ __('admin.moving_destination') }}</div>
            </article>

            <article class="ct-live-metric" style="--metric-tint:#E8F3EC;--metric-color:#287A57;">
                <span class="ct-live-metric-icon" aria-hidden="true">D</span>
                <div class="ct-live-metric-label">{{ __('admin.delivered') }}</div>
                <div class="ct-live-metric-value" data-stat="deliveredShipments">{{ number_format($deliveredShipments) }}</div>
                <div class="ct-live-metric-note">{{ __('admin.successfully_delivered') }}</div>
            </article>

            <article class="ct-live-metric" style="--metric-tint:#FFF3E5;--metric-color:#B96C22;">
                <span class="ct-live-metric-icon" aria-hidden="true">!</span>
                <div class="ct-live-metric-label">{{ __('admin.delayed') }}</div>
                <div class="ct-live-metric-value" data-stat="delayedShipments">{{ number_format($delayedShipments) }}</div>
                <div class="ct-live-metric-note">{{ __('admin.past_estimated_arrival') }}</div>
            </article>
        </div>

        <div class="ct-live-analytics-lower">
            <article class="ct-live-panel">
                <div class="ct-live-panel-heading">
                    <div>
                        <h3>{{ __('admin.creation_trend') }}</h3>
                        <p>{{ __('admin.daily_last_seven_days') }}</p>
                    </div>
                    <span class="ct-live-period">{{ __('admin.last_seven_days') }}</span>
                </div>

                @php
                    $trendMax = max(1, collect($shipmentTrend)->max('total') ?? 0);
                @endphp

                <div class="ct-live-chart" role="img" aria-label="{{ __('admin.shipment_chart') }}">
                    @foreach ($shipmentTrend as $point)
                        <div class="ct-live-chart-column">
                            <span class="ct-live-chart-value">{{ $point['total'] }}</span>
                            <div class="ct-live-chart-track">
                                <div
                                    class="ct-live-chart-bar"
                                    style="height: {{ max(5, (int) round(($point['total'] / $trendMax) * 100)) }}%;"
                                    title="{{ $point['label'] }}: {{ $point['total'] }} {{ __('admin.shipments_unit') }}"
                                ></div>
                            </div>
                            <span class="ct-live-chart-label">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="ct-live-panel">
                <div class="ct-live-panel-heading">
                    <div>
                        <h3>{{ __('admin.operational_snapshot') }}</h3>
                        <p>{{ __('admin.current_shipment_condition') }}</p>
                    </div>
                </div>

                <div class="ct-live-insight-list">
                    <div class="ct-live-insight">
                        <span class="ct-live-insight-dot" style="--insight-color:#4385B8;--insight-tint:#EAF3FB;"></span>
                        <div class="ct-live-insight-copy">
                            <strong>{{ __('admin.in_transit') }}</strong>
                            <span>{{ __('admin.currently_moving') }}</span>
                        </div>
                        <span class="ct-live-insight-number" data-stat="inTransitShipments">{{ number_format($inTransitShipments) }}</span>
                    </div>

                    <div class="ct-live-insight">
                        <span class="ct-live-insight-dot" style="--insight-color:#287A57;--insight-tint:#E8F3EC;"></span>
                        <div class="ct-live-insight-copy">
                            <strong>{{ __('admin.successfully_delivered_label') }}</strong>
                            <span>{{ __('admin.successfully_delivered_journeys') }}</span>
                        </div>
                        <span class="ct-live-insight-number" data-stat="deliveredShipments">{{ number_format($deliveredShipments) }}</span>
                    </div>

                    <div class="ct-live-insight">
                        <span class="ct-live-insight-dot" style="--insight-color:#D99445;--insight-tint:#FFF3E5;"></span>
                        <div class="ct-live-insight-copy">
                            <strong>{{ __('admin.delayed_shipments_label') }}</strong>
                            <span>{{ __('admin.past_estimated_arrival') }}</span>
                        </div>
                        <span class="ct-live-insight-number" data-stat="delayedShipments">{{ number_format($delayedShipments) }}</span>
                    </div>
                </div>
            </article>
        </div>
    </section>
    <section class="ct-admin-section" aria-labelledby="shipment-status-heading">
        <div class="ct-admin-section-heading">
            <h2 id="shipment-status-heading">{{ __('admin.shipment_status_overview') }}</h2>
            <span class="ct-admin-section-caption">{{ __('admin.current_distribution') }}</span>
        </div>
        <div class="ct-admin-panel">
            <div class="ct-admin-status-grid">
                @foreach (['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'ARRIVED', 'DELIVERED'] as $status)
                    <div class="ct-admin-status-item">
                        <strong>{{ number_format((int) ($shipmentStatusCounts[$status] ?? 0)) }}</strong>
                        <span>{{ __( 'admin.statuses.' . strtolower($status) ) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ct-admin-section" id="shipments">
        <div class="ct-admin-section-heading">
            <h2>{{ __('admin.recent_shipments') }}</h2>
            <span class="ct-admin-section-caption">{{ __('admin.latest_six_shipments') }}</span>
        </div>
        <div class="ct-admin-panel">
            @if ($recentShipments->isEmpty())
                <div class="ct-admin-empty">{{ __('admin.no_shipments') }}</div>
            @else
                <div class="ct-admin-table-wrap">
                    <table class="ct-admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.shipment_number') }}</th>
                                <th>{{ __('admin.customer') }}</th>
                                <th>{{ __('admin.route') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentShipments as $shipment)
                                <tr>
                                    <td>
                                        <a href="{{ route('shipments.show', $shipment) }}">
                                            {{ $shipment->shipment_number }}
                                        </a>
                                    </td>
                                    <td>{{ $shipment->customer?->name ?? '-' }}</td>
                                    <td>
                                        <span class="ct-admin-route">{{ $shipment->originLocation?->city ?? '-' }}</span>
                                        <span class="ct-admin-route-arrow">&rarr;</span>
                                        <span class="ct-admin-route">{{ $shipment->destinationLocation?->city ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="ct-admin-pill">{{ __( 'admin.statuses.' . strtolower($shipment->current_status) ) }}</span>
                                    </td>
                                    <td>{{ $shipment->created_at?->format('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <section class="ct-admin-section" id="activities">
        <div class="ct-admin-section-heading">
            <h2>{{ __('admin.recent_activities') }}</h2>
            <span class="ct-admin-section-caption">{{ __('admin.latest_six_activities') }}</span>
        </div>
        <div class="ct-admin-panel">
            @if ($recentActivities->isEmpty())
                <div class="ct-admin-empty">{{ __('admin.no_activities') }}</div>
            @else
                <div class="ct-admin-table-wrap">
                    <table class="ct-admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.activity') }}</th>
                                <th>{{ __('admin.user') }}</th>
                                <th>{{ __('admin.entity') }}</th>
                                <th>{{ __('admin.time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentActivities as $activity)
                                <tr>
                                    <td><span class="ct-admin-pill">{{ $activity->action }}</span></td>
                                    <td>{{ $activity->user?->name ?? __('admin.unknown_system') }}</td>
                                    <td>{{ $activity->entity_type }} #{{ $activity->entity_id ?? '-' }}</td>
                                    <td>{{ $activity->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <section class="ct-admin-section" id="management">
        <div class="ct-admin-section-heading">
            <h2>{{ __('admin.system_management') }}</h2>
            <span class="ct-admin-section-caption">{{ __('admin.quick_access') }}</span>
        </div>
        <div class="ct-admin-panel">
            <div class="ct-admin-management">
                <a class="ct-admin-management-link" href="{{ route('shipments.index') }}">
                    <span>
                        <strong>{{ __('admin.manage_shipments') }}</strong>
                        <small>{{ __('admin.review_shipments') }}</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
                <a class="ct-admin-management-link" href="{{ route('exceptions.index') }}">
                    <span>
                        <strong>{{ __('admin.manage_exceptions') }}</strong>
                        <small>{{ __('admin.review_exceptions') }}</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
                <a class="ct-admin-management-link" href="{{ route('notifications.index') }}">
                    <span>
                        <strong>{{ __('admin.view_notifications') }}</strong>
                        <small>{{ __('admin.open_notification_center') }}</small>
                    </span>
                    <span class="ct-admin-management-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
