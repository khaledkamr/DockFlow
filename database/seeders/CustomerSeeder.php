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
                'type' => 'شركة',
                'CR' => '82546872874',
                'TIN' => '01006379503',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'مؤسسة طيور الحباري التجارية',
                'type' => 'مؤسسة',
                'CR' => '82546872874',
                'TIN' => '01006379503',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'شركة اعمال البن النجارية',
                'type' => 'شركة',
                'CR' => '82546872874',
                'TIN' => '55246576',
                'national_address' => '01006379503',
                'phone' => '0123456789',
                'email' => 'ex@gmail.com',
            ],
            [
                'name' => 'مؤسسة العكيلي للتخليص الجمركي',
                'type' => 'مؤسسة',
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
