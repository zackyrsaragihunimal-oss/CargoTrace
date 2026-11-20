<?php

namespace App\Services;

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
        return $user->notifications()->create([
            'shipment_id' => $shipment?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ?: null,
        ]);
    }
}