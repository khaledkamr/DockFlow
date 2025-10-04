@extends('layouts.print')
@section('title', 'اذن دخول - ' . time())
@section('content')
<!-- Header -->
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark mb-2">إذن خدمات</h3>
</div>

<!-- بيانات الاتفاقية -->
<div class="row mb-4">
    <div class="col-12">
        <div class="border rounded p-3 bg-light">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">بيانات الاتفاقية</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">رقم الإتفاقية:</div>
                        <div class="col-7 text-dark fw-bold">{{ $policy->code }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">إسم العميل:</div>
                        <div class="col-7">{{ $policy->customer->name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">تاريخ الإتفاقية:</div>
                        <div class="col-7">{{ \Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">البيان الضريبي:</div>
                        <div class="col-7 fw-bold">{{ $policy->tax_statement }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">اسم السائق:</div>
                        <div class="col-7">{{ $policy->driver_name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">الرقم الإقامة:</div>
                        <div class="col-7">{{ $policy->driver_NID ?? 'غير محدد' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">نوع المركبة:</div>
                        <div class="col-7">{{ $policy->driver_car ?? 'غير محدد' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold text-end">رقم المركبة:</div>
                        <div class="col-7">{{ $policy->car_code ?? 'غير محدد' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- معلومات الشركة -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="border rounded p-3 bg-light">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">معلومات الشركة</h6>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">اسم الشركة:</div>
                <div class="col-7">{{ $policy->company->name ?? '-'}}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">السجل التجاري:</div>
                <div class="col-7">{{ $policy->company->CR }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">الرقم الضريبي:</div>
                <div class="col-7">{{ $policy->company->vatNumber }}</div>
            </div>
        </div>
    </div>
    
    <!-- معلومات العميل -->
    <div class="col-md-6">
        <div class="border rounded p-3 bg-light">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">معلومات العميل</h6>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">اسم العميل:</div>
                <div class="col-7">{{ $policy->customer->name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">السجل التجاري:</div>
                <div class="col-7">{{ $policy->customer->CR ?? 'غير محدد' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">الرقم الضريبي:</div>
                <div class="col-7">{{ $policy->customer->vatNumber ?? 'غير محدد' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="fw-bold text-dark mb-3">بيانات الحاويات المرخص بدخولها</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr class="text-center">
                    <th class="fw-bold">#</th>
                    <th class="fw-bold">رقم الحاوية</th>
                    <th class="fw-bold">العميل</th>
                    <th class="fw-bold">نوع الحاوية</th>
                    <th class="fw-bold">الخدمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($policyContainers as $index => $container)
                <tr class="text-center">
                    <td class="fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold text-primary">{{ $container->code }}</td>
                    <td>{{ $container->customer->name }}</td>
                    <td>{{ $container->containerType->name }}</td>
                    <td class="fw-bold">{{ $container->services->first()->description }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2 text-end">
        <small class="text-muted">إجمالي عدد الحاويات: {{ count($policyContainers) }} حاوية</small>
    </div>
</div>

<!-- الشروط والأحكام -->
<div class="mb-4">
    <div class="border rounded p-3 bg-light">
        <h6 class="fw-bold text-dark mb-3">الشروط والأحكام</h6>
        <ul class="list-unstyled mb-0">
            <li class="mb-2">• يجب على السائق الالتزام بقوانين وأنظمة المنشأة</li>
            <li class="mb-2">• يُمنع الدخول إلى أي مناطق غير مسموح بها</li>
            <li class="mb-2">• يجب إبراز هذا الإذن عند الطلب</li>
            <li class="mb-2">• هذا الإذن ساري لليوم المحدد فقط</li>
            <li class="mb-0">• الشركة غير مسؤولة عن أي أضرار قد تحدث أثناء التواجد في المنشأة</li>
        </ul>
    </div>
</div>

<!-- منطقة التوقيعات -->
<div class="d-flex justify-content-between position-absolute bottom-0 start-0 end-0 bg-white p-2 pb-5 mb-5">
    <div class="col-md-4 text-center">
        <div class="border-top pt-3 mx-3">
            <strong>توقيع المسؤول</strong>
            <br>
        </div>
    </div>
    <div class="col-md-4 text-center">
        <div class="border-top pt-3 mx-3">
            <strong>الختم الرسمي</strong>
            <br>
        </div>
    </div>
</div>

<style>
    .signature-line {
        border-top: 2px solid #333;
        margin-top: 60px;
        padding-top: 10px;
    }
    /* Add this to ensure footer shows */
    .no-print-footer {
        z-index: 9999 !important;
        display: block !important;
    }
    
</style>
@endsection