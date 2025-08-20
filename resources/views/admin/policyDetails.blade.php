@extends('layouts.admin')

@section('title', 'تفاصيل العقد')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">تفاصيل العقد #{{ $policy->id }}</h2>
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

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('errors'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>حدث خطأ في العملية!</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                        <div class="fw-bold">#{{ $policy->id }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">حالة العقد</label>
                        <div class="fw-bold">{{ $policy->status }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="text-muted small">العميل</label>
                        <div class="fw-bold">{{ $policy->customer->name }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">رقم السجل الوطني الضريبي</label>
                        <div class="fw-bold">{{ $policy->customer->CR }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">رقم الهاتف</label>
                        <div class="fw-bold">{{ $policy->customer->phone }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="text-muted small">الشركة</label>
                        <div class="fw-bold">شركة تاج الأعمال للخدمات اللوجستية</div>
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
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($policy->start_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">التاريخ المتوقع للانتهاء</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($policy->expected_end_date)->format('Y-m-d') }}</div>
                    </div>
                    <div class="col mb-3">
                        <label class="text-muted small">التاريخ الفعلي للانتهاء</label>
                        <div class="fw-bold">
                            {{ $policy->actual_end_date ? \Carbon\Carbon::parse($policy->actual_end_date)->format('Y-m-d') : 'لم ينته بعد' }}
                        </div>
                    </div>
                </div>
                @if((int) $remainingDays > 0 && $policy->status == 'جاري')
                    <div class="row">
                        <div class="col">
                            <p class="text-danger">باقي {{ (int) $remainingDays }} ايام على انتهاء مدة العقد</p>
                        </div>
                    </div>
                @elseif((int) $remainingDays < 0 && $policy->status == 'جاري')
                    <div class="row">
                        <div class="col">
                            <p class="text-danger">انتهت مدة العقد منذ {{ abs((int) $remainingDays) }} ايام</p>
                        </div>
                    </div>
                @elseif((int) $remainingDays == 0 && $policy->status == 'جاري')
                    <div class="row">
                        <div class="col">
                            <p class="text-danger">اليوم هو آخر يوم في مدة العقد</p>
                        </div>
                    </div>
                @elseif($policy->status == 'منتهي')
                    <a class="btn btn-1 fw-bold">
                        عــرض الـفاتــورة <i class="fas fa-scroll ps-1"></i>
                    </a>
                @endif
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
                    <div class="h4 fw-bold">{{ number_format($policy->price, 2) }} ريال</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">رسوم التأخير</label>
                    @if($remainingDays < 0)
                        <div class="h4 fw-bold">{{ number_format($policy->late_fee * (int) abs($remainingDays), 2) }} ريال</div>
                    @elseif($remainingDays > 0)
                        <div class="h4 fw-bold">0.00 ريال</div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="text-muted small">الضريبة المضافة</label>
                    @if($policy->tax == 'غير معفي')
                        <div class="h4 fw-bold">15%</div>
                    @elseif($policy->tax == 'معفي')
                        <div class="h4 fw-bold">0%</div>
                    @endif
                </div>
                <hr>
                <div class="mb-0">
                    <label class="text-muted small">إجمالي المبلغ</label>
                    <div class="h4 fw-bold">
                        {{ number_format($contract->price
                            + ($remainingDays < 0 ? $policy->late_fee * (int) abs($remainingDays) : 0)
                            + ($policy->tax == 'غير معفي' ? $policy->price * 15/100 : 0), 2) }} ريال
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
                <th class="text-center bg-dark text-white">تاريــخ الدخــول</th>
            </tr>
        </thead>
        <tbody>
            @if ($policy->containers->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي حاويات في العقد!</div>
                    </td>
                </tr>
            @else
                @foreach ($policy->containers as $index => $container)
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

@if($policy->status == 'جاري')
    <button type="button" class="btn btn-1 fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#createInvoice">
        إستخراج فاتورة <i class="fas fa-scroll ps-1"></i>
    </button>
@elseif($policy->status == 'منتهي')
    <button type="button" class="btn btn-1 fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#exitPermission">
        طباعة إذن خروج <i class="fas fa-scroll ps-1"></i>
    </button>
@endif

<div class="modal fade" id="exitPermission" tabrindex="-1" aria-labelby="exitPermissionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إذن خـــروج</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.exitPermission.create') }}" method="POST">
                @csrf
                <input type="hidden" name="contract_id" value="{{ $policy->id }}">
                <div class="modal-body text-dark">
                    <div class="row">
                        <div class="col">
                            <label class="text muted small">رقم العميل</label>
                            <div class="fw-bold">#{{ $policy->customer->id }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">إســم العميـــل</label>
                            <div class="fw-bold">{{ $policy->customer->name }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">رقم الهاتــف</label>
                            <div class="fw-bold">{{ $policy->customer->phone }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 text-center">
                        <div class="col-2 fw-bold">#</div>
                        <div class="col fw-bold">كود</div>
                        <div class="col fw-bold">فئة</div>
                        <div class="col fw-bold">الموقع</div>
                    </div>
                    @foreach($policy->containers as $index => $container)
                        <div class="row mb-2 text-center">
                            <div class="col-2">{{ $index + 1 }}</div>
                            <div class="col">{{ $container->code }}</div>
                            <div class="col">{{ $container->containerType->name }}</div>
                            <div class="col">{{ $container->location }}</div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="row">
                        <div class="col">
                            <label class="text muted small">رقم الموظف</label>
                            <div class="fw-bold">#23</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الموظف</label>
                            <div class="fw-bold">علي رمضان</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الساعة</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">التاريخ</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إالغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="createInvoice" tabindex="-1" aria-labelledby="createInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إنهــاء العقـد و إنشــاء فــاتورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.invoice.create') }}" method="POST">
                @csrf
                <input type="hidden" name="policy_id" value="{{ $policy->id }}">
                <input type="hidden" name="user_id" value="{{ $policy->customer->id }}">
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::now() }}">
                <input type="hidden" name="base_price" value="{{ $policy->price }}">
                <input type="hidden" name="late_fee_total" value="{{ $remainingDays < 0 ? (int) abs($remainingDays) * $policy->late_fee : 0 }}">
                <input type="hidden" name="tax_total" value="{{ ($policy->tax == 'غير معفي' ? $policy->price * 15/100 : 0) }}">
                <input type="hidden" name="grand_total" value="{{ $policy->price
                                                                    + ($policy->tax == 'غير معفي' ? $policy->price * 15/100 : 0)
                                                                    + ($remainingDays < 0 ? (int) abs($remainingDays) * $policy->late_fee : 0) }}">
                <div class="modal-body text-dark">
                    <div class="row mb-2">
                        <div class="col">
                            <label class="text muted small">عقد رقم</label>
                            <div class="fw-bold">#{{ $policy->id }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">من</label>
                            <div class="fw-bold">{{ $policy->start_date }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الى</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="text muted small">رقم العميل</label>
                            <div class="fw-bold">{{ $policy->user->id }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">إســم العميـــل</label>
                            <div class="fw-bold">{{ $policy->user->name }}</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الرقم الهاتــف</label>
                            <div class="fw-bold">{{ $policy->user->phone }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-2 fw-bold">#</div>
                        <div class="col fw-bold">كود</div>
                        <div class="col fw-bold">فئة</div>
                        <div class="col fw-bold">السعر</div>
                    </div>
                    @foreach($policy->containers as $index => $container)
                        <div class="row mb-2">
                            <div class="col-2">{{ $index + 1 }}</div>
                            <div class="col">{{ $container->code }}</div>
                            <div class="col">{{ $container->containerType->name }}</div>
                            <div class="col">{{ $container->containerType->daily_price }} ريال</div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">الإجمالي</div>
                        <div>{{ $policy->price }} ريال</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">ضريبة تأخير</div>
                        <div>{{ number_format(($remainingDays < 0 ? (int) abs($remainingDays) * $policy->late_fee : 0), 2) }} ريال</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold">الضريبة المضافة(15%)</div>
                        <div>{{ number_format(($policy->tax == 'غير معفي' ? $policy->price * 15/100 : 0), 2) }} ريال</div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-5">إجمالي المبلغ</div>
                        <div class="fs-5">{{ number_format($policy->price
                                + ($policy->tax == 'غير معفي' ? $policy->price * 15/100 : 0)
                                + ($remainingDays < 0 ? (int) abs($remainingDays) * $policy->late_fee : 0), 2) }} ريال
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <label class="text muted small">رقم الموظف</label>
                            <div class="fw-bold">#23</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الموظف</label>
                            <div class="fw-bold">علي رمضان</div>
                        </div>
                        <div class="col">
                            <label class="text muted small">الساعة</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                        </div>
                    </div>
                    <div class="row align-items-center mb-3 mt-2">
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">طريقة الدفع</label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-select" name="payment_method" id="payment_method" required>
                                <option value="" selected disabled>اختر طريقة الدفع</option>
                                <option value="كاش">كاش</option>
                                <option value="كريدت">كريدت</option>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                            </select>
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