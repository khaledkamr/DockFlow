<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'مديول النقل',
                'slug' => 'نقل'
            ],
            [
                'name' => 'مديول التخليص الجمركي',
                'slug' => 'تخليص'
            ],
            [
                'name' => 'مديول تخزين الحاويات',
                'slug' => 'تخزين'
            ]
        ];

        foreach($modules as $module) {
            Module::create($module);
        }
    }
}
