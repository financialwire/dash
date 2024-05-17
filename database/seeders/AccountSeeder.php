<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $accountName = 'Carteira';

        $user->accounts()->create([
            'name' => $accountName,
            'slug' => str($accountName)->slug(),
        ]);
    }
}
