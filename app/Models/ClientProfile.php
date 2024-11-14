<?php

namespace App\Models;

use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    /** @use HasFactory<\Database\Factories\ClientProfileFactory> */
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'client_type',
        'rut',
        'business_name',
        'giro',
    ];

    // Relación uno a uno inversa con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
