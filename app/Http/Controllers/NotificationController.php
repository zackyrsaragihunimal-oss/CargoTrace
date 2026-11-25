<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Menampilkan notifikasi milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->with('shipment')
            ->latest()
            ->paginate(15);

        $unreadCount = $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    /**
     * Menandai satu notifikasi milik pengguna sebagai sudah dibaca.
     */
    public function markRead(
        Request $request,
        Notification $notification
    ): RedirectResponse {
        $ownedNotification = $request->user()
            ->notifications()
            ->findOrFail($notification->id);

        if ($ownedNotification->read_at === null) {
            $ownedNotification->update([
                'read_at' => now(),
            ]);
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', __('notifications.success_read'));
    }

    /**
     * Menandai seluruh notifikasi pengguna sebagai sudah dibaca.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', __('notifications.success_read_all'));
    }
}