@extends('layouts.print')
@section('title', 'بوليصة شحن - ' . $policy->code )
@section('content')
    <!-- Header -->
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark mb-2">بوليصة شحن</h3>
        <h5 class="fw-semibold text-secondary">{{ $policy->code }}</h5>
    </div>

    <!-- بيانات الاتفاقية -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="border border-dark rounded-3 p-3 bg-light h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات العميل</h6>
                <div class="row">
                    <div class="col">
                        <p class="mb-2 small"><strong class="text-secondary">اسم العميل:</strong><br>{{ $policy->customer->name }}</p>
                    </div>
                    <div class="col">
                        <p class="mb-2 small"><strong class="text-secondary">رقم العميل:</strong><br>{{ $policy->customer->account->code }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p class="mb-2 small"><strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $policy->customer->vatNumber }}</p>
                    </div>
                    <div class="col">
                        <p class="mb-2 small"><strong class="text-secondary">العنوان الوطني:</strong><br>{{ $policy->customer->national_address }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="border border-dark rounded-3 p-3 bg-light h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات الناقل</h6>
                <div class="row">
                    <div class="col">
                        <p class="mb-2 small"><strong class="text-secondary">اسم المورد:</strong><br>{{ $policy->supplier->name ?? ' ' }}</p>
                    </div>
                    @if($policy->type == 'ناقل داخلي')
                        <div class="col">
                            <p class="mb-2 small"><strong class="text-secondary">اسم السائق:</strong><br>{{ $policy->driver->name }}</p>
                        </div>
                    @elseif($policy->type == 'ناقل خارجي')
                        <div class="col">
                            <p class="mb-2 small"><strong class="text-secondary">اسم السائق:</strong><br>{{ $policy->driver_name }}</p>
                        </div>
                    @endif
                </div>
                <div class="row">
                    @if($policy->type == 'ناقل داخلي')
                        <div class="col">
                            <p class="mb-2 small"><strong class="text-secondary">رقم الهوية:</strong><br>{{ $policy->driver->NID ?? 'N/A' }}</p>
                        </div>
                    @endif
                    @if($policy->type == 'ناقل داخلي')
                        <div class="col">
                            <p class="mb-2 small"><strong class="text-secondary">الشاحنة:</strong><br>{{ $policy->vehicle->plate_number . ' - ' . $policy->vehicle->type }}</p>
                        </div>
                    @elseif($policy->type == 'ناقل خارجي')
                        <div class="col">
                            <p class="mb-2 small"><strong class="text-secondary">الشاحنة:</strong><br>{{ $policy->vehicle_plate }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="table-responsive">
            <table class="table table-bordered border-dark table-hover mb-0">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th class="fw-bold">البيان</th>
                        <th class="fw-bold">الكمية</th>
                        <th class="fw-bold">الوزن</th>
                        <th class="fw-bold">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($policy->goods as $index => $good)
                        <tr class="text-center">
                            <td class="fw-bold">{{ $good->description }}</td>
                            <td class="fw-bold">{{ $good->quantity }}</td>
                            <td>{{ $good->weight }}</td>
                            <td>{{ $good->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- معلومات الوصول والتفريغ -->
    <div class="mb-4">
        <div class="border border-dark rounded p-3 bg-light">
            <h4 class="fw-bold text-center text-dark mb-4">اقرار العميل باستلام الحمولة</h4>
            <div class="d-flex gap-3 mb-4">
                <h6 class="fw-bold text-dark">تم استلام حميع محتويات البضاعة في المكان والزمان المحدد بدون نقض او تلف</h6>
                <div class="form-check d-inline-block me-3">
                    <input class="form-check-input form-check-input-lg border-dark" type="checkbox" style="transform: scale(1.5);">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 text-start">
                    <div class="mb-4">
                        <label class="fw-bold text-secondary mb-2">تاريخ  الاستلام:</label>
                        <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 300px;"></div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-secondary mb-2">الكمية المستلمة:</label>
                        <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 300px;"></div>
                    </div>
                </div>
                <div class="col-md-6 text-start">
                    <div class="mb-4">
                        <label class="fw-bold text-secondary mb-2">فترة التأخير:</label>
                        <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 300px;"></div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-secondary mb-2">من مواقع اخرى:</label>
                        <div class="border-bottom border-2 border-dark" style="min-height: 30px; width: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الملاحظات -->
    <div class="mb-4">
        <div class="border border-dark rounded p-3 bg-light">
            <h4 class="fw-bold text-center text-dark mb-3">الملاحظات</h4>
            <h6 class="fw-bold text-dark mb-3">تم تدوبن الملاحظات ان وجدت بحضور مندوب من الناقل ومندوب من العميل والسائق:</h6>
            <div style="min-height: 80px;"></div>
        </div>
    </div>

    <!-- منطقة التوقيعات -->
    <div class="d-flex justify-content-between position-absolute bottom-0 start-0 end-0 bg-white p-2 pb-5 mb-5">
        <div class="col-md-4 text-center">
            <div class="border-top border-dark border-2 pt-3 mx-3">
                <strong>اسم المستلم</strong>
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
