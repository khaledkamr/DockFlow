<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            AccountTypesSeeder::class,
            // AccountsSeeder::class,
            PrimaryAccountSeeder::class,
            UserSeeder::class,
            ContainerTypesSeeder::class,
            ServiceSeeder::class
        ]);
    }
}
