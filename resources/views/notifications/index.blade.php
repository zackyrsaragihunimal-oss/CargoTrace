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
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        color: #172033;
    }

    .notification-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
    }

    .notification-heading h2 {
        margin: 0 0 7px;
        color: #172b4d;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .notification-heading p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .notification-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notification-count {
        color: #64748b;
        font-size: 11px;
        font-weight: 650;
    }

    .notification-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 9px 13px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease;
    }

    .notification-btn:hover {
        background: #1d4ed8;
    }

    .notification-btn-secondary {
        border-color: #dbe5f1;
        background: #eff6ff;
        color: #2563eb;
    }

    .notification-btn-secondary:hover {
        border-color: #bfdbfe;
        background: #dbeafe;
    }

    .notification-panel {
        padding: 20px;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        background: #fff;
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
        gap: 18px;
        padding: 18px;
        border: 1px solid #e5eaf1;
        border-radius: 10px;
        background: #fff;
        transition: border-color .2s ease, background .2s ease;
    }

    .notification-item:hover {
        border-color: #cbd5e1;
    }

    .notification-item.unread {
        border-color: #bfdbfe;
        background: #f8fbff;
    }

    .notification-main {
        min-width: 0;
        flex: 1;
    }

    .notification-title {
        margin-bottom: 7px;
        color: #172b4d;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.5;
    }

    .notification-message {
        margin-bottom: 11px;
        color: #52647a;
        font-size: 12px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .notification-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
        color: #8492a6;
        font-size: 10px;
        line-height: 1.5;
    }

    .notification-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .notification-unread {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .notification-read {
        background: #f1f5f9;
        color: #64748b;
    }

    .notification-item-actions {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
        flex-shrink: 0;
    }

    .notification-item-actions form,
    .notification-actions form {
        margin: 0;
    }

    .notification-empty {
        padding: 58px 20px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.7;
    }

    .notification-empty strong {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 15px;
        font-weight: 800;
    }

    .notification-success {
        padding: 13px 16px;
        margin-bottom: 17px;
        border: 1px solid #bbf7d0;
        border-radius: 9px;
        background: #f0fdf4;
        color: #166534;
        font-size: 12px;
        line-height: 1.6;
    }

    .notification-pagination {
        padding-top: 19px;
    }

    @media (max-width: 700px) {
        .notification-heading {
            align-items: flex-start;
        }

        .notification-heading h2 {
            font-size: 21px;
        }

        .notification-actions {
            justify-content: flex-start;
        }

        .notification-panel {
            padding: 13px;
        }

        .notification-item {
            flex-direction: column;
            gap: 14px;
            padding: 15px;
        }

        .notification-item-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }

    @media (max-width: 420px) {
        .notification-item-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .notification-item-actions form,
        .notification-item-actions .notification-btn {
            width: 100%;
        }

        .notification-actions form,
        .notification-actions form .notification-btn {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .notification-item,
        .notification-btn {
            transition: none;
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
            <span class="notification-count" data-notification-unread-label>
                {{ $unreadCount }} unread notification(s)
            </span>

            <form data-notification-mark-all-form method="POST" action="{{ route('notifications.read-all') }}" @if ($unreadCount <= 0) hidden @endif>
                @csrf
                @method('PATCH')

                <button type="submit" class="notification-btn">
                    Mark All as Read
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="notification-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="notification-panel" data-notification-history-list data-shipment-url-template="{{ route('shipments.show', ['shipment' => '__SHIPMENT_ID__']) }}" data-notification-read-url-template="{{ route('notifications.read', ['notification' => '__NOTIFICATION_ID__']) }}">
        @forelse ($notifications as $notification)
            <div class="notification-list">
                <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}" data-notification-id="{{ $notification->id }}">
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
