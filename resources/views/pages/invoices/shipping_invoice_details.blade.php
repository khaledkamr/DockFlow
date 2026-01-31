@extends('layouts.app')

@section('title', 'عرض الفاتورة الضريبية')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">عرض الفاتورة الضريبية</h2>
        @if(auth()->user()->roles()->pluck('name')->contains('Admin'))
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editInvoiceModal">
                    <i class="fas fa-edit me-2"></i>تعديل الفاتورة
                </button>

                <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteInvoiceModal">
                    <i class="fas fa-trash me-2"></i>حذف الفاتورة
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Invoice Modal -->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="editInvoiceModalLabel">تعديل بيانات الفاتورة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body text-dark">
                        @if ($invoice->is_posted)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>تنبيه:</strong> هذه الفاتورة تم ترحيلها بالفعل. يجب حذف القيد المرتبط أولاً قبل تعديل الفاتورة.
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="code" class="form-label">رقم الفاتورة</label>
                                <input type="text" name="code" class="form-control border-primary" value="{{ $invoice->code }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="date" class="form-label">التاريخ</label>
                                <input type="date" name="date" class="form-control border-primary" value="{{ Carbon\Carbon::parse($invoice->date)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="is_posted" class="form-label">حالة الترحيل</label>
                                <select name="is_posted" class="form-select border-primary" required>
                                    <option value="0" {{ !$invoice->is_posted ? 'selected' : '' }}>لم يتم الترحيل</option>
                                    <option value="1" {{ $invoice->is_posted ? 'selected' : '' }}>تم الترحيل</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="isPaid" class="form-label">حالة الدفع</label>
                                <select name="isPaid" class="form-select border-primary" required>
                                    <option value="تم الدفع" {{ $invoice->isPaid == 'تم الدفع' ? 'selected' : '' }}>تم الدفع</option>
                                    <option value="لم يتم الدفع" {{ $invoice->isPaid == 'لم يتم الدفع' ? 'selected' : '' }}>لم يتم الدفع</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="amount_before_tax" class="form-label">المبلغ قبل الضريبة</label>
                                <input type="number" step="0.01" name="amount_before_tax" class="form-control border-primary" value="{{ $invoice->amount_before_tax }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="tax_rate" class="form-label">نسبة الضريبة (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control border-primary" value="{{ $invoice->tax_rate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="tax" class="form-label">قيمة الضريبة</label>
                                <input type="number" step="0.01" name="tax" class="form-control border-primary" value="{{ $invoice->tax }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="discount" class="form-label">الخصم (%)</label>
                                <input type="number" step="0.01" name="discount" class="form-control border-primary" value="{{ $invoice->discount }}">
                            </div>
                            <div class="col-md-3">
                                <label for="amount_after_discount" class="form-label">المبلغ بعد الخصم</label>
                                <input type="number" step="0.01" name="amount_after_discount" class="form-control border-primary" value="{{ $invoice->amount_after_discount }}">
                            </div>
                            <div class="col-md-3">
                                <label for="total_amount" class="form-label">المبلغ الإجمالي</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control border-primary" value="{{ $invoice->total_amount }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateInvoice" tabindex="-1" aria-labelledby="updateInvoiceLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="updateInvoiceLabel">تحديث بيانات الفاتورة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <button type="button" class="btn btn-secondary fw-bold"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Invoice Modal -->
    <div class="modal fade" id="deleteInvoiceModal" tabindex="-1" aria-labelledby="deleteInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white fw-bold" id="deleteInvoiceModalLabel">تأكيد حذف الفاتورة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark">
                    <p class="text-center fw-bold mb-3">هل أنت متأكد من حذف هذه الفاتورة؟</p>
                    @if($invoice->is_posted)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>تنبيه:</strong> هذه الفاتورة تم ترحيلها بالفعل. يجب حذف القيد المرتبط أولاً قبل حذف الفاتورة.
                        </div>
                    @endif
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>رقم الفاتورة: {{ $invoice->code }}</strong>
                        <br>
                        <small>لن تتمكن من استرداد هذه الفاتورة بعد حذفها</small>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <form action="{{ route('invoices.delete', $invoice) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">حذف الفاتورة</button>
                    </form>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Invoice Preview Modal -->
    @if (!$invoice->is_posted)
        <div class="modal fade" id="postInvoiceModal" tabindex="-1" aria-labelledby="postInvoiceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white fw-bold" id="postInvoiceModalLabel">
                            <i class="fas fa-file-export me-2"></i>معاينة القيد قبل الترحيل
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">الحساب</th>
                                        <th class="text-center">مدين</th>
                                        <th class="text-center">دائن</th>
                                        <th class="text-center">البيان</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            @if ($invoice->type == 'تخزين')
                                                ايرادات التخزين
                                            @elseif($invoice->type == 'تخليص')
                                                ايرادات تخليص جمركي
                                            @elseif($invoice->type == 'شحن')
                                                ايرادات النقليات
                                            @else
                                                ايرادات متنوعة
                                            @endif
                                        </td>
                                        <td class="text-center">0.00</td>
                                        <td class="text-center text-success fw-bold">
                                            {{ number_format($invoice->amount_after_discount, 2) }}
                                        </td>
                                        <td class="text-center">
                                            ايرادات
                                            {{ $invoice->type == 'تخزين' || $invoice->type == 'تخليص' || $invoice->type == 'شحن' ? $invoice->type : 'متنوعة' }}
                                            فاتورة رقم {{ $invoice->code }}
                                        </td>
                                    </tr>
                                    {{-- سطر الضريبة --}}
                                    <tr>
                                        <td class="text-center">ضريبة القيمة المضافة من الايرادات</td>
                                        <td class="text-center">0.00</td>
                                        <td class="text-center text-success fw-bold">{{ number_format($invoice->tax, 2) }}</td>
                                        <td class="text-center">قيمة مضافة فاتورة {{ $invoice->type }} رقم {{ $invoice->code }}</td>
                                    </tr>
                                    {{-- سطر العميل --}}
                                    <tr>
                                        <td class="text-center">{{ $invoice->customer->account->name ?? $invoice->customer->name }}</td>
                                        <td class="text-center text-danger fw-bold">{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="text-center">0.00</td>
                                        <td class="text-center">استحقاق فاتورة {{ $invoice->type }} رقم {{ $invoice->code }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th class="text-center fw-bold">الإجمالي</th>
                                        <th class="text-center fw-bold text-danger">{{ number_format($invoice->total_amount, 2) }}</th>
                                        <th class="text-center fw-bold text-success">{{ number_format($invoice->total_amount, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>تنبيه:</strong> بعد ترحيل الفاتورة لن تتمكن من تعديلها أو حذفها إلا بعد حذف القيد
                            المرتبط بها.
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <a href="{{ route('invoices.post', $invoice) }}" class="btn btn-primary fw-bold">
                            <i class="fas fa-check me-2"></i>تأكيد الترحيل
                        </a>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $invoice->isPaid == 'تم الدفع' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                        @if ($invoice->isPaid == 'تم الدفع')
                            <i class="fas fa-check-circle me-1"></i>مدفوعة
                        @elseif($invoice->isPaid == 'لم يتم الدفع')
                            <i class="fas fa-clock me-1"></i>غير مدفوعة
                        @endif
                    </span>
                    @if ($invoice->isPaid == 'لم يتم الدفع' && $invoice->customer->contract)
                        @php
                            $paymentDueDate = Carbon\Carbon::parse($invoice->date)->addDays((int) ($invoice->customer->contract->payment_grace_period ?? 0));
                            $lateDays = Carbon\Carbon::now()->gt($paymentDueDate) ? Carbon\Carbon::parse($paymentDueDate)->diffInDays(Carbon\Carbon::now()) : 0;
                        @endphp
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-calendar me-1"></i>موعد السداد: {{ $paymentDueDate->format('Y/m/d') }}
                        </span>
                        @if($lateDays > 0)
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-exclamation-circle me-1"></i>متأخر منذ {{ (int) $lateDays }} يوم
                            </span>
                        @endif
                    @endif
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('print.invoice.shipping', $invoice->code) }}" target="_blank"
                        class="btn btn-outline-primary">
                        <i class="fas fa-print me-2"></i>طباعة الفاتورة
                    </a>
                    @if (!$invoice->is_posted)
                        @can('ترحيل فاتورة')
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#postInvoiceModal">
                                <i class="fas fa-file-export me-2"></i>ترحيل الفاتورة
                            </button>
                        @endcan
                    @endif
                    @if ($invoice->isPaid == 'لم يتم الدفع')
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#updateInvoice">
                            <i class="fa-solid fa-pen-to-square me-1"></i> تحديث الحالة
                        </button>
                    @endif
                </div>
            </div>

            <div class="row mb-4 g-3">
                <!-- Customer Information -->
                <div class="col-12 col-sm-6 col-lg-6">
                    <div class="card border-dark h-100">
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
                <div class="col-12 col-sm-6 col-lg-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-file-alt me-2"></i>بيانات الفاتورة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-8">
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
                                            <span
                                                class="fw-bold">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-muted">طريقة الدفع:</strong>
                                            <span class="fw-bold">{{ $invoice->payment_method ?? '---' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4 text-center">
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

            <!-- Goods Details Table -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-shipping-fast me-2"></i>تفاصيل البوالص
                    </h5>
                    <span class="badge bg-primary text-white">عدد البوالص: {{ count($invoice->shippingPolicies) }}</span>
                </div>

                <div class="table-container" id="tableContainer">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center bg-dark text-white text-nowrap">#</th>
                                <th class="text-center bg-dark text-white text-nowrap">رقم البوليصة</th>
                                <th class="text-center bg-dark text-white text-nowrap">تاريخ البوليصة</th>
                                <th class="text-center bg-dark text-white text-nowrap">البيان</th>
                                <th class="text-center bg-dark text-white text-nowrap">اسم السائق</th>
                                <th class="text-center bg-dark text-white text-nowrap">رقم اللوحة</th>
                                <th class="text-center bg-dark text-white text-nowrap">مكان التحميل</th>
                                <th class="text-center bg-dark text-white text-nowrap">مكان التسليم</th>
                                <th class="text-center bg-dark text-white text-nowrap">مصاريف اخرى</th>
                                <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                                <th class="text-center bg-dark text-white text-nowrap">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->shippingPolicies as $index => $policy)
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td class="text-center fw-bold">
                                        <a href="{{ route('shipping.policies.details', $policy) }}"
                                            class="text-decoration-none">
                                            {{ $policy->code ?? '---' }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{ $policy->date ? Carbon\Carbon::parse($policy->date)->format('d/m/Y') : '---' }}
                                    </td>
                                    <td class="text-center fw-bold text-nowrap">{{ $policy->goods->first()->description ?? '---' }}
                                    </td>
                                    @if ($policy->type == 'ناقل داخلي')
                                        <td class="text-center fw-bold">{{ $policy->driver->name }}</td>
                                        <td class="text-center fw-bold">{{ $policy->vehicle->plate_number }}</td>
                                    @elseif ($policy->type == 'ناقل خارجي')
                                        <td class="text-center fw-bold">{{ $policy->driver_name }}</td>
                                        <td class="text-center fw-bold">{{ $policy->vehicle_plate }}</td>
                                    @endif
                                    <td class="text-center">
                                        <i class="fas fa-map-marker-alt text-danger"></i> {{ $policy->from }}
                                    </td>
                                    <td class="text-center">
                                        <i class="fas fa-map-marker-alt text-danger"></i> {{ $policy->to }}
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ $policy->other_expenses }}</i>
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ number_format($policy->client_cost, 2) }}</i>
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        {{ number_format($policy->total_cost, 2) }} <i data-lucide="saudi-riyal"></i>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-3">
                <div class="col-12 col-lg-5">
                    <div class="card border-dark">
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
                                <span class="text-muted">الخصم ({{ $invoice->discount }}):</span>
                                <span
                                    class="fw-bold fs-5">{{ number_format(($invoice->amount_before_tax * ($invoice->discount ?? 0)) / 100, 2) }}
                                    <i data-lucide="saudi-riyal"></i></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">الضريبة المضافة ({{ $invoice->tax_rate }}%):</span>
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
                <div class="col-12 col-lg-7">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2"></i>معلومات إضافية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3 d-none d-md-flex">
                                <div class="col-12 col-sm-6">
                                    <p><strong>تاريخ الإنشاء:</strong></p>
                                    <p><small class="text-muted">{{ $invoice->created_at->format('d/m/Y - H:i') }}</small>
                                    </p>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <p><strong>آخر تحديث:</strong></p>
                                    <p><small class="text-muted">{{ $invoice->updated_at->format('d/m/Y - H:i') }}</small>
                                    </p>
                                </div>
                            </div>

                            @if (isset($hatching_total) && $hatching_total)
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-dark fw-bold">المبلغ بالأحرف:</h6>
                                    <p class="mb-0 fw-bold fs-3 text-success">{{ $hatching_total }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments Section -->
            <div class="row mt-4">
                <div class="col-12 col-md-6">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-paperclip me-2"></i>المرفقات
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <form action="{{ route('invoices.add.file', $invoice) }}" method="POST"
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
    
                            @if($invoice->attachments && $invoice->attachments->count() > 0)
                                <div class="row g-3">
                                    @foreach($invoice->attachments as $attachment)
                                        <div class="col-12">
                                            <div class="alert alert-primary border-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @php
                                                            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
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
                                                        <i class="{{ $iconClass }} {{ $iconColor }}" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 alert-heading">{{ $attachment->file_name }}</h6>
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            أرفق بواسطة {{ $attachment->made_by ? $attachment->made_by->name : 'غير محدد' }} في 
                                                            {{ $attachment->created_at->format('Y/m/d') }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                        download="{{ $attachment->file_name }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        type="button" data-bs-toggle="modal" data-bs-target="#deleteAttachmentModal{{ $attachment->id }}"
                                                        title="حذف المرفق">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Delete Attachment Modal -->
                                        <div class="modal fade" id="deleteAttachmentModal{{ $attachment->id }}" tabindex="-1"
                                            aria-labelledby="deleteAttachmentModalLabel{{ $attachment->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white fw-bold" id="deleteAttachmentModalLabel{{ $attachment->id }}">
                                                            تأكيد حذف المرفق
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                                        <form action="{{ route('invoices.delete.file', $attachment) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger fw-bold order-1 order-sm-1">
                                                                حذف المرفق
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2" data-bs-dismiss="modal">
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
                <div class="col-12 col-md-6">
                    <div class="card border-dark h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-sticky-note me-2"></i>الملاحظات
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="notesForm">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <textarea name="notes" class="form-control" rows="5" id="notesTextarea"
                                        placeholder="أدخل ملاحظات الفاتورة هنا..." data-original="{{ $invoice->notes }}">{{ $invoice->notes }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="saveNotesBtn" style="display: none;">
                                    <i class="fas fa-save me-2"></i>حفظ الملاحظات
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-3">
        <small class="text-muted">
            تم إنشاء هذه الفاتورة بواسطة <strong>{{ $invoice->made_by->name ?? 'غير محدد' }}</strong>
        </small>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('notesTextarea');
            const saveBtn = document.getElementById('saveNotesBtn');
            const originalValue = textarea.dataset.original;

            textarea.addEventListener('input', function() {
                if (this.value !== originalValue) {
                    saveBtn.style.display = 'inline-block';
                } else {
                    saveBtn.style.display = 'none';
                }
            });
        });
    </script>

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
