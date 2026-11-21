<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_their_own_notifications(): void
    {
        $user = $this->createActiveUser();
        $otherUser = $this->createActiveUser();

        $ownNotification = $this->createNotification(
            $user,
            'Notifikasi milik sendiri'
        );

        $otherNotification = $this->createNotification(
            $otherUser,
            'Notifikasi milik pengguna lain'
        );

        $response = $this->actingAs($user)
            ->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee($ownNotification->title);
        $response->assertDontSee($otherNotification->title);
    }

    public function test_user_can_mark_their_own_notification_as_read(): void
    {
        $user = $this->createActiveUser();

        $notification = $this->createNotification(
            $user,
            'Notifikasi yang akan dibaca'
        );

        $response = $this->actingAs($user)
            ->patch(route('notifications.read', $notification));

        $response->assertRedirect(route('notifications.index'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = $this->createActiveUser();
        $otherUser = $this->createActiveUser();

        $notification = $this->createNotification(
            $otherUser,
            'Notifikasi rahasia pengguna lain'
        );

        $response = $this->actingAs($user)
            ->patch(route('notifications.read', $notification));

        $response->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_mark_all_read_only_updates_current_users_notifications(): void
    {
        $user = $this->createActiveUser();
        $otherUser = $this->createActiveUser();

        $ownUnread = $this->createNotification(
            $user,
            'Notifikasi pertama'
        );

        $ownUnreadSecond = $this->createNotification(
            $user,
            'Notifikasi kedua'
        );

        $otherUnread = $this->createNotification(
            $otherUser,
            'Notifikasi pengguna lain'
        );

        $response = $this->actingAs($user)
            ->patch(route('notifications.read-all'));

        $response->assertRedirect(route('notifications.index'));

        $this->assertNotNull($ownUnread->fresh()->read_at);
        $this->assertNotNull($ownUnreadSecond->fresh()->read_at);
        $this->assertNull($otherUnread->fresh()->read_at);
    }

    private function createActiveUser(): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'customer',
            'is_active' => true,
        ])->save();

        return $user;
    }

    private function createNotification(
        User $user,
        string $title
    ): Notification {
        return $user->notifications()->create([
            'shipment_id' => null,
            'type' => 'SHIPMENT_STATUS',
            'title' => $title,
            'message' => 'Pesan pengujian notifikasi.',
            'data' => null,
            'read_at' => null,
        ]);
    }
}