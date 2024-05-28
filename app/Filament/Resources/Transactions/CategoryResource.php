<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\CategoryResource\Pages;
use App\Models\Transactions\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-m-bookmark';

    protected static ?string $modelLabel = 'categoria';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->unique(modifyRuleUsing: function (Unique $rule, $state) {
                        return $rule->where('slug', str($state)->slug());
                    }),
                Forms\Components\ColorPicker::make('color')
                    ->label('Cor')
                    ->required(),

                IconPicker::make('icon')
                    ->label('Ícone')
                    ->required()
                    ->columnSpanFull()
                    ->sets(['fontawesome-solid'])
                    ->columns([
                        'default' => 3,
                        'xl' => 6,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\IconColumn::make('icon')
                        ->alignCenter()
                        ->icon(fn (string $state): string => $state)
                        ->color(fn ($record) => Color::hex($record->color))
                        ->size(Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Nome')
                        ->searchable()
                        ->sortable()
                        ->weight(FontWeight::SemiBold)
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                        ->alignCenter(),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->paginated([
                12,
                24,
                36,
                'all',
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->native(false),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])->badge()
                    ->color('gray')
                    ->size('sm')
                    ->outlined(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
