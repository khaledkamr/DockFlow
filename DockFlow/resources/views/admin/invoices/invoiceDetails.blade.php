@extends('layouts.admin')

@section('title', 'عرض الفاتورة الضريبية')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">عرض الفاتورة الضريبية</h2>
    
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-{{ $invoice->payment == 'تم الدفع' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                    @if($invoice->payment == 'تم الدفع')
                        <i class="fas fa-check-circle me-1"></i>مدفوعة
                    @elseif($invoice->payment == 'لم يتم الدفع')
                        <i class="fas fa-clock me-1"></i>معلقة
                    @endif
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('print.invoice', $invoice->code) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-print me-2"></i>طباعة الفاتورة
                </a>
                <a href="" class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>تحميل PDF
                </a>
            </div>
        </div>
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
                                    <span class="fw-bold">{{ $invoice->customer->name ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">رقم العميل:</strong>
                                    <span class="fw-bold">{{ $invoice->customer->account->code ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">الرقم الضريبي:</strong>
                                    <span class="fw-bold">{{ $invoice->customer->CR ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-muted">العنوان الوطني:</strong>
                                    <span class="fw-bold">{{ $invoice->customer->national_address ?? '---' }}</span>
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
                            <i class="fas fa-file-alt me-2"></i>بيانات الفاتورة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم الفاتورة:</strong>
                                        <span class="fw-bold text-primary">{{ $invoice->code ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">التاريخ:</strong>
                                        <span class="fw-bold">{{ $invoice->date ?? now()->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">أعدت بواسطة:</strong>
                                        <span class="text-muted fw-bold">{{ $invoice->made_by ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="qr-code-container">
                                    <img src="{{ asset('img/qrcode.png') }}" alt="QR Code" width="120">
                                </div>
                                <small class="text-muted">QR code</small>
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
                    <i class="fas fa-boxes me-2"></i>تفاصيل الحاويات
                </h5>
                <span class="badge bg-primary text-white">عدد الحاويات: {{ count($invoice->containers) }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            <th class="text-center bg-dark text-white">رقم الإتفاقية</th>
                            <th class="text-center bg-dark text-white">رقم الحاوية</th>
                            <th class="text-center bg-dark text-white">نوع الحاوية</th>
                            <th class="text-center bg-dark text-white">تاريخ الدخول</th>
                            <th class="text-center bg-dark text-white">تاريخ الخروج</th>
                            <th class="text-center bg-dark text-white">أيام التخزين</th>
                            <th class="text-center bg-dark text-white">سعر التخزين</th>
                            <th class="text-center bg-dark text-white">أيام التأخير</th>
                            <th class="text-center bg-dark text-white">غرامة التأخير</th>
                            <th class="text-center bg-dark text-white">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->containers as $index => $container)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('policies.receive.details', $container->policies->where('type', 'إستلام')->first()->id) }}" class="text-decoration-none">
                                        {{ $container->policies->where('type', 'إستلام')->first()->code ?? '---' }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('container.details', $container->id) }}" class="text-decoration-none">
                                        {{ $container->code }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary text-white">{{ $container->containerType->name }}</span>
                                </td>
                                <td class="text-center">
                                    <small>{{ \Carbon\Carbon::parse($container->date)->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <small>{{ $container->exit_date ? \Carbon\Carbon::parse($container->exit_date)->format('d/m/Y') : '---' }}</small>
                                </td>
                                <td class="text-center fw-bold">{{ $container->period }}</td>
                                <td class="text-center fw-bold">{{ number_format($container->storage_price, 2) }}</td>
                                <td class="text-center fw-bold">{{ $container->late_days }}</td>
                                <td class="text-center fw-bold">{{ number_format($container->late_fee, 2) }}</td>
                                <td class="text-center text-primary fw-bold">{{ number_format($container->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="row">
            <div class="col-lg-4 col-md-5">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-calculator me-2"></i>ملخص الفاتورة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الإجمالي قبل الضريبة:</span>
                            <span class="fw-bold fs-5">{{ number_format($invoice->subtotal, 2) }} ريال</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الخصم:</span>
                            <span class="fw-bold fs-5 text-dark">{{ number_format($invoice->discount, 2) }} ريال</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الضريبة المضافة (15%):</span>
                            <span class="fw-bold fs-5 text-dark">{{ number_format($invoice->tax, 2) }} ريال</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-4 text-success">الإجمالي النهائي:</span>
                            <span class="fw-bold fs-3 text-success">{{ number_format($invoice->total, 2) }} ريال</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-2"></i>معلومات إضافية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p><strong>تاريخ الإنشاء:</strong></p>
                                <p><small class="text-muted">{{ $invoice->created_at->format('d/m/Y - H:i') }}</small></p>
                            </div>
                            <div class="col-6">
                                <p><strong>آخر تحديث:</strong></p>
                                <p><small class="text-muted">{{ $invoice->updated_at->format('d/m/Y - H:i') }}</small></p>
                            </div>
                        </div>
                        
                        @if(isset($hatching_total) && $hatching_total)
                            <div class="mt-3 p-3 bg-light rounded">
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