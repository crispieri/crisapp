<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'code',
        'type',
        'discount_value',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime', // Esto convertirá expires_at a un objeto Carbon
    ];

    /**
     * Relación con las órdenes que usan este cupón.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Verifica si el cupón está activo y no ha alcanzado su límite de uso.
     */
    public function isValid(): bool
    {
        return $this->is_active &&
            (!$this->usage_limit || $this->used_count < $this->usage_limit) &&
            (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Calcula el descuento basado en el monto dado.
     */
    public function calculateDiscount(int $amount): int
    {
        return $this->type === 'percentage'
            ? (int)($amount * ($this->discount_value / 100))
            : min($this->discount_value, $amount); // Evita un descuento mayor al monto
    }

    /**
     * Incrementa el contador de usos del cupón.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
