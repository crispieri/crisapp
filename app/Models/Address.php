<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'street_address',
        'commune',
        'city',
        'region',
        'country',
    ];

    // Relación muchos a uno inversa con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
