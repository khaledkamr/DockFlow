<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'الأصول'],
            ['name' => 'الخصوم'],
            ['name' => 'المصاريف'],
            ['name' => 'الإيرادات'],
        ];

        foreach($types as $type) {
            AccountType::create($type);
        }
    }
}
