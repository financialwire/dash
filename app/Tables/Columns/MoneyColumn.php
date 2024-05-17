<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class MoneyColumn extends TextColumn
{
    public function getState(): mixed
    {
        if (!$this->getRecord()) {
            return null;
        }

        $state = ($this->getStateUsing !== null) ?
            $this->evaluate($this->getStateUsing) :
            $this->getStateFromRecord();

        return 'R$ ' . number_format($state / 100, 2, decimal_separator: ',', thousands_separator: '.');
    }
}
