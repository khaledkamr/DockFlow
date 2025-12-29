<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 3;
        
        $roles = [
            ['name' => 'Super Admin', 'company_id' => $company_id],
            ['name' => 'مشرف', 'company_id' => $company_id],
            ['name' => 'محاسب', 'company_id' => $company_id],
            ['name' => 'مدير مالي', 'company_id' => $company_id],
            ['name' => 'موظف تشغيل', 'company_id' => $company_id],
        ];

        foreach($roles as $role) {
            Role::create($role);
        }
    }
}
