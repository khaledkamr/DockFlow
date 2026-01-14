<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 1; 

        $items = [
            [
                'name' => 'رسوم اذن التسليم - DELIVERY ORDER',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم اذن التسليم - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [   
                'name' => 'رسوم مواني - PORT CHARGE',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم مواني - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'رسوم الشركه السعوديه للمواني - SAUDI GLOBAL PORT FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم الشركه السعوديه للمواني - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'اجور السكه الحديد - DRY PORT FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'اجور السكه الحديد - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'رسوم موعد فسح - APPOINTMENT FASAH',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم موعد فسح - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'رسوم سابر - SABER FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم سابر - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'رسوم جمركية - CUSTOMS FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم جمركية - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'ارضيات - DEMURRAGE CHARGE',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'ارضيات - تحت التسوية')->first()->id ?? null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'اجور تخليص - CLEARANCE FEES',
                'type' => 'ايراد تخليص',
                'debit_account_id' => null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'اجور نقل - TRANSPORT FEES',
                'type' => 'ايراد نقل',
                'debit_account_id' => null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'اجور عمال - LABOUR',
                'type' => 'ايراد عمال',
                'debit_account_id' => null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'خدمات سابر - SABER FEES',
                'type' => 'ايراد سابر',
                'debit_account_id' => null,
                'company_id' => $company_id,
            ],
            [
                'name' => 'رسوم تخزين - STORAGE FEES',
                'type' => 'ايراد تخزين',
                'debit_account_id' => null,
                'company_id' => $company_id,
            ]
        ];

        foreach($items as $item) {
            Item::create($item);
        }
    }
}
