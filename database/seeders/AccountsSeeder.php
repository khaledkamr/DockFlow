<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Level 1 Roots (من account_types مباشرة)
        $roots = [
            ['name' => 'الأصول',   'code' => '1', 'type_id' => 1],
            ['name' => 'الخصوم',   'code' => '2', 'type_id' => 2],
            ['name' => 'المصاريف', 'code' => '3', 'type_id' => 3],
            ['name' => 'الإيرادات','code' => '4', 'type_id' => 4],
        ];

        foreach ($roots as $root) {
            $rootAccount = Account::create([
                'name'      => $root['name'],
                'code'      => $root['code'],
                'type_id'   => $root['type_id'],
                'parent_id' => null,
                'level'     => 1,
                'is_active' => true,
            ]);

            // // Level 2
            // $sub1 = Account::create([
            //     'name'      => $root['name'].' متداولة',
            //     'code'      => $root['code'].'1',
            //     'type_id'   => $root['type_id'],
            //     'parent_id' => $rootAccount->id,
            //     'level'     => 2,
            //     'is_active' => true,
            // ]);

            // // Level 3
            // $sub2 = Account::create([
            //     'name'      => $root['name'].' فرعي',
            //     'code'      => $root['code'].'11',
            //     'type_id'   => $root['type_id'],
            //     'parent_id' => $sub1->id,
            //     'level'     => 3,
            //     'is_active' => true,
            // ]);

            // // Level 4
            // $sub3 = Account::create([
            //     'name'      => $root['name'].' تفصيلي',
            //     'code'      => $root['code'].'111',
            //     'type_id'   => $root['type_id'],
            //     'parent_id' => $sub2->id,
            //     'level'     => 4,
            //     'is_active' => true,
            // ]);

            // // Level 5
            // Account::create([
            //     'name'      => $root['name'].' جزئي',
            //     'code'      => $root['code'].'1111',
            //     'type_id'   => $root['type_id'],
            //     'parent_id' => $sub3->id,
            //     'level'     => 5,
            //     'is_active' => true,
            // ]);
        }
    }
}
