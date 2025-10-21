<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'type' => 'مرسيدس',
                'plate_number' => 'أ ب ج 1234',
            ],
            [
                'type' => 'مرسيدس',
                'plate_number' => 'د هـ و 5678',
            ],
            [
                'type' => 'مرسيدس',
                'plate_number' => 'ز ح ط 9012',
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
