<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'payment_method',
        'amount',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
