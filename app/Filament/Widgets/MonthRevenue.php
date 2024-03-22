<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionType;
use App\Models\Transactions\Transaction;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MonthRevenue extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Receita por mês';

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {

        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth();
        $accountId = $this->filters['accountId'] ?? null;
        $preview = $this->filters['preview'] ?? false;

        $income = $this->getPeriodAmount($startDate, $endDate, $preview, $accountId, TransactionType::Income);
        $expense = $this->getPeriodAmount($startDate, $endDate, $preview, $accountId, TransactionType::Expense);

        $expense->each(fn($period) => $period->aggregate = -$period->aggregate);

        $data = $income->concat($expense)->groupBy('new_date')->map(fn($items) => [
            'date' => $items->first()->new_date,
            'aggregate' => $items->sum('aggregate') / 100,
            'color' => $this->filamentColorToHex($items->sum('aggregate') < 0 ? Color::Red : Color::Lime)
        ]);

        Color::Red;

        $final = [
            'datasets' => [
                [
                    'label' => 'Balance',
                    'data' => $data->map(fn($period) => $period['aggregate'])->values()->toArray(),
                    'backgroundColor' => $data->map(fn($period) => $period['color'])->values()->toArray(),
                ],
            ],
            'labels' => $data->map(fn($period) => Carbon::parse($period['date'])->format('m/Y'))->values()->toArray(),
        ];

        return $final;
    }

    private function getPeriodAmount(string $startDate, string $endDate, bool $preview, ?string $accountId, TransactionType $transactionType): Collection
    {
        $query = Transaction::selectRaw("
                sum(`amount`) as `aggregate`, 
                DATE_FORMAT(`date`, '%Y-%m') AS `new_date`, 
                YEAR(`date`) AS `year`, 
                MONTH(`date`) AS `month`
            ")
            ->where('user_id', auth()->user()->id)
            ->where('transaction_type', $transactionType)
            ->whereBetween('date', [$startDate, $endDate]);

        if (!$preview) {
            $query->where('finished', true);
        }

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        return $query->groupBy('year', 'month')->get();
    }

    private function filamentColorToHex(mixed $color)
    {
        $rgb = str($color['400'])->explode(',')->toArray();
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                'scales': {
                    y: {
                        ticks: {
                            callback: function(tooltipItem, chart){

                                // console.log(tooltipItem)

                                return Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(tooltipItem);
                            }
                        }
                    },
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
                    },
                    legend: { 
                        display: false, 
                    }, 
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
