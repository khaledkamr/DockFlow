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
            // [
            //     'name' => 'خالد قمر',
            //     'email' => 'kk@gmail.com',
            //     'password' => Hash::make('111'),
            //     'company_id' => 1
            // ],
            // [
            //     'name' => 'أدمن',
            //     'email' => 'admin@gmail.com',
            //     'password' => Hash::make('123456789'),
            //     'company_id' => 1
            // ],
            [
                'name' => 'خالد قمر',
                'email' => 'kkm@gmail.com',
                'password' => Hash::make('111'),
                'company_id' => 2
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
