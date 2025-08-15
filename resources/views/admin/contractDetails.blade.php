@extends('layouts.admin')

@section('title', 'تفاصيل العقد')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">تفاصيل العقد #{{ $contract->id }}</h2>
            <div>
                <button class="btn btn-1 me-2 fw-bold" onclick="downloadContract()">
                    <i class="fas fa-download me-1"></i>
                    تحميل العقد
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary fw-bold">
                    <i class="fas fa-arrow-right me-1"></i>
                    العودة
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-dark text-white rounded-top-3">
                <h5 class="mb-0">
                    <i class="fas fa-file-contract me-2"></i>
                    معلومات العقد
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">رقم العقد</label>
                        <div class="fw-bold">#{{ $contract->id }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">حالة العقد</label>
                        <div class="fw-bold">{{ $contract->status }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="text-muted small">العميل</label>
                        <div class="fw-bold">{{ $contract->user->name }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">رقم الهوية الوطنية</label>
                        <div class="fw-bold">{{ $contract->user->NID }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">رقم الهاتف</label>
                        <div class="fw-bold">{{ $contract->user->phone }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="text-muted small">الشركة</label>
                        <div class="fw-bold">ساحة تخزين</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">إســم الفــرع</label>
                        <div class="fw-bold">الفرع الرئيسي</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">رقم الهاتف</label>
                        <div class="fw-bold">0123456789</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="text-muted small">تاريخ البدء</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($contract->start_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">التاريخ المتوقع للانتهاء</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($contract->expected_end_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">التاريخ الفعلي للانتهاء</label>
                        <div class="fw-bold">
                            {{ $contract->actual_end_date ? \Carbon\Carbon::parse($contract->actual_end_date)->format('Y-m-d') : 'لم ينته بعد' }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    @php
                        use Carbon\Carbon;
                        $remainingDays = Carbon::now()->diffInDays(Carbon::parse($contract->expected_end_date));
                    @endphp
                    @if($remainingDays > 0)
                        <div class="col">
                            <p class="text-danger">باقي {{ (int) $remainingDays }} ايام على انتهاء مدة العقد</p>
                        </div>
                    @elseif($remainingDays < 0)
                        <div class="col">
                            <p class="text-danger">انتهت مدة العقد منذ {{ abs((int) $remainingDays) }} ايام</p>
                        </div>
                    @else
                        <div class="col">
                            <p class="text-danger">اليوم هو آخر يوم في مدة العقد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white rounded-top-3">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    المعلومات المالية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">السعر الإجمالي</label>
                    <div class="h4 fw-bold">{{ number_format($contract->price, 2) }} ريال</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">رسوم التأخير</label>
                    @if($remainingDays < 0)
                        <div class="h4 fw-bold">{{ number_format($contract->late_fee * abs($remainingDays), 2) }} ريال</div>
                    @elseif($remainingDays > 0)
                        <div class="h4 fw-bold">0.00 ريال</div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="text-muted small">الضريبة المضافة</label>
                    @if($contract->tax == 'غير معفي')
                        <div class="h4 fw-bold">15%</div>
                    @elseif($contract->tax == 'معفي')
                        <div class="h4 fw-bold">0%</div>
                    @endif
                </div>
                <hr>
                <div class="mb-0">
                    <label class="text-muted small">إجمالي المبلغ</label>
                    <div class="h4 fw-bold">
                        {{ number_format($contract->price
                            + ($remainingDays < 0 ? $contract->late_fee * abs($remainingDays) : 0)
                            + ($contract->tax == 'غير معفي' ? $contract->price * 15/100 : 0), 2) }} ريال
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">#</th>
                <th class="text-center bg-dark text-white">كــود الحاويــة</th>
                <th class="text-center bg-dark text-white">الفئـــة</th>
                <th class="text-center bg-dark text-white">الموقــع</th>
                <th class="text-center bg-dark text-white">الحالـــة</th>
                <th class="text-center bg-dark text-white">السعـر اليومـي</th>
                <th class="text-center bg-dark text-white">تاريــخ الإنشــاء</th>
            </tr>
        </thead>
        <tbody>
            @if ($contract->containers->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي حاويات في العقد!</div>
                    </td>
                </tr>
            @else
                @foreach ($contract->containers as $index => $container)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $container->code }}</td>
                        <td class="text-center">{{ $container->containerType->name }}</td>
                        <td class="text-center">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $container->location }}
                        </td>
                        <td class="text-center">
                            <div class="{{ $container->status == 'في الإنتظار' ? 'status-waiting' : ($container->status == 'موجود' ? 'status-available' : 'status-danger') }}">
                                {{ $container->status }}
                            </div>
                        </td>
                        <td class="text-center text-success fw-bold">{{ number_format($container->containerType->daily_price, 2) }} ريال/يوم</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($container->created_at)->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<form action="" method="POST" class="mt-4">
    @csrf
    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
    <input type="hidden" name="remaining_days" value="{{ $remainingDays }}">
    
</form>

<button type="button" class="btn btn-1 fw-bold" data-bs-toggle="modal" data-bs-target="#createInvoice">
    إستخراج فاتورة <i class="fas fa-scroll ps-1"></i>
</button>

<div class="modal fade" id="createInvoice" tabindex="-1" aria-labelledby="createInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إنهــاء العقـد و إنشــاء فــاتورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <input type="hidden" name="invoice_date" value="{{ Carbon::now() }}">
                <input type="hidden" name="base_price" value="{{ $contract->price }}">
                <input type="hidden" name="late_fee_total" value="{{ $remainingDays < 0 ? abs($remainingDay) * $contract->late_fee : 0 }}">
                <input type="hidden" name="tax_total" value="{{ ($contract->tax == 'غير معفي' ? $contract->price * 15/100 : 0) }}">
                <input type="hidden" name="grand_total" value="{{ $contract->price
                                                                    + ($contract->tax == 'غير معفي' ? $contract->price * 15/100 : 0)
                                                                    + ($remainingDays < 0 ? abs($remainingDay) * $contract->late_fee : 0) }}">
                <div class="modal-body text-dark">
                    <div class="row mb-2">
                        <div class="col">
                            <label class="text muted small">عقد رقم</label>
                            <div class="fw-bold">#{{ $contract->id }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">من</label>
                            <div class="fw-bold">{{ $contract->start_date }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الى</label>
                            <div class="fw-bold">{{ Carbon::now()->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="text muted small">العميل</label>
                            <div class="fw-bold">{{ $contract->user->name }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الموظف</label>
                            <div class="fw-bold">علي رمضان</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الساعة</label>
                            <div class="fw-bold">{{ Carbon::now()->format('H:i') }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-2 fw-bold">#</div>
                        <div class="col fw-bold">كود</div>
                        <div class="col fw-bold">فئة</div>
                        <div class="col fw-bold">السعر</div>
                    </div>
                    @foreach($contract->containers as $index => $container)
                        <div class="row mb-2">
                            <div class="col-2">{{ $index + 1 }}</div>
                            <div class="col">{{ $container->code }}</div>
                            <div class="col">{{ $container->containerType->name }}</div>
                            <div class="col">{{ $container->containerType->daily_price }} ريال</div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">مجموع</div>
                        <div>{{ $contract->price }} ريال</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">ضريبة تأخير</div>
                        <div>{{ number_format(($remainingDays < 0 ? abs($remainingDay) * $contract->late_fee : 0), 2) }} ريال</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">الضريبة المضافة</div>
                        <div>{{ number_format(($contract->tax == 'غير معفي' ? $contract->price * 15/100 : 0), 2) }} ريال</div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">المجموع الكلي</div>
                        <div>{{ number_format($contract->price
                                + ($contract->tax == 'غير معفي' ? $contract->price * 15/100 : 0)
                                + ($remainingDays < 0 ? abs($remainingDay) * $contract->late_fee : 0), 2) }} ريال
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إالغاء</button>
                    <button type="submit" class="btn btn-1">إنشاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function downloadContract() {
    // You can implement the download functionality here
    // This could be a link to a PDF generation endpoint
    window.open(`/contracts/{{ $contract->id }}/download`, '_blank');
    
    // Or using fetch for AJAX request
    /*
    fetch(`/contracts/{{ $contract->id }}/download`)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `contract_{{ $contract->id }}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading contract:', error);
            alert('حدث خطأ أثناء تحميل العقد');
        });
    */
}
</script>

<style>
.table-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.table thead {
    background-color: #f8f9fa;
    color: #333;
}
.table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 1px solid #e9ecef;
}
.table td {
    padding: 15px;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #e9ecef;
}
.table tbody tr:hover {
    background-color: #f1f3f5;
}
.table .status-waiting {
    background-color: #fff3cd;
    color: #856404;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    display: inline-block;
}
.table .status-available {
    background-color: #d4edda;
    color: #155724;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    display: inline-block;
}
.table .status-danger {
    background-color: #f8d7da;
    color: #721c24;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    display: inline-block;
}
</style>
@endsection