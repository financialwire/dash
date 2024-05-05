<?php

namespace App\Filament\Resources\Transactions;

use App\Enums\TransactionType;
use App\Filament\Exports\Transactions\TransactionExporter;
use App\Filament\Resources\Transactions\TransactionResource\Pages;
use App\Filament\Resources\Transactions\TransactionResource\Widgets;
use App\Models\Transactions\Account;
use App\Models\Transactions\Category;
use App\Models\Transactions\Transaction;
use App\Tables\Columns\MoneyColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentPtbrFormFields\Money;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-m-banknotes';

    protected static ?string $modelLabel = 'transação';

    protected static ?string $pluralModelLabel = 'transações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transação')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label('Conta')
                            ->relationship(
                                name: 'account',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('user_id', auth()->user()->id)
                            )
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('user_id', auth()->user()->id)
                            )
                            ->native(false)
                            ->required(),

                        Money::make('amount')
                            ->label('Total')
                            ->required()
                            ->formatStateUsing(fn (?int $state) => number_format($state / 100, 2, ',', '.'))
                            ->dehydrateStateUsing(fn (?string $state): ?int => str($state)->replace(['.', ','], '')->toInteger()),
                        Forms\Components\DatePicker::make('date')
                            ->label('Data')
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('Descrição')
                            ->placeholder('Ex: Conta de Luz')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\ToggleButtons::make('transaction_type')
                            ->label('Tipo de transação')
                            ->required()
                            ->inline()
                            ->options(TransactionType::class),

                        Forms\Components\ToggleButtons::make('finished')
                            ->label('Recebida/Paga')
                            ->required()
                            ->inline()
                            ->boolean(),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('Anexo')
                            ->openable()
                            ->deletable()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->columnSpanFull(),

                        Forms\Components\Fieldset::make('Informações Adicionais')
                            ->columnSpanFull()
                            ->hidden(fn (?Transaction $record): bool => $record === null)
                            ->columns(2)
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Criado em')
                                    ->content(fn ($record) => $record->created_at->format('d/m/Y H:i')),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Atualizado em')
                                    ->content(fn ($record) => $record->updated_at->format('d/m/Y H:i')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->user()->id))
            ->defaultSort('date', 'desc')
            ->defaultGroup('date')
            ->columns(
                $livewire->isGridLayout()
                    ? static::getGridTableColumns()
                    : static::getTableColumns()
            )
            ->contentGrid(
                fn () => $livewire->isTableLayout()
                    ? null
                    : [
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ]
            )
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('startDate')
                            ->label('Data inicial')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('endDate')
                            ->label('Data final')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->whereBetween('date', [$data['startDate'] ?? now()->startOfMonth(), $data['endDate'] ?? now()->endOfMonth()])),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categorias')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('user_id', auth()->user()->id)
                    )
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('account_id')
                    ->label('Contas')
                    ->relationship(
                        name: 'account',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('user_id', auth()->user()->id)
                    )
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('finished')
                    ->label('Finalizada')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('finished', true)),
            ])
            ->groups([
                Tables\Grouping\Group::make('date')
                    ->label('Data')
                    ->getTitleFromRecordUsing(fn ($record): ?string => date('d/m/Y', strtotime($record->date))),
                Tables\Grouping\Group::make('category_id')
                    ->label('Categoria')
                    ->getTitleFromRecordUsing(fn (?Transaction $record): ?string => Category::find($record->category_id)->name),
                Tables\Grouping\Group::make('account_id')
                    ->label('Conta')
                    ->getTitleFromRecordUsing(fn (?Transaction $record): ?string => Account::find($record->account_id)->name),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([
                9,
                18,
                36,
                'all',
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\ExportBulkAction::make()
                    ->label('Exportar')
                    ->exporter(TransactionExporter::class)
                    ->icon('heroicon-s-document-arrow-up')
                    ->color('primary'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getGridTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\Layout\Grid::make([
                    'lg' => 2,
                ])
                    ->schema([
                        Tables\Columns\TextColumn::make('date')
                            ->label('Data')
                            ->date('d/m/Y')
                            ->sortable()
                            ->badge(),
                        Tables\Columns\TextColumn::make('transaction_type')
                            ->label('Tipo')
                            ->badge()
                            ->iconPosition(IconPosition::After)
                            ->alignEnd(),

                        Tables\Columns\TextColumn::make('description')
                            ->label('Descrição')
                            ->searchable()
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large),
                        Tables\Columns\TextColumn::make('category.name')
                            ->label('Categoria')
                            ->searchable()
                            ->badge()
                            ->icon(fn ($record) => $record->category->icon)
                            ->color(fn ($record) => Color::hex($record->category->color))
                            ->alignEnd(),

                        MoneyColumn::make('amount')
                            ->label('Total')
                            ->numeric()
                            ->sortable()
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Medium),
                        Tables\Columns\TextColumn::make('account.name')
                            ->label('Conta')
                            ->searchable()
                            ->alignEnd()
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Medium),
                    ]),
            ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\ToggleColumn::make('finished')
                ->label('Finalizada')
                ->alignCenter(),
            Tables\Columns\TextColumn::make('date')
                ->label('Data')
                ->date('d/m/Y')
                ->badge()
                ->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->label('Descrição')
                ->searchable(),
            MoneyColumn::make('amount')
                ->label('Total')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('category.name')
                ->label('Categoria')
                ->searchable()
                ->badge()
                ->icon(fn ($record) => $record->category->icon)
                ->color(fn ($record) => Color::hex($record->category->color)),
            Tables\Columns\TextColumn::make('transaction_type')
                ->label('Tipo')
                ->badge()
                ->iconPosition(IconPosition::After)
                ->alignCenter(),
            Tables\Columns\TextColumn::make('account.name')
                ->label('Conta')
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Atualizado em')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\TransactionsOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', auth()->user()->id)->count();
    }
}
