<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case PENDING = 'pending';
    case NEW = 'new';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public static function toArray(): array
    {
        return array_column(OrderStatusEnum::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('enums.pending'),
            self::NEW => __('enums.new'),
            self::PROCESSING => __('enums.processing'),
            self::SHIPPED => __('enums.shipped'),
            self::DELIVERED => __('enums.delivered'),
            self::CANCELLED => __('enums.cancelled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::NEW => 'primary',
            self::PROCESSING => 'info',
            self::SHIPPED => 'warning',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::NEW => 'heroicon-o-sparkles',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::SHIPPED => 'heroicon-o-truck',
            self::DELIVERED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }
}
