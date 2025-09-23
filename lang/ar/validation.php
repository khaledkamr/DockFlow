<?php
// resources/lang/ar/validation.php

return [
    'required' => ':attribute مطلوب.',
    'numeric' => ':attribute يجب أن يكون رقمًا.',
    'email' => ':attribute يجب أن يكون بريد إلكتروني صحيح.',
    'string' => ':attribute يجب أن يكون نصًا.',
    'max' => [
        'string' => ':attribute لا يجب أن يتجاوز :max حرف.',
        'numeric' => ':attribute لا يجب أن يتجاوز :max.',
    ],
    'min' => [
        'string' => ':attribute يجب أن يكون على الأقل :min حرف.',
        'numeric' => ':attribute يجب أن يكون على الأقل :min.',
    ],
    'confirmed' => 'كلمة المرور غير متطابقة.',
    'unique' => ':attribute مُستخدم بالفعل.',
    
    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'mobile' => 'رقم الجوال',
        'CR' => 'السجل التجاري',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
        'status' => 'الحالة',
        'location' => 'الموقع',
        'notes' => 'ملاحظات',
        'national_address' => 'العنوان الوطني',
        'TIN' => 'الرقم الضريبي',
        'company_representative' => 'اسم ممثل الشركة',
        'company_representative_nationality' => 'جنسية ممثل الشركة',
        'company_representative_NID' => 'الهوية الوطنية لممثل الشركة', 
        'company_representative_role' => 'دور ممثل الشركة',
        'customer_representative' => 'اسم ممثل العميل',
        'customer_representative_nationality' => 'جنسية ممثل العميل',
        'customer_representative_NID' => 'الهوية الوطنية لممثل العميل',
        'customer_representative_role' => 'دور ممثل العميل',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'description' => 'الوصف',
        'price' => 'السعر',
        'amount' => 'المبلغ',
        'hatching' => 'الفقيط',
        'role' => 'الوظيفة'
    ],
];