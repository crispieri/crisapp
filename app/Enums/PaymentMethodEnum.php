<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodEnum: string implements HasLabel, HasIcon, HasColor
{
    case Cash = 'cash';
    case DebitCard = 'debit_card';
    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';

    public static function toArray(): array
    {
        return array_column(PaymentMethodEnum::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cash => __('enums.cash'),
            self::DebitCard => __('enums.debit_card'),
            self::CreditCard => __('enums.credit_card'),
            self::BankTransfer => __('enums.bank_transfer'),
            self::Cheque => __('enums.cheque'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Cash => 'success',
            self::DebitCard => 'primary',
            self::CreditCard => 'secondary',
            self::BankTransfer => 'info',
            self::Cheque => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cash => 'heroicon-o-cash',
            self::DebitCard => 'heroicon-o-credit-card',
            self::CreditCard => 'heroicon-o-credit-card',
            self::BankTransfer => 'heroicon-o-switch-horizontal',
            self::Cheque => 'heroicon-o-switch-horizontal',
        };
    }
}
