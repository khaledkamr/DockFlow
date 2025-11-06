<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'مشرف')->where('company_id', 2)->first();
        $userRoles = [
            ['user_id' => 4, 'role_id' => $admin->id],
            // ['user_id' => 2, 'role_id' => $admin->id],
            // ['user_id' => 3, 'role_id' => $admin->id],
        ];

        foreach($userRoles as $userRole) {
            DB::table('user_roles')->updateOrInsert($userRole);
        }
    }
}
