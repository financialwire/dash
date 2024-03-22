<?php

namespace Database\Seeders;

use App\Models\Transactions\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Transaction::factory(70)
            ->make([
                'user_id' => $user->id,
                'account_id' => $user->accounts()->first()->id,
            ])
            ->each(function ($transaction) use ($user) {
                $transaction->category_id = $user->categories()->inRandomOrder()->limit(1)->first()->id;

                $transaction->save();
            });
    }
}
