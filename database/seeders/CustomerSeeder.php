<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'مكتب الغانمي للتخليص الجمركي',
                'CR' => '82546872874',
                'TIN' => '01006379503',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'مؤسسة طيور الحباري التجارية',
                'CR' => '82546872874',
                'TIN' => '01006379503',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'شركة اعمال البن النجارية',
                'CR' => '82546872874',
                'TIN' => '55246576',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'مؤسسة العكيلي للتخليص الجمركي',
                'CR' => '82546872874',
                'TIN' => '254656526',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
           
        ];

        foreach($customers as $customer) {
            Customer::create($customer);
        }
    }
}
