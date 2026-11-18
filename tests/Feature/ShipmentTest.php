<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_shipment_uses_expected_arrival_as_initial_eta(): void
    {
        // Membuat akun operator.
        $operator = User::factory()->create();
        $operator->forceFill([
            'role' => 'operator',
            'is_active' => true,
        ])->save();

        // Membuat akun customer.
        $customer = User::factory()->create();
        $customer->forceFill([
            'role' => 'customer',
            'is_active' => true,
        ])->save();

        // Membuat carrier pengujian.
        $carrierId = DB::table('carriers')->insertGetId([
            'code' => 'TEST-CARRIER',
            'name' => 'CargoTrace Test Carrier',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat lokasi asal.
        $originId = DB::table('locations')->insertGetId([
            'name' => 'Gudang Tangerang',
            'location_type' => 'warehouse',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat lokasi tujuan.
        $destinationId = DB::table('locations')->insertGetId([
            'name' => 'Gudang Deli Serdang',
            'location_type' => 'warehouse',
            'city' => 'Deli Serdang',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mengirim permintaan pembuatan shipment.
        $response = $this->actingAs($operator)->post(
            route('shipments.store'),
            [
                'customer_id' => $customer->id,
                'carrier_id' => $carrierId,
                'origin_location_id' => $originId,
                'destination_location_id' => $destinationId,
                'cargo_type' => 'Electronic Equipment',
                'total_weight_kg' => 100,
                'total_volume_m3' => 1,
                'expected_departure_at' => '2026-11-18 08:00:00',
                'expected_arrival_at' => '2026-11-20 16:00:00',
            ]
        );

        $response->assertRedirect(route('shipments.index'));

        // Memastikan shipment berhasil dibuat.
        $shipment = Shipment::where('customer_id', $customer->id)
            ->firstOrFail();

        $this->assertEquals('CREATED', $shipment->current_status);

        // ETA awal harus mengikuti jadwal kedatangan.
        $this->assertNotNull($shipment->estimated_arrival_at);

        $this->assertEquals(
            '2026-11-20 16:00:00',
            $shipment->estimated_arrival_at->format('Y-m-d H:i:s')
        );
    }

    /**
     * Customer hanya dapat melihat shipment miliknya sendiri.
     */
    public function test_customer_only_sees_own_shipments(): void
    {
        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $otherCustomer = User::factory()->create();
        $otherCustomer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $ownShipment = $this->createShipmentForCustomer(
            $customer,
            'CT-TEST-OWN-001'
        );

        $otherShipment = $this->createShipmentForCustomer(
            $otherCustomer,
            'CT-TEST-OTHER-001'
        );

        $response = $this->actingAs($customer)->get(route('shipments.index'));

        $response->assertOk();
        $response->assertSee($ownShipment->shipment_number);
        $response->assertDontSee($otherShipment->shipment_number);
    }

    /**
     * Customer tidak boleh membuka detail shipment milik orang lain.
     */
    public function test_customer_cannot_view_another_customers_shipment(): void
    {
        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $otherCustomer = User::factory()->create();
        $otherCustomer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $shipment = $this->createShipmentForCustomer(
            $otherCustomer,
            'CT-TEST-FORBIDDEN-001'
        );

        $response = $this->actingAs($customer)->get(
            route('shipments.show', $shipment)
        );

        $response->assertForbidden();
    }

    /**
     * Customer tidak boleh membuka formulir pembuatan shipment.
     */
    public function test_customer_cannot_open_shipment_creation_form(): void
    {
        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $response = $this->actingAs($customer)->get(
            route('shipments.create')
        );

        $response->assertForbidden();
    }

    /**
     * Customer tidak boleh mengirim permintaan pembuatan shipment.
     */
    public function test_customer_cannot_create_shipment(): void
    {
        $customer = User::factory()->create();
        $customer->forceFill(['role' => 'customer', 'is_active' => true])->save();

        $response = $this->actingAs($customer)->post(
            route('shipments.store'),
            []
        );

        $response->assertForbidden();
        $this->assertDatabaseCount('shipments', 0);
    }

    /**
     * Buat shipment pengujian untuk customer tertentu.
     */
    private function createShipmentForCustomer(
        User $customer,
        string $shipmentNumber
    ): Shipment {
        $now = now();

        $carrierId = DB::table('carriers')->insertGetId([
            'code' => 'TEST-' . $shipmentNumber,
            'name' => 'Test Carrier',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $originId = DB::table('locations')->insertGetId([
            'name' => 'Test Origin',
            'location_type' => 'warehouse',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $destinationId = DB::table('locations')->insertGetId([
            'name' => 'Test Destination',
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
}
