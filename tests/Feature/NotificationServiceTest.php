<?php

namespace Tests\Feature;

use App\Events\NotificationCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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

    public function test_notification_service_dispatches_event_with_correct_payload(): void
    {
        Event::fake([NotificationCreated::class]);

        $user = User::factory()->create();

        $notification = app(NotificationService::class)->send(
            $user,
            null,
            'SHIPMENT_STATUS',
            'Status pengiriman diperbarui',
            'Pengiriman Anda sedang dalam perjalanan.'
        );

        Event::assertDispatched(
            NotificationCreated::class,
            function (NotificationCreated $event) use ($user, $notification) {
                return $event->userId === $user->id
                    && $event->notification['id'] === $notification->id
                    && $event->notification['type'] === 'SHIPMENT_STATUS'
                    && $event->notification['title'] === 'Status pengiriman diperbarui'
                    && $event->notification['message'] === 'Pengiriman Anda sedang dalam perjalanan.'
                    && $event->notification['shipment_id'] === null
                    && $event->notification['shipment_number'] === null
                    && $event->notification['unread_count'] === 1;
            }
        );
    }
}
