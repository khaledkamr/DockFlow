@extends('layouts.app')

@section('title', 'تفاصيل السند')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <h2 class="mb-0">تفاصيل السند - {{ $voucher->code }}</h2>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <!-- Header with Status and Actions -->
            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                <div>
                    <span class="badge bg-{{ $voucher->is_posted ? 'success' : 'warning' }} fs-6 px-3 py-2">
                        @if ($voucher->is_posted)
                            <i class="fas fa-check-circle me-1"></i>تم الترحيل
                        @else
                            <i class="fas fa-clock me-1"></i>لم يتم الترحيل
                        @endif
                    </span>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('voucher.print', $voucher) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-print me-2"></i>طباعة السند
                    </a>
                    @if (!$voucher->is_posted)
                        <form action="" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary"
                                onclick="return confirm('هل أنت متأكد من ترحيل هذا السند؟')">
                                <i class="fas fa-file-export me-2"></i>ترحيل السند
                            </button>
                        </form>
                    @endif
                    <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal"
                        data-bs-target="#deleteVoucher">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteVoucher" tabindex="-1" aria-labelledby="deleteVoucherLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteVoucherLabel">تأكيد الحذف</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body text-dark text-center">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                                <p class="mt-3">هل أنت متأكد من حذف السند <strong>{{ $voucher->code }}</strong>؟</p>
                                <p class="text-muted">لا يمكن التراجع عن هذا الإجراء</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary fw-bold"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger fw-bold">حذف السند</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Voucher Information Cards -->
            <div class="row g-3 mb-4">
                <!-- Basic Information -->
                <div class="col-12 col-lg-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2"></i>بيانات السند الأساسية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم السند:</strong>
                                        <span class="fw-bold">{{ $voucher->code }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">نوع السند:</strong>
                                        <span class="fw-bold">{{ $voucher->type }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">التاريخ:</strong>
                                        <span
                                            class="fw-bold">{{ Carbon\Carbon::parse($voucher->date)->format('Y/m/d') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">المبلغ:</strong>
                                        <span class="fw-bold text-primary">{{ number_format($voucher->amount, 2) }}
                                            ر.س</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="text-muted">البيان:</strong>
                                        <span class="fw-bold text-end" style="max-width: 60%;">{{ $voucher->description ?? 'لا يوجد بيان' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">تم الإنشاء بواسطة:</strong>
                                        <span class="fw-bold">{{ $voucher->made_by->name ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="col-12 col-lg-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-book me-2"></i>بيانات الحسابات
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-success">
                                                <i class="fas fa-arrow-down me-1"></i>الحساب المدين:
                                            </strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted">الاسم:</span>
                                            <span class="fw-bold">{{ $voucher->debit_account->name ?? '---' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">الرقم:</span>
                                            <span class="fw-bold">{{ $voucher->debit_account->code ?? '---' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-danger">
                                                <i class="fas fa-arrow-up me-1"></i>الحساب الدائن:
                                            </strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted">الاسم:</span>
                                            <span class="fw-bold">{{ $voucher->credit_account->name ?? '---' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">الرقم:</span>
                                            <span class="fw-bold">{{ $voucher->credit_account->code ?? '---' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-language me-2"></i>التفقيط
                            </h6>
                        </div>
                        <div class="card-body bg-light">
                            <p class="mb-0 text-center fw-bold fs-5 text-primary">
                                {{ $voucher->hatching }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Invoice Information (if exists) -->
            @if (isset($voucher->invoice_id) && $voucher->invoice)
                <div class="row g-3 mt-3">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-file-invoice me-2"></i>الفاتورة المرتبطة
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>رقم الفاتورة:</strong>
                                        <span class="ms-2">{{ $voucher->invoice->code }}</span>
                                    </div>
                                    <a href="{{ route('invoices.details', $voucher->invoice->uuid) }}"
                                        class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-eye me-1"></i>عرض الفاتورة
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attachments Section -->
            <div class="row g-3 mt-3">
                <div class="col-12">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-paperclip me-2"></i>المرفقات
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($voucher->attachments && count($voucher->attachments) > 0)
                                <div class="row g-3">
                                    @foreach($voucher->attachments as $attachment)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card border-light">
                                                <div class="card-body text-center">
                                                    <div class="mb-2">
                                                        @php
                                                            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                                        @endphp
                                                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                            <i class="fas fa-image text-success" style="font-size: 2rem;"></i>
                                                        @elseif(strtolower($extension) == 'pdf')
                                                            <i class="fas fa-file-pdf text-danger" style="font-size: 2rem;"></i>
                                                        @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                                                            <i class="fas fa-file-word text-primary" style="font-size: 2rem;"></i>
                                                        @elseif(in_array(strtolower($extension), ['xls', 'xlsx']))
                                                            <i class="fas fa-file-excel text-success" style="font-size: 2rem;"></i>
                                                        @else
                                                            <i class="fas fa-file text-secondary" style="font-size: 2rem;"></i>
                                                        @endif
                                                    </div>
                                                    <p class="mb-2 fw-bold">{{ $attachment->original_name }}</p>
                                                    <small class="text-muted">{{ number_format($attachment->file_size / 1024, 2) }} KB</small>
                                                    <div class="mt-3">
                                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" 
                                                           class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="fas fa-eye"></i> عرض
                                                        </a>
                                                        <a href="{{ Storage::url($attachment->file_path) }}" download 
                                                           class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-download"></i> تحميل
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">لا توجد مرفقات</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
