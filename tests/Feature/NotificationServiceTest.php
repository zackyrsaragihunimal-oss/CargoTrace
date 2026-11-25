<?php

namespace Tests\Feature;

use App\Events\NotificationCreated;
use App\Models\Shipment;
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

    public function test_english_customer_receives_translated_shipment_notification(): void
    {
        $customer = User::factory()->create();

        $customer->forceFill([
            'role' => 'customer',
            'preferred_language' => 'en',
        ])->save();

        $notification = app(NotificationService::class)->send(
            $customer,
            null,
            'SHIPMENT_CREATED',
            'Shipment baru telah dibuat',
            'Shipment CT-EN-001 telah dibuat dan menunggu proses berikutnya.',
            [
                'shipment_number' => 'CT-EN-001',
                'current_status' => 'CREATED',
            ]
        );

        $this->assertSame('New shipment created', $notification->title);

        $this->assertSame(
            'Shipment CT-EN-001 has been created.',
            $notification->message
        );
    }

    public function test_english_admin_receives_customer_name_in_shipment_notification(): void
    {
        $customer = User::factory()->create([
            'name' => 'Customer Test',
        ]);

        $admin = User::factory()->create();

        $admin->forceFill([
            'role' => 'admin',
            'preferred_language' => 'en',
        ])->save();

        $shipment = new Shipment([
            'shipment_number' => 'CT-ADMIN-001',
        ]);

        $shipment->setRelation('customer', $customer);

        $notification = app(NotificationService::class)->send(
            $admin,
            $shipment,
            'SHIPMENT_CREATED',
            'Shipment baru dibuat',
            'Shipment CT-ADMIN-001 telah dibuat untuk customer Customer Test.',
            [
                'shipment_number' => 'CT-ADMIN-001',
                'customer_id' => $customer->id,
                'current_status' => 'CREATED',
            ]
        );

        $this->assertSame(
            'New shipment created for a customer',
            $notification->title
        );

        $this->assertSame(
            'Shipment CT-ADMIN-001 has been created for customer Customer Test.',
            $notification->message
        );
    }

    public function test_english_customer_receives_translated_tracking_statuses(): void
    {
        $customer = User::factory()->create();

        $customer->forceFill([
            'role' => 'customer',
            'preferred_language' => 'en',
        ])->save();

        $notification = app(NotificationService::class)->send(
            $customer,
            null,
            'SHIPMENT_STATUS',
            'Status pengiriman diperbarui',
            'Status pengiriman CT-EN-002 berubah dari CREATED menjadi IN_TRANSIT.',
            [
                'shipment_number' => 'CT-EN-002',
                'previous_status' => 'CREATED',
                'current_status' => 'IN_TRANSIT',
            ]
        );

        $this->assertSame(
            'Shipment status updated',
            $notification->title
        );

        $this->assertSame(
            'Shipment CT-EN-002 status changed from Created to In Transit.',
            $notification->message
        );
    }
}