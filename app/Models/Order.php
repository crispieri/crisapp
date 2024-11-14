<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'status',
        'discount_amount',
        'coupon_id',
        'grand_total',
        'notes',
    ];

    protected $casts = [
        // 'payment_method' => PaymentStatusEnum::class,
        // 'payment_status' => PaymentMethodEnum::class,
        'status' => OrderStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Calcula el total de la orden, aplicando el descuento si el cupón es válido.
     */
    public function calculateTotal(int $subTotal): int
    {
        $discount = $this->coupon && $this->coupon->isValid()
            ? $this->coupon->calculateDiscount($subTotal)
            : 0;

        $this->discount_amount = $discount;
        return $subTotal - $discount;
    }

    /**
     * Aplica el cupón a la orden si es válido, actualizando el contador de usos del cupón.
     */
    public function applyCoupon(): void
    {
        if ($this->coupon && $this->coupon->isValid()) {
            $this->coupon->incrementUsage();
        } else {
            $this->coupon_id = null; // Elimina el cupón si no es válido
            $this->discount_amount = 0;
        }
    }

    // public function calculateTotal(int $subTotal): int
    // {
    //     $discount = $this->coupon ? $this->coupon->calculateDiscount($subTotal) : 0;
    //     $this->discount_amount = $discount;
    //     return $subTotal - $discount;
    // }
}
