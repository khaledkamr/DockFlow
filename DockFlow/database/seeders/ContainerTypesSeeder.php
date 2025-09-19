<?php

namespace Database\Seeders;

use App\Models\Container_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainerTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'فئة 20',
                'daily_price' => 50,
            ],
            [
                'name' => 'فئة 30',
                'daily_price' => 50,
            ],
            [
                'name' => 'فئة 40',
                'daily_price' => 50,
            ],
        ];

        foreach($types as $type) {
            Container_type::create($type);
        }
    }
}
