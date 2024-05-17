<?php

namespace App\Filament\Resources\Transactions\TransactionResource\Widgets;

use App\Enums\TransactionType;
use App\Models\Transactions\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TransactionsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getStats(): array
    {
        $startDate = $this->tableFilters['date']['startDate'] ?? now()->startOfMonth();
        $endDate = $this->tableFilters['date']['endDate'] ?? now()->endOfMonth();
        $categoriesIds = $this->tableFilters['category_id']['values'];
        $accountsIds = $this->tableFilters['account_id']['values'];
        $preview = $this->tableFilters['finished']['isActive'] ?? false;

        return [
            Stat::make(
                label: 'Receitas',
                value: $this->formatCurrency($this->getIncomes($startDate, $endDate, $preview, $categoriesIds, $accountsIds))
            )->icon('heroicon-m-arrow-trending-up'),

            Stat::make(
                label: 'Despesas',
                value: $this->formatCurrency($this->getExpenses($startDate, $endDate, $preview, $categoriesIds, $accountsIds))
            )->icon('heroicon-m-arrow-trending-down'),

            Stat::make(
                label: 'Saldo',
                value: $this->formatCurrency($this->getCurrentBalance($startDate, $endDate, $preview, $categoriesIds, $accountsIds))
            )->icon('heroicon-m-building-library'),
        ];
    }

    private function getTransactions($startDate, $endDate, bool $preview, array $categoriesIds, array $accountsIds, TransactionType $transactionType): Builder
    {
        $query = Transaction::where('user_id', auth()->user()->id)
            ->where('transaction_type', $transactionType)
            ->whereBetween('date', [$startDate, $endDate]);

        if (!$preview) {
            $query->where('finished', true);
        }

        if (!empty($categoriesIds)) {
            $query->whereIn('category_id', $categoriesIds);
        }

        if (!empty($accountsIds)) {
            $query->whereIn('account_id', $accountsIds);
        }

        return $query;
    }

    private function getIncomes($startDate, $endDate, bool $preview, array $categoriesIds, array $accountsIds)
    {
        return $this->getTransactions($startDate, $endDate, $preview, $categoriesIds, $accountsIds, TransactionType::Income)
            ->sum('amount');
    }

    private function getExpenses($startDate, $endDate, bool $preview, array $categoriesIds, array $accountsIds)
    {
        return $this->getTransactions($startDate, $endDate, $preview, $categoriesIds, $accountsIds, TransactionType::Expense)
            ->sum('amount');
    }

    private function getCurrentBalance($startDate, $endDate, bool $preview, array $categoriesIds, array $accountsIds)
    {
        return $this->getIncomes($startDate, $endDate, $preview, $categoriesIds, $accountsIds)
            - $this->getExpenses($startDate, $endDate, $preview, $categoriesIds, $accountsIds);
    }

    private function formatCurrency(int $currency): string
    {
        return 'R$ ' . number_format($currency / 100, 2, decimal_separator: ',', thousands_separator: '.');
    }
}
