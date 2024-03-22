<?php

namespace Database\Seeders;

use App\Models\Transactions\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        $name = 'Carteira';

        Account::create([
            'user_id' => $user->id, 
            'name' => $name,
            'slug' => str($name)->slug()
        ]);
    }
}
