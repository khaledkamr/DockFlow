<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'شركة تاج الأعمال للخدمات اللوجستية',
            'type' => 'شركة',
            'branch' => 'فرع الرئيسي',
            'CR' => '1010771098',
            'TIN' => '7027331623',
            'national_address' => 'الدمام',
        ]);      
    }
}
