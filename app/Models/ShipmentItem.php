<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id',
        'item_name',
        'description',
        'quantity',
        'unit_weight_kg',
        'unit_volume_m3',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_weight_kg' => 'decimal:3',
            'unit_volume_m3' => 'decimal:3',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
