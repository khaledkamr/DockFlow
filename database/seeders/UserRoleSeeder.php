<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('name', 'Super Admin')->where('company_id', null)->first();
        $user = User::where('email', 'admin@gmail.com')->first();
        $userRoles = [
            ['user_id' => $user->id, 'role_id' => $superAdmin->id],
            // ['user_id' => 2, 'role_id' => $admin->id],
            // ['user_id' => 3, 'role_id' => $admin->id],
        ];

        foreach($userRoles as $userRole) {
            DB::table('user_roles')->updateOrInsert($userRole);
        }
    }
}
