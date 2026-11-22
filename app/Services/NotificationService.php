<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\User;

class NotificationService
{
    /**
     * Membuat notifikasi untuk satu pengguna.
     */
    public function send(
        User $user,
        ?Shipment $shipment,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        $notification = $user->notifications()->create([
            'shipment_id' => $shipment?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ?: null,
        ]);

        NotificationCreated::dispatch(
            $user->id,
            [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'shipment_id' => $notification->shipment_id,
                'shipment_number' => $shipment?->shipment_number,
                'created_at' => $notification->created_at?->toIso8601String(),
                'unread_count' => $user->notifications()
                    ->whereNull('read_at')
                    ->count(),
            ]
        );

        return $notification;
    }
}