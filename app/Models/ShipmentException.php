<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    protected $fillable = [
        'shipment_id',
        'tracking_event_id',
        'reported_by',
        'assigned_to',
        'exception_code',
        'title',
        'description',
        'severity',
        'status',
        'detected_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trackingEvent(): BelongsTo
    {
        return $this->belongsTo(TrackingEvent::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
