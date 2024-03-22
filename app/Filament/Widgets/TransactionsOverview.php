<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionType;
use App\Models\Transactions\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TransactionsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth();
        $preview = $this->filters['preview'] ?? false;

        return [
            Stat::make(
                label: 'Receitas',
                value: $this->formatCurrency($this->getIncomes($startDate, $endDate, $preview))
            )->icon('heroicon-m-arrow-trending-up'),

            Stat::make(
                label: 'Despesas',
                value: $this->formatCurrency($this->getExpenses($startDate, $endDate, $preview))
            )->icon('heroicon-m-arrow-trending-down'),

            Stat::make(
                label: 'Saldo',
                value: $this->formatCurrency($this->getCurrentBalance($startDate, $endDate, $preview))
            )->icon('heroicon-m-building-library'),
        ];
    }

    private function getTransactions($startDate, $endDate, bool $preview, TransactionType $transactionType): Builder
    {
        $query = Transaction::where('user_id', auth()->user()->id)
            ->where('transaction_type', $transactionType)
            ->whereBetween('date', [$startDate, $endDate]);

        if (!$preview) {
            $query->where('finished', true);
        }

        return $query;
    }

    private function getIncomes($startDate, $endDate, bool $preview)
    {
        return $this->getTransactions($startDate, $endDate, $preview, TransactionType::Income)
            ->sum('amount');
    }

    private function getExpenses($startDate, $endDate, bool $preview)
    {
        return $this->getTransactions($startDate, $endDate, $preview, TransactionType::Expense)
            ->sum('amount');
    }

    private function getCurrentBalance($startDate, $endDate, bool $preview)
    {
        return $this->getIncomes($startDate, $endDate, $preview) - $this->getExpenses($startDate, $endDate, $preview);
    }

    private function formatCurrency(int $currency): string
    {
        return 'R$ ' . number_format($currency / 100, 2, decimal_separator: ',', thousands_separator: '.');
    }
}
