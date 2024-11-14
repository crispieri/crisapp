<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicDocument extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'order_id',
        'type',
        'folio',
        'total',
        'client_name',
        'client_rut',
        'status',
        'response_data',
    ];

    protected $casts = [
        'type' => DocumentType::class, // Casteo a enum
        'response_data' => 'array', // JSON de respuesta de la API del SII
    ];

    // Relación con la orden
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
