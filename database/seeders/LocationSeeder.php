<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Menambahkan data awal lokasi pengiriman.
     */
    public function run(): void
    {
        $locations = [
            [
                'code' => 'CGK',
                'name' => 'Soekarno-Hatta International Airport',
                'location_type' => 'AIRPORT',
                'address' => 'Bandara Soekarno-Hatta',
                'city' => 'Tangerang',
                'state_province' => 'Banten',
                'country' => 'Indonesia',
                'country_code' => 'ID',
                'latitude' => -6.1256,
                'longitude' => 106.6559,
                'is_active' => true,
            ],
            [
                'code' => 'KNO',
                'name' => 'Kualanamu International Airport',
                'location_type' => 'AIRPORT',
                'address' => 'Bandara Kualanamu',
                'city' => 'Deli Serdang',
                'state_province' => 'Sumatera Utara',
                'country' => 'Indonesia',
                'country_code' => 'ID',
                'latitude' => 3.6422,
                'longitude' => 98.8854,
                'is_active' => true,
            ],
            [
                'code' => 'BTJ',
                'name' => 'Sultan Iskandar Muda International Airport',
                'location_type' => 'AIRPORT',
                'address' => 'Bandara Sultan Iskandar Muda',
                'city' => 'Aceh Besar',
                'state_province' => 'Aceh',
                'country' => 'Indonesia',
                'country_code' => 'ID',
                'latitude' => 5.5235,
                'longitude' => 95.4204,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['code' => $location['code']],
                $location
            );
        }
    }
}
