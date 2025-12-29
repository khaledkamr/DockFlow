<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 3;

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
            [
                'description' => 'اجور تخليص',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور عمال',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمات سابر',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة20/40',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمة تخزين حاوية 20 قدم بعد المدة المتفق عليها',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'خدمة تخزين حاوية 40 قدم بعد المدة المتفق عليها',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الدمام الى الدمام',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الدمام الى الدمام',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الدمام الى الدمام',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الدمام الى الدمام',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الدمام الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الدمام الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الدمام الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الدمام الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الدمام الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الدمام الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الدمام الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الدمام الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الدمام الى الخرج',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الدمام الى الخرج',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الدمام الى الخرج',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الدمام الى الخرج',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الدمام الى ملهم',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الدمام الى ملهم',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الدمام الى ملهم',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الدمام الى ملهم',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الرياض الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الرياض الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الرياض الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الرياض الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من الرياض الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من الرياض الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من الرياض الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من الرياض الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من جدة الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من جدة الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من جدة الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من جدة الى جدة',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية فئة 20/40 من جدة الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية وزن زائد من جدة الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل طرود LCL من جدة الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
            [
                'description' => 'اجور نقل حاوية مبردة من جدة الى الرياض',
                'type' => 'primary',
                'company_id' => $company_id
            ],
        ];

        foreach($services as $service) {
            Service::create($service);
        }
    }
}
