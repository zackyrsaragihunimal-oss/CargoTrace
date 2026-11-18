<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TrackingEventTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;
    private Shipment $shipment;
    private int $destinationId;

    protected function setUp(): void
    {
        parent::setUp();

        // Membuat akun operator khusus untuk pengujian.
        $this->operator = User::factory()->create();
        $this->operator->forceFill([
            'role' => 'operator',
            'is_active' => true,
        ])->save();

        // Membuat lokasi asal.
        $originId = DB::table('locations')->insertGetId([
            'name' => 'Gudang Tangerang',
            'location_type' => 'warehouse',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat lokasi tujuan.
        $this->destinationId = DB::table('locations')->insertGetId([
            'name' => 'Gudang Deli Serdang',
            'location_type' => 'warehouse',
            'city' => 'Deli Serdang',
            'country' => 'Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat carrier khusus pengujian.
        $carrierId = DB::table('carriers')->insertGetId([
            'code' => 'TEST-CARRIER',
            'name' => 'CargoTrace Test Carrier',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat shipment uji.
        $this->shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-001',
            'customer_id' => $this->operator->id,
            'carrier_id' => $carrierId,
            'origin_location_id' => $originId,
            'destination_location_id' => $this->destinationId,
            'cargo_type' => 'Electronic Equipment',
            'expected_departure_at' => '2026-11-18 08:00:00',
            'expected_arrival_at' => '2026-11-20 16:00:00',
            'current_status' => 'IN_TRANSIT',
            'created_by' => $this->operator->id,
        ]);
    }

    private function submitTracking(
        string $eventType,
        string $location,
        string $selectedStatus = 'IN_TRANSIT',
        string $eventAt = '2026-11-18 15:00:00'
    ): void {
        $this->actingAs($this->operator)
            ->post(route('shipments.tracking.store', $this->shipment), [
                'event_type' => $eventType,
                'status' => $selectedStatus,
                'event_at' => $eventAt,
                'location_name' => $location,
                'description' => 'Pengujian otomatis CargoTrace.',
            ])
            ->assertRedirect(route('shipments.show', $this->shipment));
    }

    public function test_pickup_sets_shipment_status_to_picked_up(): void
    {
        $this->shipment->update(['current_status' => 'BOOKED']);

        $this->submitTracking('PICKUP', 'Tangerang');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'PICKED_UP',
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $this->shipment->id,
            'event_type' => 'PICKUP',
            'status' => 'PICKED_UP',
        ]);
    }

    public function test_departure_sets_shipment_status_to_in_transit(): void
    {
        $this->shipment->update(['current_status' => 'PICKED_UP']);
        $this->submitTracking(
            'DEPARTURE',
            'Tangerang',
            'IN_TRANSIT',
            '2026-11-18 08:00:00'
        );

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'IN_TRANSIT',
        ]);
    }

    public function test_departure_delay_below_threshold_updates_eta_without_exception(): void
    {
        $this->shipment->update(['current_status' => 'PICKED_UP']);

        $this->submitTracking(
            'DEPARTURE',
            'Tangerang',
            'IN_TRANSIT',
            '2026-11-18 08:59:00'
        );

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'estimated_arrival_at' => '2026-11-20 16:59:00',
        ]);

        $this->assertDatabaseMissing('shipment_exceptions', [
            'shipment_id' => $this->shipment->id,
            'exception_code' => 'DEPARTURE_DELAY',
        ]);
    }

    public function test_departure_delay_at_threshold_creates_exception(): void
    {
        $this->shipment->update(['current_status' => 'PICKED_UP']);

        $this->submitTracking(
            'DEPARTURE',
            'Tangerang',
            'IN_TRANSIT',
            '2026-11-18 09:00:00'
        );

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'estimated_arrival_at' => '2026-11-20 17:00:00',
        ]);

        $this->assertDatabaseHas('shipment_exceptions', [
            'shipment_id' => $this->shipment->id,
            'exception_code' => 'DEPARTURE_DELAY',
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
        ]);

        $this->assertDatabaseCount('shipment_exceptions', 1);
    }

    public function test_departure_delay_of_eight_hours_updates_eta_and_creates_exception(): void
    {
        $this->shipment->update(['current_status' => 'PICKED_UP']);

        $this->submitTracking(
            'DEPARTURE',
            'Tangerang',
            'IN_TRANSIT',
            '2026-11-18 16:00:00'
        );

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'actual_departure_at' => '2026-11-18 16:00:00',
            'estimated_arrival_at' => '2026-11-21 00:00:00',
        ]);

        $this->assertDatabaseHas('shipment_exceptions', [
            'shipment_id' => $this->shipment->id,
            'exception_code' => 'DEPARTURE_DELAY',
            'description' => 'Keberangkatan terlambat 480 menit dari jadwal.',
            'status' => 'OPEN',
        ]);

        $this->assertDatabaseCount('shipment_exceptions', 1);
    }

    public function test_arrival_at_transit_location_keeps_in_transit_status(): void
    {
        $this->submitTracking('ARRIVAL', 'Medan');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'IN_TRANSIT',
            'actual_arrival_at' => null,
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $this->shipment->id,
            'event_type' => 'ARRIVAL',
            'status' => 'IN_TRANSIT',
            'location_name' => 'Medan',
        ]);
    }

    public function test_arrival_at_destination_sets_arrived_status(): void
    {
        $this->submitTracking('ARRIVAL', 'Deli Serdang');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'ARRIVED',
            'actual_arrival_at' => '2026-11-18 15:00:00',
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $this->shipment->id,
            'event_type' => 'ARRIVAL',
            'status' => 'ARRIVED',
        ]);
    }

    public function test_delivery_changes_arrived_shipment_to_delivered(): void
    {
        $this->shipment->update(['current_status' => 'ARRIVED']);
        $this->submitTracking('DELIVERY', 'Deli Serdang');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'DELIVERED',
        ]);
    }

    public function test_inspection_preserves_current_shipment_status(): void
    {
        $this->submitTracking('INSPECTION', 'Medan');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'IN_TRANSIT',
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $this->shipment->id,
            'event_type' => 'INSPECTION',
            'status' => 'IN_TRANSIT',
        ]);
    }

    public function test_tracking_rejects_event_earlier_than_last_event(): void
    {
        DB::table('tracking_events')->insert([
            'shipment_id' => $this->shipment->id,
            'created_by' => $this->operator->id,
            'event_type' => 'DEPARTURE',
            'status' => 'IN_TRANSIT',
            'event_at' => '2026-11-18 16:00:00',
            'location_name' => 'Tangerang',
            'description' => 'Aktivitas tracking sebelumnya.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->operator)
            ->from(route('shipments.show', $this->shipment))
            ->post(route('shipments.tracking.store', $this->shipment), [
                'event_type' => 'INSPECTION',
                'event_at' => '2026-11-18 15:00:00',
                'location_name' => 'Medan',
                'description' => 'Pengujian kronologi.',
            ])
            ->assertSessionHasErrors('event_at');

        $this->assertDatabaseCount('tracking_events', 1);
    }

    public function test_tracking_rejects_activity_after_delivery(): void
    {
        $this->shipment->update(['current_status' => 'DELIVERED']);

        $this->actingAs($this->operator)
            ->from(route('shipments.show', $this->shipment))
            ->post(route('shipments.tracking.store', $this->shipment), [
                'event_type' => 'INSPECTION',
                'event_at' => '2026-11-18 15:00:00',
                'location_name' => 'Deli Serdang',
                'description' => 'Pengujian status terminal.',
            ])
            ->assertSessionHasErrors('event_type');

        $this->assertDatabaseCount('tracking_events', 0);
    }

    public function test_booking_changes_created_shipment_to_booked(): void
    {
        $this->shipment->update(['current_status' => 'CREATED']);

        $this->submitTracking('BOOKING', 'Tangerang');

        $this->assertDatabaseHas('shipments', [
            'id' => $this->shipment->id,
            'current_status' => 'BOOKED',
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $this->shipment->id,
            'event_type' => 'BOOKING',
            'status' => 'BOOKED',
        ]);

        // Memastikan aktivitas booking tercatat dengan status lama dan baru.
        $auditLog = AuditLog::where('action', 'TRACKING_EVENT_CREATED')
            ->where('entity_type', 'TrackingEvent')
            ->firstOrFail();

        $this->assertEquals($this->operator->id, $auditLog->user_id);
        $this->assertEquals('CREATED', $auditLog->old_values['shipment_status']);
        $this->assertEquals('BOOKED', $auditLog->new_values['shipment_status']);
        $this->assertEquals('BOOKING', $auditLog->new_values['event_type']);
    }

    public function test_pickup_is_rejected_before_booking(): void
    {
        $this->shipment->update(['current_status' => 'CREATED']);

        $this->actingAs($this->operator)
            ->from(route('shipments.show', $this->shipment))
            ->post(route('shipments.tracking.store', $this->shipment), [
                'event_type' => 'PICKUP',
                'event_at' => '2026-11-18 15:00:00',
                'location_name' => 'Tangerang',
                'description' => 'Pengujian urutan lifecycle.',
            ])
            ->assertSessionHasErrors('event_type');

        $this->assertDatabaseCount('tracking_events', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }
}
