<?php

namespace Database\Seeders;

use App\Models\Container_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainerTypesSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 3;

        $types = [
            [
                'name' => 'حاوية 20',
                'daily_price' => 0,
                'company_id' => $company_id,
            ],
            [
                'name' => 'حاوية 40',
                'daily_price' => 0,
                'company_id' => $company_id,
            ],
            [
                'name' => 'وزن زائد',
                'daily_price' => 0,
                'company_id' => $company_id,
            ],
            [
                'name' => 'طرود LCL',
                'daily_price' => 0,
                'company_id' => $company_id,
            ],
            [
                'name' => 'حاوية مبرده',
                'daily_price' => 0,
                'company_id' => $company_id,
            ],
        ];

        foreach($types as $type) {
            Container_type::create($type);
        }
    }
}
