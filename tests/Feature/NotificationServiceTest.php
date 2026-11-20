<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_can_be_sent_and_saved_for_a_user(): void
    {
        $user = User::factory()->create();

        $notification = app(NotificationService::class)->send(
            $user,
            null,
            'SHIPMENT_STATUS',
            'Status pengiriman diperbarui',
            'Pengiriman Anda sedang dalam perjalanan.',
            ['status' => 'IN_TRANSIT']
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'shipment_id' => null,
            'type' => 'SHIPMENT_STATUS',
            'title' => 'Status pengiriman diperbarui',
            'message' => 'Pengiriman Anda sedang dalam perjalanan.',
        ]);

        $this->assertSame(
            ['status' => 'IN_TRANSIT'],
            $notification->fresh()->data
        );

        $this->assertNull($notification->fresh()->read_at);
    }
}