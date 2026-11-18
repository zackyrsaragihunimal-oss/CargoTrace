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
}
