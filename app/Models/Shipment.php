<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    /**
     * Kolom yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_location_id',
        'destination_location_id',
        'cargo_type',
        'total_weight_kg',
        'total_volume_m3',
        'expected_departure_at',
        'expected_arrival_at',
        'estimated_arrival_at',
        'actual_departure_at',
        'actual_arrival_at',
        'current_status',
        'created_by',
        'updated_by',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected function casts(): array
    {
        return [
            'total_weight_kg' => 'decimal:3',
            'total_volume_m3' => 'decimal:3',
            'expected_departure_at' => 'datetime',
            'expected_arrival_at' => 'datetime',
            'estimated_arrival_at' => 'datetime',
            'actual_departure_at' => 'datetime',
            'actual_arrival_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi ke carrier.
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Relasi ke lokasi asal.
     */
    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    /**
     * Relasi ke lokasi tujuan.
     */
    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    /**
     * Relasi ke pembuat data.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke pengguna yang terakhir memperbarui data.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke detail barang dalam pengiriman.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Relasi ke riwayat tracking.
     */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }

    /**
     * Relasi ke masalah atau pengecualian pengiriman.
     */
    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class);
    }
}
