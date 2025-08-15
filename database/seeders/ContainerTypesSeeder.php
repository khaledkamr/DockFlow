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
                'name' => 'فئة صغيرة',
                'daily_price' => 100,
            ],
            [
                'name' => 'فئة متوسطة',
                'daily_price' => 150,
            ],
            [
                'name' => 'فئة كبيرة',
                'daily_price' => 200,
            ],
        ];

        foreach($types as $type) {
            Container_type::create($type);
        }
    }
}
