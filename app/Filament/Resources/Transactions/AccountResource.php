<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\AccountResource\Pages;
use App\Filament\Resources\Transactions\AccountResource\RelationManagers;
use App\Models\Transactions\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->hidden(fn(?Account $record): bool => is_null($record))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Criado em:')
                            ->content(fn(Account $record): string => $record->created_at->format('d/m/Y H:i')),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Atualizado em:')
                            ->content(fn(Account $record): string => $record->updated_at->format('d/m/Y H:i')),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', auth()->user()->id))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
        return static::getModel()::where('user_id', auth()->user()->id)->count();
    }
}
