@extends('layouts.app')

@section('title', 'عرض المطالبة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">عرض المطالبة</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('print.invoice', $invoiceStatement->code) }}" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-print me-2"></i>طباعة المطالبة
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row mb-4">
            <!-- Customer Information -->
            <div class="col-lg-6 col-md-12">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-user-tie me-2"></i>بيانات العميل
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">اسم العميل:</strong>
                                    <span class="fw-bold">{{ $invoiceStatement->customer->name ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">رقم العميل:</strong>
                                    <span class="fw-bold">{{ $invoiceStatement->customer->account->code ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">الرقم الضريبي:</strong>
                                    <span class="fw-bold">{{ $invoiceStatement->customer->CR ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">العنوان الوطني:</strong>
                                    <span class="fw-bold">{{ $invoiceStatement->customer->national_address ?? '---' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Information -->
            <div class="col-lg-6 col-md-12">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-file-alt me-2"></i>بيانات المطالبة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم المطالبة:</strong>
                                        <span class="fw-bold text-primary">{{ $invoiceStatement->code ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">التاريخ:</strong>
                                        <span class="fw-bold">{{ Carbon\Carbon::parse($invoiceStatement->date)->format('Y/m/d') ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">أعدت بواسطة:</strong>
                                        <span class="text-muted fw-bold">{{ $invoiceStatement->made_by->name ?? '---' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">ملاحظات:</strong>
                                        <span class="fw-bold">{{ $invoiceStatement->notes ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container Details Table -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-dark">
                    <i class="fas fa-file-invoice me-2"></i>تفاصيل الفواتير
                </h5>
                <span class="badge bg-primary text-white">عدد الفواتير: {{ count($invoiceStatement->invoices) }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            <th class="text-center bg-dark text-white">رقم الفاتورة</th>
                            <th class="text-center bg-dark text-white">نوع الفاتورة</th>
                            <th class="text-center bg-dark text-white">تاريخ الفاتورة</th>
                            <th class="text-center bg-dark text-white">قيمة الفاتورة</th>
                            <th class="text-center bg-dark text-white">اعدت بواسطة</th>
                            <th class="text-center bg-dark text-white">الإجرائات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoiceStatement->invoices as $index => $invoice)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td class="text-center text-primary fw-bold">
                                    @if($invoice->type == 'خدمات')
                                        <a href="{{ route('invoices.services.details', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'تخزين')
                                        <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'تخليص')
                                        <a href="{{ route('invoices.clearance.details', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">{{ $invoice->type ?? '-' }}</td>
                                <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center fw-bold">{{ $invoice->total_amount }}</td>
                                <td class="text-center">{{ $invoice->made_by->name ?? '-' }}</td>
                                <td class="d-flex justify-content-center align-items-center gap-2 text-center">
                                    @if($invoice->type == 'خدمات')
                                        <a href="{{ route('invoices.services.details', $invoice) }}" class="btn btn-sm btn-primary">
                                            عرض
                                        </a>
                                    @elseif($invoice->type == 'تخزين')
                                        <a href="{{ route('invoices.details', $invoice) }}" class="btn btn-sm btn-primary">
                                            عرض
                                        </a>
                                    @elseif($invoice->type == 'تخليص')
                                        <a href="{{ route('invoices.clearance.details', $invoice) }}" class="btn btn-sm btn-primary">
                                            عرض
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="row">
            <div class="col-5">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-calculator me-2"></i>ملخص المطالبة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الإجمالي قبل الضريبة:</span>
                            <span class="fw-bold fs-5">{{ number_format($invoiceStatement->subtotal, 2) }} ريال</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">إجمالي الضريبة المضافة:</span>
                            <span class="fw-bold fs-5 text-dark"> {{ number_format($invoiceStatement->tax, 2) }} ريال</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-4 text-success">الإجمالي النهائي:</span>
                            <span class="fw-bold fs-3 text-success">{{ number_format($invoiceStatement->amount, 2) }} ريال</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-2"></i>معلومات إضافية
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($hatching_total) && $hatching_total)
                            <div class="mt-2 p-3 bg-light rounded">
                                <h6 class="text-dark fw-bold">المبلغ بالأحرف:</h6>
                                <p class="mb-0 fw-bold fs-3 text-success">{{ $hatching_total }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .qr-code-container svg {
        max-width: 100px;
        height: auto;
    }
    
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .btn-lg {
        padding: 12px 24px;
        font-size: 1.1em;
    }
</style>
@endsection