<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 1;

        $services = [
            [
                'description' => 'خدمة تخزين الحاوية الواحدة في ساحتنا',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمة تخزين الحاوية بعد المدة المتفق عليها',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمة تبديل الحاوية من شاحنة الى شاحنة',
                'type' => 'additional',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمة تفريغ الحاوية',
                'type' => 'additional',
                'company_id' => $company_id
            ],
        ];

        foreach($services as $service) {
            Service::create($service);
        }
    }
}
