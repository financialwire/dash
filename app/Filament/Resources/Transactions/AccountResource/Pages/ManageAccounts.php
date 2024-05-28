<?php

namespace App\Filament\Resources\Transactions\AccountResource\Pages;

use App\Filament\Resources\Transactions\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

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
