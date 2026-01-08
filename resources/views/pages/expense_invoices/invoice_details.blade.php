@extends('layouts.app')

@section('title', 'عرض فاتورة المصاريف')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <h2 class="mb-0">عرض فاتورة المصاريف</h2>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                <div>
                    <span class="badge bg-{{ $invoice->is_paid ? 'success' : 'danger' }} fs-6 px-3 py-2">
                        @if ($invoice->is_paid)
                            <i class="fas fa-check-circle me-1"></i>مدفوعة
                        @elseif(!$invoice->is_paid)
                            <i class="fas fa-clock me-1"></i>غير مدفوعة
                        @endif
                    </span>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('print.expense.invoice', $invoice->code) }}" target="_blank"
                        class="btn btn-outline-primary">
                        <i class="fas fa-print me-2"></i>طباعة الفاتورة
                    </a>
                    @if (!$invoice->is_posted)
                        @can('ترحيل فاتورة')
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#journalPreviewModal">
                                <i class="fas fa-file-export me-2"></i>ترحيل الفاتورة
                            </button>
                        @endcan
                    @endif
                    @if (!$invoice->is_paid)
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#updateInvoice">
                            <i class="fa-solid fa-pen-to-square me-1"></i> تحديث الحالة
                        </button>
                    @endif
                </div>
            </div>

            <div class="modal fade" id="updateInvoice" tabindex="-1" aria-labelledby="updateInvoiceLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white fw-bold" id="updateInvoiceLabel">تحديث بيانات الفاتورة</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('expense.invoices.update.status', $invoice) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body text-dark">
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="isPaid" class="form-label">عملية الدفع</label>
                                        <select name="is_paid" class="form-select border-primary" required>
                                            <option value="" selected disabled>اختر عملية الدفع</option>
                                            <option value="1">تم الدفع</option>
                                            <option value="0">لم يتم الدفع</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary fw-bold">حفظ الفاتورة</button>
                                <button type="button" class="btn btn-secondary fw-bold"
                                    data-bs-dismiss="modal">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Journal Preview Modal -->
            <div class="modal fade" id="journalPreviewModal" tabindex="-1" aria-labelledby="journalPreviewModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white fw-bold" id="journalPreviewModalLabel">معاينة قيد اليومية</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-dark p-4">
                            <div class="table-container">
                                <table class="table table-bordered border-secondary table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">رقم الحساب</th>
                                            <th class="text-center">اسم الحساب</th>
                                            <th class="text-center">مدين</th>
                                            <th class="text-center">دائن</th>
                                            <th class="text-center">البيان</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $counter = 1; @endphp

                                        {{-- Invoice Items --}}
                                        @foreach ($invoice->items as $item)
                                            <tr>
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td class="text-center">{{ $item->account->code }}</td>
                                                <td class="text-center fw-semibold">{{ $item->account->name }}</td>
                                                <td class="text-center fw-bold text-success">{{ number_format($item->amount, 2) }}</td>
                                                <td class="text-center">0.00</td>
                                                <td class="text-center">
                                                    {{ $item->description ?: 'بند فاتورة مصاريف رقم ' . $invoice->code }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($invoice->tax > 0)
                                            <tr>
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td class="text-center">{{ $tax_account->code ?? '-' }}</td>
                                                <td class="text-center fw-semibold">{{ $tax_account->name ?? '-' }}</td>
                                                <td class="text-center fw-bold text-success">{{ number_format($invoice->tax, 2) }}</td>
                                                <td class="text-center">0.00</td>
                                                <td class="text-center">ضريبة قيمة مضافة فاتورة مصاريف رقم {{ $invoice->code }}</td>
                                            </tr>
                                        @endif

                                        <tr class="table-warning border-secondary">
                                            <td class="text-center">{{ $counter++ }}</td>
                                            <td class="text-center">{{ $invoice->supplier->account->code ?? '-' }}</td>
                                            <td class="text-center fw-semibold">{{ $invoice->supplier->account->name ?? 'مورد' }}</td>
                                            <td class="text-center">0.00</td>
                                            <td class="text-center fw-bold text-danger">{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td class="text-center">فاتورة مصاريف رقم {{ $invoice->code }}</td>
                                        </tr>

                                        @if ($invoice->payment_method !== 'آجل')
                                            <tr class="table-info border-secondary">
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td class="text-center">{{ $invoice->supplier->account->code ?? '-' }}</td>
                                                <td class="text-center fw-semibold">{{ $invoice->supplier->name ?? 'مورد' }}</td>
                                                <td class="text-center fw-bold text-success">{{ number_format($invoice->total_amount, 2) }}</td>
                                                <td class="text-center">0.00</td>
                                                <td class="text-center">سداد فاتورة مصاريف رقم {{ $invoice->code }}</td>
                                            </tr>

                                            <tr class="table-info border-secondary">
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td class="text-center">{{ $invoice->expense_account->code ?? '-' }}</td>
                                                <td class="text-center fw-semibold">{{ $invoice->expense_account->name ?? '-' }}</td>
                                                <td class="text-center">0.00</td>
                                                <td class="text-center fw-bold text-danger">{{ number_format($invoice->total_amount, 2) }}</td>
                                                <td class="text-center">سداد فاتورة مصاريف رقم {{ $invoice->code }}</td>
                                            </tr>
                                        @endif

                                        <tr class="table-dark">
                                            <td class="text-center text-white fw-bold" colspan="3">الإجمالي</td>
                                            <td class="text-center text-white fw-bold">
                                                @php
                                                    $totalDebit = $invoice->items->sum('amount') + $invoice->tax;
                                                    if ($invoice->payment_method !== 'آجل') {
                                                        $totalDebit += $invoice->total_amount;
                                                    }
                                                @endphp
                                                {{ number_format($totalDebit, 2) }}
                                            </td>
                                            <td class="text-center text-white fw-bold">
                                                @php
                                                    $totalCredit = $invoice->total_amount;
                                                    if ($invoice->payment_method !== 'آجل') {
                                                        $totalCredit += $invoice->total_amount;
                                                    }
                                                @endphp
                                                {{ number_format($totalCredit, 2) }}
                                            </td>
                                            <td class="text-center text-white"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <form action="{{ route('expense.invoices.post', $invoice) }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary fw-bold">
                                    <i class="fas fa-check me-2"></i>تأكيد الترحيل
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <!-- Supplier Information -->
                <div class="col-12 col-lg-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-building me-2"></i>بيانات المورد
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">اسم المورد:</strong>
                                        <span class="fw-bold">{{ $invoice->supplier->name ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم المورد:</strong>
                                        <span class="fw-bold">{{ $invoice->supplier->account->code ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">الرقم الضريبي:</strong>
                                        <span class="fw-bold">{{ $invoice->supplier->vat_number ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">العنوان الوطني:</strong>
                                        <span class="fw-bold">{{ $invoice->supplier->national_address ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Information -->
                <div class="col-12 col-lg-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-file-alt me-2"></i>بيانات الفاتورة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم الفاتورة:</strong>
                                        <span class="fw-bold text-primary">{{ $invoice->code ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">رقم فاتورة المورد:</strong>
                                        <span class="fw-bold">{{ $invoice->supplier_invoice_number ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">التاريخ:</strong>
                                        <span
                                            class="fw-bold">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">طريقة الدفع:</strong>
                                        <span class="fw-bold">{{ $invoice->payment_method ?? '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-list me-2"></i>تفاصيل البنود
                    </h5>
                    <span class="badge bg-primary text-white">عدد البنود: {{ count($invoice->items) }}</span>
                </div>

                <div class="table-container">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center bg-dark text-white text-nowrap">#</th>
                                <th class="text-center bg-dark text-white text-nowrap">البند</th>
                                <th class="text-center bg-dark text-white text-nowrap">البيان</th>
                                <th class="text-center bg-dark text-white text-nowrap">الكمية</th>
                                <th class="text-center bg-dark text-white text-nowrap">السعر</th>
                                <th class="text-center bg-dark text-white text-nowrap">مركز التكلفة</th>
                                <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                                <th class="text-center bg-dark text-white text-nowrap">الضريبة</th>
                                <th class="text-center bg-dark text-white text-nowrap">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = 1;
                            @endphp
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td class="text-center fw-bold">{{ $index++ }}</td>
                                    <td class="text-center fw-bold" style="min-width: 200px">
                                        {{ $item->account->name ?? '---' }}
                                    </td>
                                    <td class="text-center" style="min-width: 200px">
                                        {{ $item->description ?? '---' }}
                                    </td>
                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                    <td class="text-center">{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center" style="min-width: 150px">
                                        {{ $item->costCenter->name ?? '---' }}</td>
                                    <td class="text-center">{{ number_format($item->amount, 2) }}</td>
                                    <td class="text-center">{{ number_format($item->tax, 2) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-calculator me-2"></i>ملخص الفاتورة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">الإجمالي قبل الضريبة:</span>
                                <span class="fw-bold fs-5">{{ number_format($invoice->amount_before_tax, 2) }} <i
                                        data-lucide="saudi-riyal"></i></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">الخصم
                                    ({{ $invoice->discount ? $invoice->discount . '%' : '0%' }}):</span>
                                <span class="fw-bold fs-5">{{ number_format($discountValue ?? 0, 2) }} <i
                                        data-lucide="saudi-riyal"></i></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">إجمالي الضريبة المضافة:</span>
                                <span class="fw-bold fs-5 text-dark"> {{ number_format($invoice->tax, 2) }} <i
                                        data-lucide="saudi-riyal"></i></span>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-4 text-success">الإجمالي النهائي:</span>
                                <span class="fw-bold fs-3 text-success">{{ number_format($invoice->total_amount, 2) }} <i
                                        data-lucide="saudi-riyal" style="width: 32px; height: 32px;"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2"></i>معلومات إضافية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <form action="{{ route('expense.invoices.update.notes', $invoice) }}" method="POST"
                                        class="d-flex flex-column flex-md-row align-items-stretch align-items-md-end gap-3"
                                        id="notesForm">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="notes" class="form-label fw-bold">الملاحظات:</label>
                                                <button type="submit" id="saveNotesBtn"
                                                    class="btn btn-sm btn-primary align-self-md-end mb-3"
                                                    style="white-space: nowrap; display: none;">
                                                    حفظ الملاحظات
                                                </button>
                                            </div>
                                            <textarea name="notes" id="notes" class="form-control border-2" rows="3">{{ $invoice->notes ?? '' }}</textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @if (isset($hatching_total) && $hatching_total)
                                <h6 class="text-dark fw-bold">المبلغ بالأحرف:</h6>
                                <p class="mb-0 fw-bold fs-3 text-success">{{ $hatching_total }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments Section -->
            <div class="mt-4">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-paperclip me-2"></i>المرفقات
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <form action="{{ route('expense.invoices.add.attachment', $invoice) }}" method="POST"
                                    enctype="multipart/form-data"
                                    class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                    @csrf
                                    <div class="flex-grow-1">
                                        <input type="file" name="attachment" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i>
                                        إرفاق ملف
                                    </button>
                                </form>
                                <small class="text-muted mt-1 d-block">
                                    يمكنك إرفاق الملفات التالية: PDF, صور
                                </small>
                            </div>
                        </div>

                        @if ($invoice->attachments && $invoice->attachments->count() > 0)
                            <div class="row g-3">
                                @foreach ($invoice->attachments as $attachment)
                                    <div class="col-12 col-lg-6">
                                        <div
                                            class="alert alert-primary border-2 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @php
                                                        $extension = pathinfo(
                                                            $attachment->file_name,
                                                            PATHINFO_EXTENSION,
                                                        );
                                                        $iconClass = 'fas fa-file';
                                                        $iconColor = 'text-secondary';

                                                        switch (strtolower($extension)) {
                                                            case 'pdf':
                                                                $iconClass = 'fas fa-file-pdf';
                                                                $iconColor = 'text-danger';
                                                                break;
                                                            case 'doc':
                                                            case 'docx':
                                                                $iconClass = 'fas fa-file-word';
                                                                $iconColor = 'text-primary';
                                                                break;
                                                            case 'xls':
                                                            case 'xlsx':
                                                                $iconClass = 'fas fa-file-excel';
                                                                $iconColor = 'text-success';
                                                                break;
                                                            case 'jpg':
                                                            case 'jpeg':
                                                            case 'png':
                                                            case 'gif':
                                                                $iconClass = 'fas fa-file-image';
                                                                $iconColor = 'text-info';
                                                                break;
                                                            case 'txt':
                                                                $iconClass = 'fas fa-file-alt';
                                                                $iconColor = 'text-dark';
                                                                break;
                                                        }
                                                    @endphp
                                                    <i class="{{ $iconClass }} {{ $iconColor }}"
                                                        style="font-size: 2rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 alert-heading">{{ $attachment->file_name }}</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        أرفق بواسطة
                                                        {{ $attachment->made_by ? $attachment->made_by->name : 'غير محدد' }}
                                                        في
                                                        {{ $attachment->created_at->format('Y/m/d') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                    download="{{ $attachment->file_name }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteAttachmentModal{{ $attachment->id }}"
                                                    title="حذف المرفق">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Attachment Modal -->
                                    <div class="modal fade" id="deleteAttachmentModal{{ $attachment->id }}"
                                        tabindex="-1" aria-labelledby="deleteAttachmentModalLabel{{ $attachment->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white fw-bold"
                                                        id="deleteAttachmentModalLabel{{ $attachment->id }}">
                                                        تأكيد حذف المرفق
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-dark">
                                                    <p class="mb-3">هل أنت متأكد من حذف هذا المرفق؟</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>{{ $attachment->file_name }}</strong>
                                                        <br>
                                                        <small>لن تتمكن من استرداد هذا الملف بعد حذفه</small>
                                                    </div>
                                                </div>
                                                <div
                                                    class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                                    <form
                                                        action="{{ route('expense.invoices.delete.attachment', $attachment) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger fw-bold order-1 order-sm-1">
                                                            حذف المرفق
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        class="btn btn-secondary fw-bold order-2 order-sm-2"
                                                        data-bs-dismiss="modal">
                                                        إلغاء
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center pb-4 mt-2">
                                <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">لا توجد مرفقات للفاتورة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-3">
        <small class="text-muted">
            تم إنشاء هذه الفاتورة بواسطة: {{ $invoice->made_by->name ?? 'غير محدد' }}
        </small>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notesTextarea = document.getElementById('notes');
            const saveNotesBtn = document.getElementById('saveNotesBtn');
            const originalNotes = "{{ $invoice->notes ?? '' }}";

            function checkNotesChanged() {
                const currentNotes = notesTextarea.value;
                if (currentNotes !== originalNotes) {
                    saveNotesBtn.style.display = 'block';
                } else {
                    saveNotesBtn.style.display = 'none';
                }
            }

            notesTextarea.addEventListener('input', checkNotesChanged);
            notesTextarea.addEventListener('change', checkNotesChanged);
        });
    </script>
@endsection
