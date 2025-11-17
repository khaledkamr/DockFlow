<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyModulesSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();
        $company = Company::where('id', 3)->first();

        if ($company && $modules) {
            $company->modules()->sync($modules->pluck('id')->toArray());
        }
    }
}
