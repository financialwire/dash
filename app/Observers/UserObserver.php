<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $accountName = 'Carteira';

        $user->accounts()->create([
            'name' => $accountName,
            'slug' => str($accountName)->slug(),
        ]);

        $user->categories()->createMany([
            [
                'name' => 'Cartão de Crédito',
                'slug' => 'cartao-de-credito',
                'icon' => 'fas-credit-card',
                'color' => fake()->hexColor(),
            ],
            [
                'name' => 'Salário',
                'slug' => 'salario',
                'icon' => 'fas-money-bill-wave',
                'color' => fake()->hexColor(),
            ],
            [
                'name' => 'Educação',
                'slug' => 'educacao',
                'icon' => 'fas-graduation-cap',
                'color' => fake()->hexColor(),
            ],
            [
                'name' => 'Outros',
                'slug' => 'outros',
                'icon' => 'fas-ellipsis-h',
                'color' => fake()->hexColor(),
            ],
        ]);
    }
}
