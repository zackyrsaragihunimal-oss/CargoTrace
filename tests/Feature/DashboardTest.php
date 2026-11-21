<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_platform_wide_statistics_and_activity(): void
    {
        $admin = $this->createUser('admin');
        $customer = $this->createUser('customer');
        $operator = $this->createUser('operator');
        $inactiveCustomer = $this->createUser('customer', false);

        $shipment = $this->createShipment(
            $customer,
            'CT-DASH-ADMIN-001',
            'IN_TRANSIT'
        );

        $this->createException($shipment, null, 'EX-ADMIN-001', 'OPEN');

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'DASHBOARD_TEST_ACTIVITY',
            'entity_type' => 'Shipment',
            'entity_id' => $shipment->id,
            'new_values' => ['status' => 'IN_TRANSIT'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Dashboard Test',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboards.admin')
            ->assertViewHas('totalShipments', 1)
            ->assertViewHas('activeUsers', 3)
            ->assertViewHas('totalCustomers', 2)
            ->assertViewHas('totalOperators', 1)
            ->assertViewHas('openExceptions', 1)
            ->assertViewHas('recentShipments', function ($shipments) use ($shipment) {
                return $shipments->contains('id', $shipment->id);
            })
            ->assertViewHas('recentActivities', function ($activities) {
                return $activities->contains('action', 'DASHBOARD_TEST_ACTIVITY');
            });

        $response->assertSee('CT-DASH-ADMIN-001')
            ->assertSee('DASHBOARD_TEST_ACTIVITY');
    }

    public function test_operator_dashboard_only_displays_assigned_exceptions_and_own_tracking_events(): void
    {
        $operator = $this->createUser('operator');
        $otherOperator = $this->createUser('operator');
        $customer = $this->createUser('customer');

        $shipment = $this->createShipment(
            $customer,
            'CT-DASH-OP-001'
        );

        $ownException = $this->createException(
            $shipment,
            $operator,
            'EX-OP-OWN-001'
        );

        $otherException = $this->createException(
            $shipment,
            $otherOperator,
            'EX-OP-OTHER-001'
        );

        $this->createTrackingEvent(
            $shipment,
            $operator,
            'OPERATOR OWN LOCATION'
        );

        $this->createTrackingEvent(
            $shipment,
            $otherOperator,
            'OTHER OPERATOR LOCATION'
        );

        $response = $this->actingAs($operator)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboards.operator')
            ->assertViewHas('assignedExceptionCount', 1)
            ->assertViewHas('openExceptionCount', 1)
            ->assertViewHas('resolvedExceptionCount', 0)
            ->assertViewHas('recentAssignedExceptions', function ($exceptions) use ($ownException, $otherException) {
                return $exceptions->contains('id', $ownException->id)
                    && !$exceptions->contains('id', $otherException->id);
            })
            ->assertViewHas('recentTrackingEvents', function ($events) {
                return $events->contains('location_name', 'OPERATOR OWN LOCATION')
                    && !$events->contains('location_name', 'OTHER OPERATOR LOCATION');
            });

        $response->assertSee('EX-OP-OWN-001')
            ->assertDontSee('EX-OP-OTHER-001')
            ->assertSee('OPERATOR OWN LOCATION')
            ->assertDontSee('OTHER OPERATOR LOCATION');
    }

    public function test_customer_dashboard_only_displays_own_shipments_exceptions_and_notifications(): void
    {
        $customer = $this->createUser('customer');
        $otherCustomer = $this->createUser('customer');

        $ownActiveShipment = $this->createShipment(
            $customer,
            'CT-DASH-CUSTOMER-OWN-001',
            'IN_TRANSIT'
        );

        $ownDeliveredShipment = $this->createShipment(
            $customer,
            'CT-DASH-CUSTOMER-OWN-002',
            'DELIVERED'
        );

        $otherShipment = $this->createShipment(
            $otherCustomer,
            'CT-DASH-CUSTOMER-OTHER-001',
            'IN_TRANSIT'
        );

        $this->createException(
            $ownActiveShipment,
            null,
            'EX-CUSTOMER-OWN-001',
            'OPEN'
        );

        $this->createException(
            $otherShipment,
            null,
            'EX-CUSTOMER-OTHER-001',
            'OPEN'
        );

        DB::table('notifications')->insert([
            'user_id' => $customer->id,
            'shipment_id' => $ownActiveShipment->id,
            'type' => 'DASHBOARD_TEST',
            'title' => 'OWN CUSTOMER NOTIFICATION',
            'message' => 'This notification belongs to the logged-in customer.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id' => $otherCustomer->id,
            'shipment_id' => $otherShipment->id,
            'type' => 'DASHBOARD_TEST',
            'title' => 'OTHER CUSTOMER NOTIFICATION',
            'message' => 'This notification belongs to another customer.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboards.customer')
            ->assertViewHas('totalShipments', 2)
            ->assertViewHas('activeShipments', 1)
            ->assertViewHas('deliveredShipments', 1)
            ->assertViewHas('openExceptions', 1)
            ->assertViewHas('unreadNotifications', 1)
            ->assertViewHas('recentShipments', function ($shipments) use ($ownActiveShipment, $ownDeliveredShipment, $otherShipment) {
                return $shipments->contains('id', $ownActiveShipment->id)
                    && $shipments->contains('id', $ownDeliveredShipment->id)
                    && !$shipments->contains('id', $otherShipment->id);
            })
            ->assertViewHas('recentNotifications', function ($notifications) {
                return $notifications->contains('title', 'OWN CUSTOMER NOTIFICATION')
                    && !$notifications->contains('title', 'OTHER CUSTOMER NOTIFICATION');
            });

        $response->assertSee('CT-DASH-CUSTOMER-OWN-001')
            ->assertSee('CT-DASH-CUSTOMER-OWN-002')
            ->assertDontSee('CT-DASH-CUSTOMER-OTHER-001')
            ->assertSee('OWN CUSTOMER NOTIFICATION')
            ->assertDontSee('OTHER CUSTOMER NOTIFICATION');
    }

    /**
     * Membuat pengguna pengujian sesuai role.
     */
    private function createUser(string $role, bool $active = true): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => $role,
            'is_active' => $active,
        ])->save();

        return $user;
    }

    /**
     * Membuat shipment dengan carrier dan dua lokasi pengujian.
     */
    private function createShipment(
        User $customer,
        string $shipmentNumber,
        string $status = 'CREATED'
    ): Shipment {
        $now = now();
        $code = substr(preg_replace('/[^A-Za-z0-9]/', '', $shipmentNumber), 0, 25);

        $carrierId = DB::table('carriers')->insertGetId([
            'code' => 'C-' . $code,
            'name' => 'Dashboard Test Carrier',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $originId = DB::table('locations')->insertGetId([
            'name' => 'Dashboard Test Origin',
            'location_type' => 'warehouse',
            'city' => 'Medan',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $destinationId = DB::table('locations')->insertGetId([
            'name' => 'Dashboard Test Destination',
            'location_type' => 'warehouse',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return Shipment::create([
            'shipment_number' => $shipmentNumber,
            'customer_id' => $customer->id,
            'carrier_id' => $carrierId,
            'origin_location_id' => $originId,
            'destination_location_id' => $destinationId,
            'cargo_type' => 'Dashboard Test Cargo',
            'expected_departure_at' => $now,
            'expected_arrival_at' => $now->copy()->addDay(),
            'estimated_arrival_at' => $now->copy()->addDay(),
            'current_status' => $status,
        ]);
    }

    /**
     * Membuat exception untuk pengujian dashboard.
     */
    private function createException(
        Shipment $shipment,
        ?User $operator,
        string $exceptionCode,
        string $status = 'OPEN'
    ) {
        $id = DB::table('shipment_exceptions')->insertGetId([
            'shipment_id' => $shipment->id,
            'assigned_to' => $operator?->id,
            'exception_code' => $exceptionCode,
            'title' => 'Dashboard Test Exception',
            'severity' => 'MEDIUM',
            'status' => $status,
            'detected_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return \App\Models\ShipmentException::findOrFail($id);
    }

    /**
     * Membuat tracking event oleh operator tertentu.
     */
    private function createTrackingEvent(
        Shipment $shipment,
        User $operator,
        string $locationName
    ): void {
        DB::table('tracking_events')->insert([
            'shipment_id' => $shipment->id,
            'created_by' => $operator->id,
            'event_type' => 'LOCATION_UPDATE',
            'status' => 'IN_TRANSIT',
            'event_at' => now(),
            'location_name' => $locationName,
            'description' => 'Dashboard test tracking event.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}