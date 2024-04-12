<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionType;
use App\Models\Transactions\Category;
use App\Models\User;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CategoriesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Receita por Categoria';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth();
        $accountId = $this->filters['accountId'] ?? false;
        $preview = $this->filters['preview'] ?? false;

        $data = [];
        $backgroundColor = [];
        $labels = [];

        $user = User::find(auth()->user()->id);

        $categories = $user->categories()->whereHas('transactions')->orderBy('name')->get();

        foreach ($categories as $category) {

            $income = $this->getCategoryAmount($category, $startDate, $endDate, $preview, $accountId, TransactionType::Income);
            $expense = $this->getCategoryAmount($category, $startDate, $endDate, $preview, $accountId, TransactionType::Expense);

            $data[] = ($income - $expense) / 100;
            $backgroundColor[] = $category->color;
            $labels[] = $category->name;
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {display: false},
                    x: {display: false},
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem, chart){
                                var datasetLabel = tooltipItem.label || '';

                                return datasetLabel + ': ' + Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(tooltipItem.raw);
                            }
                        }
                    }
                }
            }
        JS);
    }

    private function getCategoryAmount(Category $category, string $startDate, string $endDate, bool $preview, ?string $accountId, TransactionType $transactionType): int
    {
        $query = $category
            ->transactions()
            ->where('transaction_type', $transactionType)
            ->whereBetween('date', [$startDate, $endDate]);

        if (!$preview) {
            $query->where('finished', true);
        }

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        return $query->sum('amount');
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
