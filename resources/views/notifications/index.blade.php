@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('sidebar-menu')
    <a href="{{ route('shipments.index') }}"
       class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        Shipments
    </a>

    <a href="{{ route('exceptions.index') }}"
       class="{{ request()->routeIs('exceptions.*') ? 'active' : '' }}">
        Exceptions
    </a>
@endsection

@section('content')
<style>
    .notification-page {
        color: #172b4d;
    }

    .notification-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .notification-heading h2 {
        font-size: 23px;
        margin-bottom: 6px;
    }

    .notification-heading p {
        color: #718096;
        font-size: 13px;
    }

    .notification-panel {
        background: white;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, .04);
    }

    .notification-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .notification-count {
        color: #64748b;
        font-size: 13px;
    }

    .notification-btn {
        border: 0;
        border-radius: 8px;
        padding: 10px 15px;
        background: #1769e0;
        color: white;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .notification-btn-secondary {
        background: #eaf1fb;
        color: #1769e0;
    }

    .notification-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 17px;
        border: 1px solid #e5eaf2;
        border-radius: 10px;
        background: #fff;
    }

    .notification-item.unread {
        background: #f3f8ff;
        border-color: #bfdbfe;
    }

    .notification-main {
        min-width: 0;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 700;
        color: #172b4d;
        margin-bottom: 7px;
    }

    .notification-message {
        font-size: 13px;
        line-height: 1.6;
        color: #52647a;
        margin-bottom: 9px;
    }

    .notification-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        color: #8492a6;
        font-size: 11px;
    }

    .notification-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .notification-unread {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .notification-read {
        background: #e2e8f0;
        color: #64748b;
    }

    .notification-item-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .notification-empty {
        text-align: center;
        padding: 55px 20px;
        color: #8492a6;
    }

    .notification-empty strong {
        display: block;
        color: #334155;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .notification-success {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 8px;
        background: #dcfce7;
        color: #166534;
        font-size: 13px;
    }

    .notification-pagination {
        margin-top: 20px;
    }

    @media (max-width: 650px) {
        .notification-item {
            flex-direction: column;
        }

        .notification-item-actions {
            width: 100%;
        }
    }
</style>

<div class="notification-page">

    <div class="notification-heading">
        <div>
            <h2>Your Notifications</h2>
            <p>Stay informed about shipment updates and activities.</p>
        </div>

        <div class="notification-actions">
            <span class="notification-count">
                {{ $unreadCount }} unread notification(s)
            </span>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="notification-btn">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="notification-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="notification-panel">
        @forelse ($notifications as $notification)
            <div class="notification-list">
                <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                    <div class="notification-main">
                        <div class="notification-title">
                            {{ $notification->title }}
                        </div>

                        <div class="notification-message">
                            {{ $notification->message }}
                        </div>

                        <div class="notification-meta">
                            <span>
                                {{ $notification->created_at->format('d M Y, H:i') }}
                            </span>

                            @if ($notification->shipment)
                                <span>
                                    Shipment:
                                    {{ $notification->shipment->shipment_number }}
                                </span>
                            @endif

                            @if ($notification->read_at)
                                <span class="notification-status notification-read">
                                    READ
                                </span>
                            @else
                                <span class="notification-status notification-unread">
                                    UNREAD
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="notification-item-actions">
                        @if ($notification->shipment)
                            <a href="{{ route('shipments.show', $notification->shipment->id) }}"
                               class="notification-btn notification-btn-secondary">
                                View Shipment
                            </a>
                        @endif

                        @if (!$notification->read_at)
                            <form method="POST"
                                  action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="notification-btn">
                                    Mark as Read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="notification-empty">
                <strong>No notifications yet</strong>
                You will see shipment updates and other important activities here.
            </div>
        @endforelse

        <div class="notification-pagination">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection