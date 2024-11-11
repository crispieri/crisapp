<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: string implements HasLabel
{
    case Pending = 'pending';
    case New = 'new';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public static function toArray(): array
    {
        return array_column(OrderStatusEnum::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('enums.pending'),
            self::New => __('enums.new'),
            self::Processing => __('enums.processing'),
            self::Shipped => __('enums.shipped'),
            self::Delivered => __('enums.delivered'),
            self::Cancelled => __('enums.cancelled'),
        };
    }
}
