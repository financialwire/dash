<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

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
