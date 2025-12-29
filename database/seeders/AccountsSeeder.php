<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 3;

        $roots = [
            [
                "name" => "الأصول",
                "code" => "1",
                "parent_id" => null,
                "type_id" => 1,
                "level" => 1,
                "is_active" => 1,
                "company_id" => $company_id,
            ],
            [
                "name" => "الخصوم",
                "code" => "2",
                "parent_id" => null,
                "type_id" => 2,
                "level" => 1,
                "is_active" => 1,
                "company_id" => $company_id,
            ],
            [
                "name" => "المصاريف",
                "code" => "3",
                "parent_id" => null,
                "type_id" => 3,
                "level" => 1,
                "is_active" => 1,
                "company_id" => $company_id,
            ],
            [
                "name" => "الإيرادات",
                "code" => "4",
                "parent_id" => null,
                "type_id" => 4,
                "level" => 1,
                "is_active" => 1,
                "company_id" => $company_id,
            ],
        ];

        foreach ($roots as $root) {
            Account::create($root);
        }
    }
}
