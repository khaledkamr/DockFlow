<?php

namespace Database\Seeders;

use App\Models\Places;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlacesSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 1;
        $places = [
            [
                'name' => 'الميناء',
                'company_id' => $company_id,
            ],
            [
                'name' => 'الساحة',
                'company_id' => $company_id,
            ],
            [
                'name' => 'الرياض',
                'company_id' => $company_id,
            ],
            [
                'name' => 'الخرج',
                'company_id' => $company_id,
            ],
            [
                'name' => 'الدمام',
                'company_id' => $company_id,
            ],
            [
                'name' => 'القطيف',
                'company_id' => $company_id,
            ]
        ];

        foreach($places as $place) {
            Places::create($place);
        }
    }
}
