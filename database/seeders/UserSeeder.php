<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'خالد قمر',
                'email' => 'kk@gmail.com',
                'password' => '111'
            ]
        ];

        foreach($users as $user) {
            User::create($user);
        }
    }
}
