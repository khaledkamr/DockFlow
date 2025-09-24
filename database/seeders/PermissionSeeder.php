<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'عرض بيانات الشركة'],
            ['name' => 'تعديل بيانات الشركة'],
            ['name' => 'عرض العملاء'],
            ['name' => 'إضافة عميل'],
            ['name' => 'تعديل عميل'],
            ['name' => 'حذف عميل'],
            ['name' => 'إدارة المستخدمين'],
            ['name' => 'إدارة العقود'],
            ['name' => 'عرض العقود'],
            ['name' => 'إضافة عقد'],
            ['name' => 'عرض تفاصيل العقد'],
            ['name' => 'عرض الخدمات'],
            ['name' => 'إضافة خدمة'],
            ['name' => 'تعديل خدمة'],
            ['name' => 'حذف خدمة'],
            ['name' => 'عرض الحاويات'],
            ['name' => 'عرض انواع الحاويات'],
            ['name' => 'إضافة نوع حاوية'],
            ['name' => 'تعديل نوع حاوية'],
            ['name' => 'حذف نوع حاوية'],
            ['name' => 'عرض تقارير الحاويات'],
            ['name' => 'عرض الإتفاقيات'],
            ['name' => 'إنشاء اتفاقية'],
            ['name' => 'عرض الفواتير'],
            ['name' => 'إنشاء فاتورة'],
            ['name' => 'تعديل فاتورة'],
            ['name' => 'طباعة فاتورة'],
            ['name' => 'الإدارة المالية'],
            ['name' => 'إنشاء قيود وسندات'],
            ['name' => 'إنشاء حساب'],
            ['name' => 'حذف حساب'],
            ['name' => 'تقارير القيود'],
        ];

        foreach($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
