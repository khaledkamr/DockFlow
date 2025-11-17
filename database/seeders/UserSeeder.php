<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 3;

        $users = [
            [
                'name' => 'خالد قمر',
                'email' => 'kk.tag@gmail.com',
                'password' => Hash::make('111'),
                'company_id' => $company_id
            ],
            [
                'name' => 'قمر رشاد',
                'email' => 'kamr.tag@gmail.com',
                'password' => Hash::make('111'),
                'company_id' => $company_id
            ],
            [
                'name' => 'بكري',
                'email' => 'bakry.tag@gmail.com',
                'password' => Hash::make('111'),
                'company_id' => $company_id
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
