@extends('layouts.admin')

@section('title', 'بيانات العميل')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <!-- User Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="fas fa-user-circle me-2"></i>
                    <h5 class="mb-0">معلومات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">الاسم:</strong>
                                <span class="ms-2 fw-bold fs-5">{{ $user->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-muted">رقم الهوية:</strong>
                                <span class="ms-2 fw-bold text-dark">{{ $user->NID }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">رقم الهاتف:</strong>
                                <span class="ms-2 fw-bold">{{ $user->phone }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-muted">تاريخ التسجيل:</strong>
                                <span class="ms-2 fw-bold">{{ $user->created_at ? $user->created_at->format('Y/m/d') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-2 border-info bg-transparent text-info">
                <div class="card-body text-center">
                    <i class="fas fa-file-contract fa-2x mb-2"></i>
                    <h4 class="fw-bold">{{ $user->contracts->count() }}</h4>
                    <p class="mb-0 fw-bold">إجمالي العقود</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-2 border-info bg-transparent text-info">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <h4 class="fw-bold">{{ count($user->invoices ?? []) }}</h4>
                    <p class="fw-bold mb-0">إجمالي الفواتير</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-2 border-success bg-transparent text-success">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                    <h4 class="fw-bold">{{ number_format(collect($user->invoices ?? [])->sum('grand_total'), 0) }}</h4>
                    <p class="fw-bold mb-0">إجمالي المدفوعات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-2 border-danger bg-transparent text-danger">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h4 class="fw-bold">{{ number_format(collect($user->invoices ?? [])->sum('late_fee_total'), 0) }}</h4>
                    <p class="fw-bold mb-0">إجمالي رسوم التأخير</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contracts Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-dark border-dark  d-flex align-items-center">
                    <i class="fas fa-handshake me-2"></i>
                    <h5 class="mb-0">العقود</h5>
                </div>
                <div class="card-body">
                    @if(isset($user->contracts) && count($user->contracts) > 0)
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center bg-dark text-white">رقم العقد</th>
                                        <th class="text-center bg-dark text-white">تاريخ العقد</th>
                                        <th class="text-center bg-dark text-white">تاريخ الانتهاء المتوقع</th>
                                        <th class="text-center bg-dark text-white">تاريخ الانتهاء الفعلي</th>
                                        <th class="text-center bg-dark text-white">حالة العقد</th>
                                        <th class="text-center bg-dark text-white">عدد الحاويات</th>
                                        <th class="text-center bg-dark text-white">السعر</th>
                                        <th class="text-center bg-dark text-white">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($user->contracts->isEmpty())
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="status-canceled fs-6">لم يتم العثور على اي عقود!</div>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($user->contracts as $contract)
                                            <tr>
                                                <td class="text-center text-primary fw-bold">#{{ $contract->id }}</td>
                                                <td class="text-center">{{ $contract->start_date }}</td>
                                                <td class="text-center">{{ $contract->expected_end_date }}</td>
                                                <td class="text-center">{{ $contract->actual_end_date ?? 'لم ينتهي بعد' }}</td>
                                                <td class="text-center">
                                                    <div class="{{ $contract->status == 'جاري' ? 'status-running' : ($contract->status == 'منتهي' ? 'status-completed' : 'status-canceled') }}">
                                                        {{ $contract->status }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $contract->containers->count() }}</td>
                                                <td class="text-center text-success fw-bold">{{ $contract->price }} ريال</td>
                                                <td class="action-icons text-center">
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" title="طباعة">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-info" title="تحميل PDF">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد عقود متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-dark border-dark d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    <h5 class="mb-0">الفواتير</h5>
                </div>
                <div class="card-body">
                    @if(isset($user->invoices) && count($user->invoices) > 0)
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center bg-dark text-white">رقم الفاتــورة</th>
                                        <th class="text-center bg-dark text-white">رقــم العقــد</th>
                                        <th class="text-center bg-dark text-white">سعــر الإيجــار</th>
                                        <th class="text-center bg-dark text-white">غرامــة التأخيــر</th>
                                        <th class="text-center bg-dark text-white">الضريبـــة المضافــة</th>
                                        <th class="text-center bg-dark text-white">إجمالــي المبلـــغ</th>
                                        <th class="text-center bg-dark text-white">طريقــة الدفـــع</th>
                                        <th class="text-center bg-dark text-white">تاريــخ الفــاتورة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($user->invoices->isEmpty())
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="status-danger fs-6">لا يوجد اي فواتيـــر!</div>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($user->invoices as $invoice)
                                            <tr>
                                                <td class="text-center text-primary fw-bold">#{{ $invoice->id }}</td>
                                                <td class="text-center text-primary fw-bold">#{{ $invoice->contract_id }}</td>
                                                <td class="text-center">{{ $invoice->base_price }}</td>
                                                <td class="text-center">{{ $invoice->late_fee_total }}</td>
                                                <td class="text-center">{{ $invoice->tax_total }}</td>
                                                <td class="text-center text-success fw-bold">{{ $invoice->grand_total }} ريال</td>
                                                <td class="text-center">{{ $invoice->payment_method }}</td>
                                                <td class="text-center">{{ $invoice->date }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-receipt fa-3x mb-3"></i>
                            <p>لا توجد فواتير متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.table th {
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin: 0 1px;
}

.badge {
    font-size: 0.8rem;
    padding: 0.4em 0.8em;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}
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
.table .status-average {
    background-color: #fff3cd;
    color: #856404;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    display: inline-block;
}
.table .status-high {
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