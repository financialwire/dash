<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\AccountResource\Pages;
use App\Models\Transactions\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-m-wallet';

    protected static ?string $modelLabel = 'conta';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Fieldset::make('Informações Adicionais')
                    ->hidden(fn (?Account $record): bool => is_null($record))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Criado em:')
                            ->content(fn (Account $record): string => $record->created_at->format('d/m/Y H:i')),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Atualizado em:')
                            ->content(fn (Account $record): string => $record->updated_at->format('d/m/Y H:i')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Nome')
                        ->searchable()
                        ->size(TextColumnSize::Large)
                        ->weight(FontWeight::SemiBold),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 4,
            ])
            ->paginated([
                12,
                24,
                36,
                'all',
            ])
            ->actionsAlignment('right')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->badge()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['slug'] = str($data['name'])->slug();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->badge(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
