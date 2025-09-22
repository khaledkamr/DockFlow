<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'description' => 'خدمة تخزين الحاوية الواحدة في ساحتنا',
                'type' => 'primary'
            ],
            [
                'description' => 'خدمة تخزين الحاوية بعد المدة المتفق عليها',
                'type' => 'primary'
            ],
            [
                'description' => 'خدمة تبديل الحاوية من شاحنة الى شاحنة',
                'type' => 'additional'
            ],
            [
                'description' => 'خدمة تفريغ الحاوية',
                'type' => 'additional'
            ],
        ];

        foreach($services as $service) {
            Service::create($service);
        }
    }
}
