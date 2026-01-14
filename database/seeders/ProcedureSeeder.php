<?php

namespace Database\Seeders;

use App\Models\Procedure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcedureSeeder extends Seeder
{
    public function run(): void
    {
        $company_id = 1;

        $procedures = [
            [
                'name' => 'تم فتح معامله - Open Transaction',
                'company_id' => $company_id,
            ],
            [
                'name' => 'تم استلام اذن التسليم - The delivery order has been received',
                'company_id' => $company_id,
            ],
            [
                'name' => 'تم طباعة البيان - The import declaration has been received',
                'company_id' => $company_id,
            ],
            [
                'name' => 'تم السداد على معامله - Payment is made',
                'company_id' => $company_id,
            ],
            [
                'name' => 'لم تصل الباخرة - The steamer did not arrive',
                'company_id' => $company_id,
            ],
            [
                'name' => 'مطلوب تفويض - Authorization required',
                'company_id' => $company_id,
            ],
            [
                'name' => 'مطلوب فسح للمواد المقيدة - Approval Required',
                'company_id' => $company_id,
            ],
            [
                'name' => 'تم عمل اشعار نقل - Create transport notification',
                'company_id' => $company_id,
            ],
            [
                'name' => 'تم عمل فاتورة - Create invoice',
                'company_id' => $company_id,
            ],
            [
                'name' => 'لم تفرغ الحاوية فى الايداع - The container was not emptied at the deposit',
                'company_id' => $company_id,
            ],
        ];

        foreach($procedures as $procedure) {
            Procedure::create($procedure);
        }
    }
}
