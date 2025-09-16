<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'خالد قمر',
                'email' => 'kk@gmail.com',
                'password' => Hash::make('111'),
            ],
            [
                'name' => 'Abdeltawab',
                'email' => 'aw@gmail.com',
                'password' => Hash::make('333'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user 
            );
        }
    }
}
