<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExceptionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Customer hanya melihat exception dari shipment miliknya.
     */
    public function test_customer_only_sees_own_shipment_exceptions(): void
    {
        $customer = $this->createUser('customer');
        $otherCustomer = $this->createUser('customer');

        $ownShipment = $this->createShipment(
            $customer,
            'CT-EXCEPTION-OWN-001'
        );

        $otherShipment = $this->createShipment(
            $otherCustomer,
            'CT-EXCEPTION-OTHER-001'
        );

        $ownException = $this->createException($ownShipment);
        $otherException = $this->createException($otherShipment);

        $response = $this->actingAs($customer)
            ->get(route('exceptions.index'));

        $response->assertOk();
        $response->assertSee($ownException->exception_code);
        $response->assertDontSee($otherException->exception_code);
    }

    /**
     * Customer tidak boleh menugaskan atau mengubah exception.
     */
    public function test_customer_cannot_assign_or_update_exception(): void
    {
        $customer = $this->createUser('customer');
        $shipment = $this->createShipment(
            $customer,
            'CT-EXCEPTION-CUSTOMER-001'
        );
        $exception = $this->createException($shipment);

        $operator = $this->createUser('operator');

        $this->actingAs($customer)
            ->patch(route('exceptions.assign', $exception), [
                'assigned_to' => $operator->id,
            ])
            ->assertForbidden();

        $this->actingAs($customer)
            ->patch(route('exceptions.update', $exception), [
                'status' => 'RESOLVED',
                'resolution_notes' => 'Selesai.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'status' => 'OPEN',
            'assigned_to' => null,
        ]);
    }

    /**
     * Admin dapat menugaskan exception kepada operator aktif.
     */
    public function test_admin_can_assign_exception_to_active_operator(): void
    {
        $admin = $this->createUser('admin');
        $operator = $this->createUser('operator');
        $customer = $this->createUser('customer');

        $shipment = $this->createShipment(
            $customer,
            'CT-EXCEPTION-ASSIGN-001'
        );

        $exception = $this->createException($shipment);

        $response = $this->actingAs($admin)
            ->patch(route('exceptions.assign', $exception), [
                'assigned_to' => $operator->id,
            ]);

        $response->assertRedirect(route('exceptions.index'));

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'assigned_to' => $operator->id,
        ]);

        $auditLog = AuditLog::where('action', 'EXCEPTION_ASSIGNED')
            ->where('entity_type', 'ShipmentException')
            ->where('entity_id', $exception->id)
            ->firstOrFail();

        $this->assertEquals($admin->id, $auditLog->user_id);
        $this->assertNull($auditLog->old_values['assigned_to']);
        $this->assertEquals(
            $operator->id,
            $auditLog->new_values['assigned_to']
        );
    }

    /**
     * Operator tidak boleh mengubah exception milik operator lain.
     */
    public function test_operator_cannot_update_another_operators_exception(): void
    {
        $assignedOperator = $this->createUser('operator');
        $otherOperator = $this->createUser('operator');
        $customer = $this->createUser('customer');

        $shipment = $this->createShipment(
            $customer,
            'CT-EXCEPTION-ACCESS-001'
        );

        $exception = $this->createException(
            $shipment,
            $assignedOperator
        );

        $this->actingAs($otherOperator)
            ->patch(route('exceptions.update', $exception), [
                'status' => 'RESOLVED',
                'resolution_notes' => 'Mencoba menyelesaikan exception lain.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'status' => 'OPEN',
            'assigned_to' => $assignedOperator->id,
        ]);
    }

    /**
     * Operator yang ditugaskan dapat menyelesaikan exception.
     * Aktivitas penyelesaian harus masuk audit log.
     */
    public function test_assigned_operator_can_resolve_exception_with_audit_log(): void
    {
        $operator = $this->createUser('operator');
        $customer = $this->createUser('customer');

        $shipment = $this->createShipment(
            $customer,
            'CT-EXCEPTION-RESOLVE-001'
        );

        $exception = $this->createException(
            $shipment,
            $operator
        );

        $response = $this->actingAs($operator)
            ->patch(route('exceptions.update', $exception), [
                'status' => 'RESOLVED',
                'resolution_notes' => 'Kendala telah ditangani dan pengiriman dapat dilanjutkan.',
            ]);

        $response->assertRedirect(route('exceptions.index'));

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'status' => 'RESOLVED',
            'assigned_to' => $operator->id,
            'resolution_notes' => 'Kendala telah ditangani dan pengiriman dapat dilanjutkan.',
        ]);

        $updatedException = $exception->fresh();

        $this->assertNotNull($updatedException->resolved_at);

        $auditLog = AuditLog::where('action', 'EXCEPTION_STATUS_UPDATED')
            ->where('entity_type', 'ShipmentException')
            ->where('entity_id', $exception->id)
            ->firstOrFail();

        $this->assertEquals($operator->id, $auditLog->user_id);
        $this->assertEquals('OPEN', $auditLog->old_values['status']);
        $this->assertEquals('RESOLVED', $auditLog->new_values['status']);
        $this->assertEquals(
            'Kendala telah ditangani dan pengiriman dapat dilanjutkan.',
            $auditLog->new_values['resolution_notes']
        );
    }

    /**
     * Membuat akun pengujian dengan role tertentu.
     */
    private function createUser(string $role): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => $role,
            'is_active' => true,
        ])->save();

        return $user;
    }

    /**
     * Membuat shipment pengujian.
     */
    private function createShipment(
        User $customer,
        string $shipmentNumber
    ): Shipment {
        $now = now();

        $carrierId = DB::table('carriers')->insertGetId([
            'code' => 'TEST-' . $shipmentNumber,
            'name' => 'Exception Test Carrier',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $originId = DB::table('locations')->insertGetId([
            'name' => 'Exception Test Origin',
            'location_type' => 'warehouse',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $destinationId = DB::table('locations')->insertGetId([
            'name' => 'Exception Test Destination',
            'location_type' => 'warehouse',
            'city' => 'Medan',
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
            'cargo_type' => 'Test Cargo',
            'expected_departure_at' => '2026-11-18 08:00:00',
            'expected_arrival_at' => '2026-11-20 16:00:00',
            'estimated_arrival_at' => '2026-11-20 16:00:00',
            'current_status' => 'CREATED',
        ]);
    }

    /**
     * Membuat exception pengujian.
     */
    private function createException(
        Shipment $shipment,
        ?User $operator = null
    ): ShipmentException {
        return ShipmentException::create([
            'shipment_id' => $shipment->id,
            'reported_by' => null,
            'assigned_to' => $operator?->id,
            'exception_code' => 'TEST-EXCEPTION-' . $shipment->id,
            'title' => 'Pengujian Kendala Pengiriman',
            'description' => 'Exception untuk pengujian otomatis.',
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
            'detected_at' => now(),
        ]);
    }
}