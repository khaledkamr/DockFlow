<?php

namespace Database\Seeders;

use App\Models\Container;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainersSeeder extends Seeder
{
    public function run(): void
    {
        $containers = [
            [
                'code' => 'SFD-127',
                'container_type_id' => 1,
                'customer_id' => 1,
                'status' => 'في الإنتظار',
            ],
            [
                'code' => 'SFD-128',
                'container_type_id' => 1,
                'customer_id' => 1,
                'status' => 'في الإنتظار',
            ],
            [
                'code' => 'SFD-129',
                'container_type_id' => 1,
                'customer_id' => 1,
                'status' => 'في الإنتظار',
            ],
            [
                'code' => 'SFD-130',
                'container_type_id' => 2,
                'customer_id' => 1,
                'status' => 'متوفر',
                'location' => 'B-38',
                'date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'code' => 'SFD-131',
                'container_type_id' => 2,
                'customer_id' => 1,
                'status' => 'متوفر',
                'location' => 'B-39',
                'date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'code' => 'SFD-132',
                'container_type_id' => 3,
                'customer_id' => 1,
                'status' => 'متوفر',
                'location' => 'B-40',
                'date' => Carbon::now()->format('Y-m-d')
            ],
        ];

        foreach($containers as $container) {
            Container::create($container);
        }
    }
}
