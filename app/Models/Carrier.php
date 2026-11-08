<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    /**
     * Kolom yang boleh diisi.
     */
    protected $fillable = [
        'code',
        'name',
        'contact_email',
        'contact_phone',
        'website',
        'is_active',
    ];

    /**
     * Konversi tipe data.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke seluruh pengiriman milik carrier.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
