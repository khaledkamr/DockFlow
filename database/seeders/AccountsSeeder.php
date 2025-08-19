<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Level 1 Roots
        $roots = [
            ['name' => 'الأصول',   'code' => '1', 'type_id' => 1],
            ['name' => 'الخصوم',   'code' => '2', 'type_id' => 2],
            ['name' => 'المصاريف', 'code' => '3', 'type_id' => 3],
            ['name' => 'الإيرادات','code' => '4', 'type_id' => 4],
        ];

        foreach ($roots as $root) {
            Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => null,
                'level'     => 1,
                'is_active' => true,
            ]);
        }

        // level 2
        $roots = [
            ['name' => 'اصول متداولة', 'code' => '101', 'type_id' => 1, 'parent_id' => 1],
            ['name' => 'اصول ثابتة', 'code' => '102', 'type_id' => 1, 'parent_id' => 1],
            ['name' => 'ارصدة مدينة اخرى', 'code' => '103', 'type_id' => 1, 'parent_id' => 1],
            ['name' => 'خصوم قصيرة الأجل', 'code' => '201', 'type_id' => 2, 'parent_id' => 2],
            ['name' => 'خصوم طويلة الأجل', 'code' => '202', 'type_id' => 2, 'parent_id' => 2],
            ['name' => 'أرصدة دائنة اخرى', 'code' => '203', 'type_id' => 2, 'parent_id' => 2],
            ['name' => 'حقوق الملكية', 'code' => '204', 'type_id' => 2, 'parent_id' => 2],
            ['name' => 'المصروفات', 'code' => '301', 'type_id' => 3, 'parent_id' => 3],
        ];

        foreach($roots as $root) {
            Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => $root['parent_id'],
                'level'     => 2,
                'is_active' => true
            ]);
        };

        // level 3
        $roots = [
            ['name' => 'المصروفات', 'code' => '30101', 'type_id' => 3, 'parent_id' => 12],
        ];

        foreach($roots as $root) {
            Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => $root['parent_id'],
                'level'     => 3,
                'is_active' => true
            ]);
        };

        // level 4
        $roots = [
            ['name' => 'مصاريف التشغيل', 'code' => '3010101', 'type_id' => 3, 'parent_id' => 13],
            ['name' => 'المصاريف العمومية', 'code' => '3010102', 'type_id' => 3, 'parent_id' => 13],
        ];

        foreach($roots as $root) {
            Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => $root['parent_id'],
                'level'     => 4,
                'is_active' => true
            ]);
        };

        // level 5 
        $roots = [
            ['name' => 'محروقات', 'code' => '30101010001', 'type_id' => 3, 'parent_id' => 14],
            ['name' => 'رواتب و اجور', 'code' => '30101020001', 'type_id' => 3, 'parent_id' => 15],
        ];

        foreach($roots as $root) {
            Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => $root['parent_id'],
                'level'     => 5,
                'is_active' => true
            ]);
        };
    }
}
