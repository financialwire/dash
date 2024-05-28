<?php

namespace App\Filament\Resources\Transactions\CategoryResource\Pages;

use App\Filament\Resources\Transactions\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['slug'] = str($data['name'])->slug();

                    return $data;
                }),
        ];
    }
}
