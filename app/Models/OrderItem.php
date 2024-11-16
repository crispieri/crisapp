<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_amount',
        'sub_total',
    ];

    // public function unitAmount(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn($value) => number_format($value, 0, ',', '.'), // Miles con punto
    //         // set: fn($value) => str_replace(['.', ','], ['', '.'], $value), // Limpieza al guardar
    //     );
    // }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
