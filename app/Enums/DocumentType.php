<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: int implements HasLabel
{
    case FACTURA = 33;       // Factura Electrónica
    case BOLETA = 39;        // Boleta Electrónica
    case FACTURA_EXENTA = 34; // Factura No Afecta o Exenta Electrónica
    case NOTA_CREDITO = 61;  // Nota de Crédito Electrónica
    case NOTA_DEBITO = 56;   // Nota de Débito Electrónica

    public static function toArray(): array
    {
        return array_column(DocumentType::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FACTURA => __('enums.factura'),
            self::BOLETA => __('enums.boleta'),
            self::FACTURA_EXENTA => __('enums.factura_exenta'),
            self::NOTA_CREDITO => __('enums.nota_credito'),
            self::NOTA_DEBITO => __('enums.nota_debito'),
        };
    }
}
