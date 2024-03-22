<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasLabel, HasIcon, HasColor
{
    case Expense = 'expense';
    case Income = 'income';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Expense => 'Despesa',
            self::Income => 'Receita'
        };
    }

    public function getPluralLabel(): ?string
    {
        return match ($this) {
            self::Expense => 'Despesas',
            self::Income => 'Receitas'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Expense => 'heroicon-m-arrow-trending-down',
            self::Income => 'heroicon-m-arrow-trending-up'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Expense => Color::Red,
            self::Income => Color::Lime
        };
    }
}