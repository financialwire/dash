<?php

namespace Database\Seeders;

use App\Models\Transactions\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Category::create([
            'user_id' => $user->id,
            'name' => 'Salário',
            'slug' => 'salario',
            'icon' => 'fas-money-bill-wave',
            'color' => fake()->hexColor(),
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Outros',
            'slug' => 'outros',
            'icon' => 'fas-ellipsis-h',
            'color' => fake()->hexColor(),
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Cartão de Crédito',
            'slug' => 'cartao-de-credito',
            'icon' => 'fas-credit-card',
            'color' => fake()->hexColor(),
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Educação',
            'slug' => 'educacao',
            'icon' => 'fas-graduation-cap',
            'color' => fake()->hexColor(),
        ]);
    }
}
