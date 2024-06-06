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
use JibayMcs\FilamentTour\Tour\HasTour;
use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;
use Livewire\Component as Livewire;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm, HasTour;

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-m-home';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filtros')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->collapsible()
                    ->collapsed()
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
                ->action($this->resetFilterFormFields()),
        ];
    }

    private function checkIfFilterFormHasFilledFields(): Closure
    {
        return fn (Livewire $livewire) => collect($livewire->filters)
            ->filter(fn ($filter) => !is_null($filter))
            ->count() == 0;
    }

    private function resetFilterFormFields(): Closure
    {
        return function (Livewire $livewire) {
            collect($livewire->filters)
                ->map(fn ($filter, $key) => $livewire->filters[$key] = null);

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

    public function tours(): array
    {
        return [
            Tour::make('dashboard')
                ->colors(light: '#000', dark: '#000')
                ->steps(
                    Step::make()
                        ->title('Bem-vindo(a) ao Financialwire!')
                        ->description('Vamos fazer um pequeno tour para você se familiarizar com a plataforma.'),

                    Step::make('.fi-avatar')
                        ->title('Perfil')
                        ->description('Personalize seu perfil e edite seus dados')
                        ->icon('heroicon-m-user-circle'),

                    Step::make('.fi-sidebar-group-items li:nth-child(2)')
                        ->title('Contas')
                        ->description('Crie suas contas, como conta corrente, conta poupança etc.')
                        ->icon('heroicon-m-wallet'),

                    Step::make('.fi-sidebar-group-items li:nth-child(3)')
                        ->title('Categorias')
                        ->description('Crie categorias para organizar as suas transações')
                        ->icon('heroicon-m-bookmark'),

                    Step::make('.fi-sidebar-group-items li:nth-child(4)')
                        ->title('Transações')
                        ->description('Registre seus ganhos e despesas para salvar na sua dashboard')
                        ->icon('heroicon-m-banknotes'),

                    Step::make('section.flex.flex-col.gap-y-8.py-8 :nth-child(2)')
                        ->title('Dashboard')
                        ->description('Resumo da sua vida financeira com gráficos dinâmicos')
                        ->icon('heroicon-m-presentation-chart-line'),

                    Step::make('header.fi-section-header')
                        ->title('Filtros')
                        ->description('Visualize suas finanças de acordo com a data que você deseja')
                        ->icon('heroicon-m-adjustments-horizontal'),

                    Step::make()
                        ->title('Fim!')
                        ->description(view('tour.greetings'))
                ),
        ];
    }
}
