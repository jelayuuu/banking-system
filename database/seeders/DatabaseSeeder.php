<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Alice Santos', 'email' => 'alice@example.com'],
            ['name' => 'Bob Reyes', 'email' => 'bob@example.com'],
        ];

        foreach ($users as $i => $data) {
            $user = User::create([
                ...$data,
                'password' => Hash::make('password'),
            ]);

            Account::create([
                'user_id' => $user->id,
                'account_number' => 'ACC-000' . ($i + 1),
                'balance' => 1000.00,
                'minimum_balance' => 100.00,
            ]);
        }
    }
}