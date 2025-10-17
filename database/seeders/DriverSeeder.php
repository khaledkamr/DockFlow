<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'محمد علي',
                'NID' => '12345678901234',
            ],
            [
                'name' => 'حسن إبراهيم',
                'NID' => '23456789012345',
            ],
            [
                'name' => 'يوسف محمود',
                'NID' => '34567890123456',
            ]
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }
    }
}
