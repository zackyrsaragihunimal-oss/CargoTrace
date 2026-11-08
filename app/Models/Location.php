<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** Kolom yang boleh diisi. */
    protected $fillable = [
        'code',
        'name',
        'location_type',
        'address',
        'city',
        'state_province',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'is_active',
    ];

    /** Konversi tipe data otomatis. */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    /** Pengiriman yang menggunakan lokasi ini sebagai asal. */
    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    /** Pengiriman yang menggunakan lokasi ini sebagai tujuan. */
    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }

    /** Riwayat tracking yang tercatat pada lokasi ini. */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }
}
