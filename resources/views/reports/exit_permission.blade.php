@extends('layouts.print')

@section('title', 'اذن خروج - ' . time())

@section('content')
<h3 class="text-center fw-bold mb-5 mt-4">اذن خروج</h3>

<!-- بيانات الاتفاقية -->
<div class="row mb-4">
    <div class="col-12">
        <div class="border border-dark rounded p-3 bg-light">
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
        <div class="border border-dark rounded p-3 bg-light">
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
        <div class="border border-dark rounded p-3 bg-light">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">معلومات العميل</h6>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">اسم العميل:</div>
                <div class="col-7">{{ $policy->contract->customer->name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">السجل التجاري:</div>
                <div class="col-7">{{ $policy->contract->customer->CR ?? 'غير محدد' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5 fw-bold text-end">الرقم الضريبي:</div>
                <div class="col-7">{{ $policy->contract->customer->vatNumber ?? 'غير محدد' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered border-dark table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center fw-bold">#</th>
                <th class="text-center fw-bold">كود الحاوية</th>
                <th class="text-center fw-bold">العميل</th>
                <th class="text-center fw-bold">نوع الحاوية</th>
                <th class="text-center fw-bold">الموقع</th>
                <th class="text-center fw-bold">تاريخ الخروج</th>
            </tr>
        </thead>
        <tbody>
            @foreach($policyContainers as $index => $container)
            <tr class="text-center">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="fw-bold text-primary">{{ $container->code }}</td>
                <td>{{ $container->customer->name }}</td>
                <td class="fw-bold">{{ $container->containerType->name }}</td>
                <td class="fw-bold">{{ $container->location }}</td>
                <td>{{ \Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- منطقة التوقيعات -->
<div class="d-flex justify-content-between position-absolute bottom-0 start-0 end-0 bg-white p-2 pb-5 mb-5">
    <div class="col-md-4 text-center">
        <div class="border-top border-dark pt-3 mx-3">
            <strong>توقيع المسؤول</strong>
            <br>
        </div>
    </div>
    <div class="col-md-4 text-center">
        <div class="border-top border-dark pt-3 mx-3">
            <strong>الختم الرسمي</strong>
            <br>
        </div>
    </div>
</div>

@endsection