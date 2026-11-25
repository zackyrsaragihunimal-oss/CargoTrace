<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class NotificationService
{
    /**
     * Membuat notifikasi sesuai bahasa penerima.
     */
    public function send(
        User $user,
        ?Shipment $shipment,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        $locale = in_array($user->preferred_language, ['id', 'en'], true)
            ? $user->preferred_language
            : 'id';

        [$translatedTitle, $translatedMessage] = $this->translateContent(
            $user,
            $shipment,
            $type,
            $title,
            $message,
            $data,
            $locale
        );

        $notification = $user->notifications()->create([
            'shipment_id' => $shipment?->id,
            'type' => $type,
            'title' => $translatedTitle,
            'message' => $translatedMessage,
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

    /**
     * Menerjemahkan judul dan pesan berdasarkan bahasa penerima.
     */
    private function translateContent(
        User $user,
        ?Shipment $shipment,
        string $type,
        string $title,
        string $message,
        array $data,
        string $locale
    ): array {
        // Pertahankan teks Indonesia yang sudah digunakan sistem dan pengujian.
        if ($locale === 'id') {
            return [$title, $message];
        }

        $shipmentNumber = $data['shipment_number']
            ?? $shipment?->shipment_number
            ?? '-';

        $translationType = $type;

        $replacements = [
            'shipment_number' => $shipmentNumber,
            'exception_code' => $data['exception_code'] ?? '-',
            'delay_minutes' => $data['delay_minutes'] ?? 0,
        ];

        // Notifikasi pembuatan shipment untuk Admin memiliki pesan khusus.
        if ($type === 'SHIPMENT_CREATED' && $user->role === 'admin') {
            $translationType = 'SHIPMENT_CREATED_ADMIN';

            $replacements['customer_name'] = $shipment?->customer?->name
                ?? '-';
        }

        // Terjemahkan status sebelumnya dan status terbaru.
        if ($type === 'SHIPMENT_STATUS') {
            $replacements['previous_status'] = $this->translateStatus(
                $data['previous_status'] ?? '',
                $locale
            );

            $replacements['current_status'] = $this->translateStatus(
                $data['current_status'] ?? '',
                $locale
            );
        }

        $titleKey = "notifications.types.{$translationType}_TITLE";
        $messageKey = "notifications.types.{$translationType}_MESSAGE";

        $translatedTitle = Lang::get($titleKey, [], $locale);
        $translatedMessage = Lang::get($messageKey, $replacements, $locale);

        // Jika jenis notifikasi belum memiliki terjemahan, gunakan teks asal.
        if ($translatedTitle === $titleKey) {
            $translatedTitle = $title;
        }

        if ($translatedMessage === $messageKey) {
            $translatedMessage = $message;
        }

        return [$translatedTitle, $translatedMessage];
    }

    /**
     * Menerjemahkan kode status shipment.
     */
    private function translateStatus(string $status, string $locale): string
    {
        if ($status === '') {
            return '-';
        }

        $key = "notifications.statuses.{$status}";
        $translated = Lang::get($key, [], $locale);

        return $translated === $key
            ? str_replace('_', ' ', $status)
            : $translated;
    }
}