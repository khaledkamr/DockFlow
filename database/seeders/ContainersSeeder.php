<?php

namespace Database\Seeders;

use App\Models\Container;
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
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-35',
            ],
            [
                'code' => 'SFD-128',
                'container_type_id' => 1,
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-36',
            ],
            [
                'code' => 'SFD-129',
                'container_type_id' => 1,
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-37',
            ],
            [
                'code' => 'SFD-130',
                'container_type_id' => 2,
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-38',
            ],
            [
                'code' => 'SFD-131',
                'container_type_id' => 2,
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-39',
            ],
            [
                'code' => 'SFD-132',
                'container_type_id' => 3,
                'user_id' => 1,
                'status' => 'في الإنتظار',
                'location' => 'B-40',
            ],
        ];

        foreach($containers as $container) {
            Container::create($container);
        }
    }
}
