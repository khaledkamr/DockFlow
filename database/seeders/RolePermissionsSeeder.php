<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = Permission::all();
        $role = Role::where('name', 'Super Admin')->where('company_id', 2)->first();

        foreach($permissions as $permission) {
            $role->permissions()->attach($permission->id);
        }
    }
}
