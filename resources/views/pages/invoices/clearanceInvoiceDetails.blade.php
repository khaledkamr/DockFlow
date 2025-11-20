@extends('layouts.app')

@section('title', 'عرض الفاتورة الضريبية')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">عرض الفاتورة الضريبية</h2>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-{{ $invoice->isPaid == 'تم الدفع' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                    @if($invoice->isPaid == 'تم الدفع')
                        <i class="fas fa-check-circle me-1"></i>مدفوعة
                    @elseif($invoice->isPaid == 'لم يتم الدفع')
                        <i class="fas fa-clock me-1"></i>غير مدفوعة
                    @endif
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('print.invoice.clearance', $invoice->code) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-print me-2"></i>طباعة الفاتورة
                </a>
                @can('ترحيل فاتورة')
                    <a href="{{ route('invoices.post.clearance', $invoice) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-export me-2"></i>ترحيل الفاتورة
                    </a>
                @endcan
                @if($invoice->isPaid == 'لم يتم الدفع')
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#updateInvoice">
                        <i class="fa-solid fa-pen-to-square me-1"></i> تحديث الحالة
                    </button>
                @endif
            </div>
        </div>

        <div class="modal fade" id="updateInvoice" tabindex="-1" aria-labelledby="updateInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="updateInvoiceLabel">تحديث بيانات الفاتورة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="isPaid" class="form-label">عملية الدفع</label>
                                    <select name="isPaid" class="form-select border-primary" required>
                                        <option value="" selected disabled>اختر عملية الدفع</option>
                                        <option value="تم الدفع">تم الدفع</option>
                                        <option value="لم يتم الدفع">لم يتم الدفع</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">حفظ الفاتورة</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
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
                                    <span class="fw-bold">{{ $invoice->customer->vatNumber ?? '---' }}</span>
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
                                        <strong class="text-muted">نوع الفاتورة:</strong>
                                        <span class="fw-bold">{{ $invoice->type ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">التاريخ:</strong>
                                        <span class="fw-bold">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">أعدت بواسطة:</strong>
                                        <span class="text-muted fw-bold">{{ $invoice->made_by->name ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="qr-code-container">
                                    {!! $qrCode !!}
                                    {{-- <img src="{{ asset('img/qrcode.png') }}" alt="QR Code" width="120"> --}}
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
                            <th class="text-center bg-dark text-white">رقم المعاملة</th>
                            <th class="text-center bg-dark text-white">رقم الحاوية</th>
                            <th class="text-center bg-dark text-white">فئة الحاوية</th>
                            <th class="text-center bg-dark text-white">تاريخ التسجيل</th>
                            <th class="text-center bg-dark text-white">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->containers as $index => $container)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('transactions.details', $container->transactions->first()) }}" class="text-decoration-none">
                                        {{ $container->transactions->first()->code ?? '---' }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('container.details', $container) }}" class="text-decoration-none">
                                        {{ $container->code }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ $container->containerType->name }}
                                </td>
                                <td class="text-center">
                                    <small>{{ \Carbon\Carbon::parse($container->created_at)->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-center fw-bold">{{ $container->notes ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction Items Table -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-dark">
                    <i class="fas fa-boxes me-2"></i>تفاصيل البنود
                </h5>
                <span class="badge bg-primary text-white">عدد البنود: {{ count($invoice->containers->first()->transactions->first()->items) }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            <th class="text-center bg-dark text-white">البند</th>
                            <th class="text-center bg-dark text-white">المبلغ</th>
                            <th class="text-center bg-dark text-white">الضريبة</th>
                            <th class="text-center bg-dark text-white">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->clearanceInvoiceItems->sortBy('number') as $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $item->number }}</td>
                                <td class="text-center fw-bold">
                                    {{ $item->description }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($item->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($item->tax, 2) }}
                                </td>
                                <td class="text-center fw-bold">
                                    <small>{{ number_format($item->total, 2) }}</small>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-secondary">
                            <td colspan="2" class="text-center fw-bold">الإجمالي</td>
                            <td class="text-center fw-bold">{{ number_format($invoice->amount_before_tax, 2) }}</td>
                            <td class="text-center fw-bold">{{ number_format($invoice->tax, 2) }}</td>
                            <td class="text-center fw-bold">{{ number_format($invoice->total_amount, 2) }} <i data-lucide="saudi-riyal"></i></td>
                        </tr>
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
                            <span class="fw-bold fs-5">{{ number_format($invoice->amount_before_tax, 2) }} <i data-lucide="saudi-riyal"></i></span>
                        </div>
                         
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الخصم ({{ $invoice->discount ? $invoice->discount . '%' : '0%' }}):</span>
                            <span class="fw-bold fs-5">{{ number_format($discountValue, 2) }} <i data-lucide="saudi-riyal"></i></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">إجمالي الضريبة المضافة:</span>
                            <span class="fw-bold fs-5 text-dark"> {{ number_format($invoice->tax, 2) }} <i data-lucide="saudi-riyal"></i></span>
                        </div>
                       
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-4 text-success">الإجمالي النهائي:</span>
                            <span class="fw-bold fs-3 text-success">{{ number_format($invoice->total_amount, 2) }} <i data-lucide="saudi-riyal" style="width: 32px; height: 32px;"></i></span>
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