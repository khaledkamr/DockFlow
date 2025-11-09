@extends('layouts.print')
@section('title', 'إشعار نقل - ' . time())
@section('content')
<!-- Header -->
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark mb-2">إشعار نقل</h3>
    <p class="text-muted mb-0">{{ $transportOrder->code }}</p>
</div>

<!-- بيانات الاتفاقية -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="border rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات العميل</h6>
            <div class="row">
                <div class="col">
                    <p class="mb-2 small"><strong class="text-secondary">اسم العميل:</strong><br>{{ $transportOrder->customer->name }}</p>
                </div>
                <div class="col">
                    <p class="mb-2 small"><strong class="text-secondary">رقم العميل:</strong><br>{{ $transportOrder->customer->account->code }}</p>
                </div>
                <div class="col">
                    <p class="mb-2 small"><strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $transportOrder->customer->vatNumber }}</p>
                </div>
                <div class="col">
                    <p class="mb-2 small"><strong class="text-secondary">العنوان الوطني:</strong><br>{{ $transportOrder->customer->national_address }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="fw-bold text-dark mb-3">بيانات الحاويات</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr class="text-center">
                    <th class="fw-bold">#</th>
                    <th class="fw-bold">رقم البوليصة</th>
                    <th class="fw-bold">رقم الحاوية</th>
                    <th class="fw-bold">نوع الحاوية</th>
                    <th class="fw-bold">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transportOrder->containers as $index => $container)
                <tr class="text-center">
                    <td class="fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $transportOrder->transaction->policy_number }}</td>
                    <td class="fw-bold">{{ $container->code }}</td>
                    <td>{{ $container->containerType->name }}</td>
                    <td>{{ $container->notes ?? '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mb-5">
    <h6 class="fw-bold text-dark mb-3">جميع الردود</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr class="text-center">
                    <th class="fw-bold">#</th>
                    <th class="fw-bold">رقم اللوحة</th>
                    <th class="fw-bold">السائق</th>
                    <th class="fw-bold">مكان التحميل</th>
                    <th class="fw-bold">مكان التسليم</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td class="fw-bold">1</td>
                    @if($transportOrder->type == 'ناقل داخلي')
                        <td class="fw-bold">{{ $transportOrder->vehicle->plate_number }}</td>
                        <td class="fw-bold">{{ $transportOrder->driver->name }}</td>
                    @elseif($transportOrder->type == 'ناقل خارجي')
                        <td class="fw-bold">{{ $transportOrder->vehicle_plate }}</td>
                        <td class="fw-bold">{{ $transportOrder->driver_name }}</td>
                    @endif
                    <td>{{ $transportOrder->from }}</td>
                    <td>{{ $transportOrder->to }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- الشروط والأحكام -->
<div class="mb-4">
    <div class="border rounded p-3 bg-light">
        <h6 class="fw-bold text-dark mb-3">الشروط والأحكام</h6>
        <ul class="list-unstyled mb-0">
            <li class="mb-2">• المحتوبات المشار إليها أعله تقع على مسؤولية العميل ولا تقع على مسؤولية الناقل.</li>
            <li class="mb-2">• عدد الطرود ومحتوياتها المذكورة أعله هي حسب تصريح العميل ولا تشكل اي مسؤولية على الناقل.</li>
        </ul>
    </div>
</div>

<!-- معلومات الوصول والتفريغ -->
<div class="mb-4">
    <div class="border rounded p-3 bg-light">
        <h6 class="fw-bold text-dark mb-3">معلومات الوصول والتفريغ</h6>
        <div class="row">
            <div class="col-md-6 text-start">
                <div class="mb-4">
                    <label class="fw-bold text-secondary mb-2">تاريخ التفريغ:</label>
                    <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 200px;"></div>
                </div>
                <div class="mb-4">
                    <label class="fw-bold text-secondary mb-2">وقت التفريغ:</label>
                    <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 200px;"></div>
                </div>
            </div>
            <div class="col-md-6 text-start">
                <div class="mb-4">
                    <label class="fw-bold text-secondary mb-2">تاريخ الوصول:</label>
                    <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 200px;"></div>
                </div>
                <div class="mb-4">
                    <label class="fw-bold text-secondary mb-2">وقت الوصول:</label>
                    <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 200px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- منطقة التوقيعات -->
<div class="d-flex justify-content-between position-absolute bottom-0 start-0 end-0 bg-white p-2 pb-5 mb-5">
    <div class="col-md-4 text-center">
        <div class="border-top border-dark border-2 pt-3 mx-3">
            <strong>توقيع المسؤول</strong>
            <br>
        </div>
    </div>
    <div class="col-md-4 text-center">
        <div class="border-top border-dark border-2 pt-3 mx-3">
            <strong>توقيع المستلم</strong>
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