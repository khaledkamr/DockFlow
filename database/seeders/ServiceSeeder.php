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
                'description' => 'خدمة تخزين الحاوية الواحدة في ساحتنا'
            ],
            [
                'description' => 'خدمة تنزيل وتحميل الحاوية بالكرين لدينا بالساحة'
            ],
            [
                'description' => 'خدمة تخزين الحاوية بعد المدة المتفق عليها'
            ],
            [
                'description' => 'خدمة تبديل الحاوية من شاحنة الى شاحنة'
            ],
        ];

        foreach($services as $service) {
            Service::create($service);
        }
    }
}
