<?php

namespace App\Filament\Exports\Transactions;

use App\Enums\TransactionType;
use App\Models\Transactions\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('transaction_type')
                ->label('Tipo')
                ->formatStateUsing(fn(TransactionType $state): ?string => $state->getLabel()),
            ExportColumn::make('amount')
                ->label('Total')
                ->formatStateUsing(fn(?int $state): string => 'R$ ' . number_format($state / 100, 2, ',', '.')),
            ExportColumn::make('date')
                ->label('Data')
                ->formatStateUsing(fn(string $state): ?string => date('d/m/Y', strtotime($state))),
            ExportColumn::make('finished')
                ->label('Finalizada')
                ->formatStateUsing(fn(bool $state): string => $state ? 'Sim' : 'Não'),
            ExportColumn::make('description')
                ->label('Descrição'),
            ExportColumn::make('account.name')
                ->label('Conta'),
            ExportColumn::make('category.name')
                ->label('Categoria'),
            ExportColumn::make('created_at')
                ->label('Criada em')
                ->formatStateUsing(fn(string $state): ?string => date('d/m/Y H:i', strtotime($state))),
            ExportColumn::make('updated_at')
                ->label('Atualizado em')
                ->formatStateUsing(fn(string $state): ?string => date('d/m/Y H:i', strtotime($state))),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Seu relatório foi realizado com sucesso e ' . number_format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' foram exportados.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('registro')->plural($failedRowsCount) . ' não foram exportados.';
        }

        return $body;
    }
}
