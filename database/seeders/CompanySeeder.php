<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'شركة تاج الأعمال للخدمات اللوجستية',
                'branch' => 'فرع الرئيسي',
                'CR' => '1010771098',
                'TIN' => '7027331623',
                'vatNumber' => '312266919700003',
                'national_address' => 'الدمام',
                'phone' => '0123456789',
                'email' => 'tag@gmail.com'
            ],
            // [
            //     'name' => 'شركة مسار سريع للنقليات',
            //     'branch' => 'فرع الرياض',
            //     'CR' => '1010771099',
            //     'TIN' => '7027331624',
            //     'vatNumber' => '312266919700004',
            //     'national_address' => 'الرياض',
            //     'phone' => '0123456790',
            //     'email' => 'fast@gmail.com'
            // ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
