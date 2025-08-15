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
                'NID' => '82546872874',
                'phone' => '01006379503'
            ],
            [
                'name' => 'يوسف قمر',
                'NID' => '3797345984394',
                'phone' => '01012863099'
            ],
            [
                'name' => 'قمر رشاد',
                'NID' => '7265726439904',
                'phone' => '0593230125'
            ],
            [
                'name' => 'احمد محمد',
                'NID' => '427948284829',
                'phone' => '0123456789'
            ],
        ];

        foreach($users as $user) {
            User::create($user);
        }
    }
}
