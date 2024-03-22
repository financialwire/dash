<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transactions\Transaction;
use App\Tables\Columns\MoneyColumn;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayTransactions extends BaseWidget
{
    protected static ?string $heading = 'Transações de Hoje';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::where('user_id', auth()->user()->id)
                    ->whereDate('created_at', now()->today()->toDateString())
                    ->orderByDesc('created_at')
                    ->limit(5)
            )
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->paginated(false)
            ->searchable(false)
            ->columns([
                Tables\Columns\ToggleColumn::make('finished')
                    ->label('Finalizada')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->icon(fn($record) => $record->category->icon)
                    ->color(fn($record) => Color::hex($record->category->color))
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Tipo')
                    ->badge()
                    ->iconPosition(IconPosition::After)
                    ->alignCenter(),
                MoneyColumn::make('amount')
                    ->label('Total')
                    ->numeric(),
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Conta'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->since(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('all')
                    ->label('Ver Mais')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(TransactionResource::getUrl('index'))
            ]);
    }
}
