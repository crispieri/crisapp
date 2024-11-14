<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel, HasColor
{
    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';

    public static function toArray(): array
    {
        return array_column(ClientType::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => __('enums.individual'),
            self::COMPANY => __('enums.company'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'primary',
            self::COMPANY => 'secondary',
        };
    }
}
