<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     *
     * Role dan is_active tetap dikecualikan demi keamanan.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'preferred_language',
    ];

    /**
     * Atribut yang disembunyikan saat data pengguna diserialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Periksa apakah pengguna adalah administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Periksa apakah pengguna adalah operator logistik.
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Periksa apakah pengguna adalah customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Pengiriman yang dimiliki customer.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'customer_id');
    }

    /**
     * Pengiriman yang dibuat oleh pengguna.
     */
    public function createdShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'created_by');
    }

    /**
     * Pengiriman yang terakhir diperbarui pengguna.
     */
    public function updatedShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'updated_by');
    }

    /**
     * Tracking event yang dibuat pengguna.
     */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class, 'created_by');
    }

    /**
     * Exception yang dilaporkan pengguna.
     */
    public function reportedExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'reported_by');
    }

    /**
     * Exception yang ditugaskan kepada pengguna.
     */
    public function assignedExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'assigned_to');
    }

    /**
     * Notifikasi milik pengguna.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Catatan audit yang dibuat pengguna.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}
