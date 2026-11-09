<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan seluruh seeder aplikasi.
     */
    public function run(): void
    {
        $this->call([
            CarrierSeeder::class,
            LocationSeeder::class,
        ]);
    }
}
