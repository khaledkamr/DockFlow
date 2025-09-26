<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 1;
        $roles = [
            ['name' => 'مشرف', 'company_id' => $company_id],
            ['name' => 'محاسب', 'company_id' => $company_id],
            ['name' => 'امين مخازن', 'company_id' => $company_id],
            ['name' => 'موظف', 'company_id' => $company_id],
        ];

        foreach($roles as $role) {
            Role::create($role);
        }
    }
}
