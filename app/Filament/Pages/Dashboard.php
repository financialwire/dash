<?php

namespace App\Filament\Pages;

use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconSize;
use Livewire\Component as Livewire;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-m-home';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filtros')
                    ->columns(4)
                    ->iconSize(IconSize::Medium)
                    ->schema([
                        Forms\Components\DatePicker::make('startDate')
                            ->label('Data inicial'),
                        Forms\Components\DatePicker::make('endDate')
                            ->label('Data final'),
                        Forms\Components\Select::make('accountId')
                            ->label('Conta')
                            ->native(false)
                            ->options(auth()->user()->accounts->pluck('name', 'id')),
                        Forms\Components\Select::make('preview')
                            ->label('Projetar')
                            ->boolean()
                            ->native(false)
                            ->hintIcon('heroicon-m-question-mark-circle')
                            ->hintIconTooltip('Ao ativar essa opção, considera todas as transações, finalizadas ou não.'),
                    ])
                    ->headerActions($this->getFiltersFormHeaderActions()),
            ]);
    }

    private function getFiltersFormHeaderActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('clearFilters')
                ->label('Limpar filtros')
                ->icon('heroicon-m-x-circle')
                ->size(ActionSize::ExtraSmall)
                ->hidden($this->checkIfFilterFormHasFilledFields())
                ->action($this->resetFilterFormFields())
        ];
    }

    private function checkIfFilterFormHasFilledFields(): Closure
    {
        return fn(Livewire $livewire) => collect($livewire->filters)
            ->filter(fn($filter) => !is_null($filter))
            ->count() == 0;
    }

    private function resetFilterFormFields(): Closure
    {
        return function (Livewire $livewire) {
            collect($livewire->filters)
                ->map(fn($filter, $key) => $livewire->filters[$key] = null);

            Notification::make()
                ->title('Filtros limpados com sucesso!')
                ->success()
                ->send();
        };
    }

    public function getColumns(): int|string|array
    {
        return 3;
    }
}