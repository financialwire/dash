<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->accounts()->create($this->getDefaultAccount());

        $user->categories()->createMany($this->getDefaultCategories());

        Notification::sendNow($user, new UserCreated($user));
    }

    protected function getDefaultAccount()
    {
        return [
            'name' => 'Carteira',
            'slug' => 'carteira',
            'icon' => 'fas-wallet',
        ];
    }

    protected function getDefaultCategories()
    {
        return [
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
        ];
    }
}
