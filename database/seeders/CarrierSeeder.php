<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    /**
     * Menambahkan data awal perusahaan pengiriman.
     */
    public function run(): void
    {
        $carriers = [
            [
                'code' => 'CT-001',
                'name' => 'CargoTrace Express',
                'contact_email' => 'express@cargotrace.test',
                'contact_phone' => '+6281234567890',
                'website' => 'https://example.com',
                'is_active' => true,
            ],
            [
                'code' => 'CT-002',
                'name' => 'Nusantara Logistics',
                'contact_email' => 'info@nusantaralogistics.test',
                'contact_phone' => '+6281234567891',
                'website' => 'https://example.com',
                'is_active' => true,
            ],
            [
                'code' => 'CT-003',
                'name' => 'Global Freight Indonesia',
                'contact_email' => 'support@globalfreight.test',
                'contact_phone' => '+6281234567892',
                'website' => 'https://example.com',
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::updateOrCreate(
                ['code' => $carrier['code']],
                $carrier
            );
        }
    }
}
