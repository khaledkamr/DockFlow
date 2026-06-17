@extends('layouts.app')

@section('title', 'تقارير الفواتير')

@section('content')
    <h1 class="mb-4">تقارير الفواتير</h1>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form method="GET" id="reportForm">
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">العميل</label>
                    <select name="customer" id="customer_id" class="form-select border-primary">
                        <option value="all" {{ request('customer') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from"
                        value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-sm-6 col-md">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-sm-6 col-md">
                    <label class="form-label">نوع الفاتورة</label>
                    <select name="type" class="form-select border-primary">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md">
                    <label class="form-label">طريقة الدفع</label>
                    <select name="payment_method" class="form-select border-primary">
                        <option value="all" {{ request('payment_method') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="آجل" {{ request('payment_method') == 'آجل' ? 'selected' : '' }}>آجل</option>
                        <option value="كاش" {{ request('payment_method') == 'كاش' ? 'selected' : '' }}>كاش</option>
                        <option value="تحويل بنكي" {{ request('payment_method') == 'تحويل بنكي' ? 'selected' : '' }}>تحويل
                            بنكي</option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md">
                    <label class="form-label">الحالــة</label>
                    <select name="status" class="form-select border-primary">
                        <option value="all"
                            {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                            الكل</option>
                        <option value="تم الدفع" {{ request()->query('status') === 'تم الدفع' ? 'selected' : '' }}>مسددة
                        </option>
                        <option value="لم يتم الدفع" {{ request()->query('status') === 'لم يتم الدفع' ? 'selected' : '' }}>
                            غير مسدد</option>
                        <option value="تم الدفع جزئياً"
                            {{ request()->query('status') === 'تم الدفع جزئياً' ? 'selected' : '' }}>مسددة جزئياً</option>
                        <option value="مسودة" {{ request()->query('status') === 'مسودة' ? 'selected' : '' }}>مسودة
                        </option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label">حالة الترحيل</label>
                    <select name="is_posted" class="form-select border-primary">
                        <option value="all" {{ request('is_posted') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="true" {{ request('is_posted') == 'true' ? 'selected' : '' }}>تم الترحيل</option>
                        <option value="false" {{ request('is_posted') == 'false' ? 'selected' : '' }}>لم يتم الترحيل
                        </option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label">حالة zatca</label>
                    <select name="zatca_status" class="form-select border-primary">
                        <option value="all" {{ request('zatca_status') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="sent without error"
                            {{ request('zatca_status') == 'sent without error' ? 'selected' : '' }}>تم الإرسال بنجاح
                        </option>
                        <option value="sent with error"
                            {{ request('zatca_status') == 'sent with error' ? 'selected' : '' }}>تم الإرسال بخطأ</option>
                        <option value="not sent" {{ request('zatca_status') == 'not sent' ? 'selected' : '' }}>لم يتم
                            الإرسال</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-start">
                    <button id="submitBtn" class="btn btn-primary fw-bold px-4"
                        onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                        عرض التقرير
                        <i class="fa-solid fa-file-circle-check ms-1"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Status Cards -->
    <div class="row g-3 mb-4" id="statusCardsRow">
        <!-- Draft Invoices -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-regular fa-file position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #6c757d;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">مسودات</h6>
                    <h4 class="text-secondary fw-bold" style="font-size: 1.5rem;">
                        {{ $draftInvoicesCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Paid Invoices -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-circle-check position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #198754;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">مسددة</h6>
                    <h4 class="text-success fw-bold" style="font-size: 1.5rem;">
                        {{ $paidInvoicesCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Partially Paid Invoices -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-circle-half-stroke position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #ffc107;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">مسددة جزئياً</h6>
                    <h4 class="text-warning fw-bold" style="font-size: 1.5rem;">
                        {{ $partiallyPaidInvoicesCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Not Paid Invoices -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-circle-xmark position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #dc3545;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">غير مسددة</h6>
                    <h4 class="text-danger fw-bold" style="font-size: 1.5rem;">
                        {{ $unpaidInvoicesCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Sent to ZATCA Successfully -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-paper-plane position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #198754;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">تم الإرسال بنجاح</h6>
                    <h4 class="text-success fw-bold" style="font-size: 1.5rem;">
                        {{ $zatcaAcceptedCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Sent to ZATCA with Error -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-triangle-exclamation position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #ffc107;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">تم الإرسال بخطأ</h6>
                    <h4 class="text-warning fw-bold" style="font-size: 1.5rem;">
                        {{ $zatcaRejectedCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Not Sent to ZATCA -->
        <div class="col-12 col-md-6 col-lg flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <i class="fa-solid fa-ban position-absolute"
                    style="font-size: 4rem; opacity: 0.1; bottom: -5px; left: -5px; color: #dc3545;"></i>
                <div class="card-body text-center p-1 position-relative">
                    <h6 class="card-title text-muted fw-bold mb-1">لم يتم الإرسال</h6>
                    <h4 class="text-danger fw-bold" style="font-size: 1.5rem;">
                        {{ $zatcaPendingCount ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-5">
        <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div>
                <form method="GET" action="">
                    <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                    <select id="per_page" name="per_page" class="form-select form-select-sm d-inline-block w-auto">
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                        <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ $perPage == 1000 ? 'selected' : '' }}>1000</option>
                    </select>
                </form>
            </div>
            <div class="flex-grow-1 mx-2">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-primary"
                            placeholder="ابحث عن فاتورة بالرقم او بإسم العميل او بتاريخ الفاتورة..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                    @foreach (request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('export.excel', 'invoices') }}">
                    @foreach (request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="تصدير Excel">
                        <i class="fa-solid fa-file-excel"></i>
                    </button>
                </form>
                <form action="{{ route('print.invoices.reports') }}" method="GET" target="_blank">
                    @csrf
                    @foreach (request()->except('page', 'per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-outline-primary" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="طباعة">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-container" id="tableContainer">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center bg-dark text-white">#</th>
                        <th class="text-center bg-dark text-white">رقم الفاتورة</th>
                        <th class="text-center bg-dark text-white">نوع الفاتورة</th>
                        <th class="text-center bg-dark text-white">إسم العميل</th>
                        <th class="text-center bg-dark text-white">التاريخ</th>
                        <th class="text-center bg-dark text-white">موعد السداد</th>
                        <th class="text-center bg-dark text-white">المبلغ</th>
                        <th class="text-center bg-dark text-white">الضريبة المضافة</th>
                        <th class="text-center bg-dark text-white">الإجمالي</th>
                        <th class="text-center bg-dark text-white">الحالة</th>
                        <th class="text-center bg-dark text-white">المبلغ المسدد</th>
                        <th class="text-center bg-dark text-white">المبلغ المتبقي</th>
                        <th class="text-center bg-dark text-white">حالة ZATCA</th>
                        <th class="text-center bg-dark text-white">الإجرائات</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($invoices->isEmpty() || !request()->hasAny(['customer', 'from', 'to', 'type', 'payment_method']))
                        <tr>
                            <td colspan="14" class="text-center">
                                <div class="status-danger fs-6">لم يتم العثور على اي فواتير!</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('invoices.unified.details', $invoice) }}" target="_blank"
                                        class="text-decoration-none">
                                        {{ $invoice->code }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $invoice->type }}</td>
                                <td class="text-center">
                                    <a href="{{ route('users.customer.profile', $invoice->customer) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $invoice->customer->name }}
                                    </a>
                                </td>
                                <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center">{{ $invoice->paymentDueDate }}</td>
                                <td class="text-center">{{ $invoice->amount_before_tax }}</td>
                                <td class="text-center">{{ $invoice->tax }}</td>
                                <td class="text-center">{{ $invoice->total_amount }}</td>
                                @if ($invoice->status == 'تم الدفع')
                                    <td class="text-center"><span class="badge status-delivered">مسددة</span></td>
                                @elseif ($invoice->status == 'تم الدفع جزئياً')
                                    <td class="text-center"><span class="badge status-waiting">مسددة جزئياً</span></td>
                                @elseif ($invoice->status == 'لم يتم الدفع')
                                    <td class="text-center"><span class="badge status-danger">غير مسددة</span></td>
                                @elseif ($invoice->status == 'مسودة')
                                    <td class="text-center"><span class="badge status-secondary">مسودة</span></td>
                                @endif
                                <td class="text-center">{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td class="text-center">
                                    {{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @if ($invoice->zatca_status == 'sent without error')
                                        <span class="badge status-delivered">تم الإرسال بنجاح</span>
                                    @elseif ($invoice->zatca_status == 'sent with error')
                                        <span class="badge status-waiting">تم الإرسال بخطأ</span>
                                    @else
                                        <span class="badge status-danger">لم يتم الإرسال</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 text-center">
                                        @if ($invoice->zatca_status !== 'sent without error')
                                            <a href="{{ route('invoices.send.zatca', $invoice) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <span class="d-none d-sm-inline">إرسال</span>
                                                <i class="fa-solid fa-paper-plane d-inline d-sm-none"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('invoices.unified.details', $invoice) }}"
                                            class="btn btn-sm btn-primary">
                                            <span class="d-none d-sm-inline">عرض</span>
                                            <i class="fa-solid fa-eye d-inline d-sm-none"></i>
                                        </a>

                                        @if (auth()->user()->roles()->pluck('name')->contains('Admin'))
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $invoice->id }}">
                                                <span class="d-none d-sm-inline">حذف</span><i
                                                    class="fa-solid fa-trash d-inline d-sm-none"></i>
                                            </button>

                                            <div class="modal fade" id="deleteModal{{ $invoice->id }}" tabindex="-1"
                                                aria-labelledby="deleteModalLabel{{ $invoice->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title fw-bold"
                                                                id="deleteModalLabel{{ $invoice->id }}">تأكيد الحذف</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body fs-6">
                                                            هل أنت متأكد من حذف الفاتورة
                                                            <strong>{{ $invoice->code }}</strong>؟
                                                            @if ($invoice->is_posted)
                                                                <div class="alert alert-danger mt-3">
                                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                                    <strong>تنبيه:</strong> هذه الفاتورة تم ترحيلها بالفعل.
                                                                    يجب حذف
                                                                    القيد المرتبط أولاً قبل حذف الفاتورة.
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-secondary fw-bold"data-bs-dismiss="modal">إلغاء</button>
                                                            <form action="{{ route('invoices.delete', $invoice) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger fw-bold">حذف</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
            <i class="fa-solid fa-arrows-left-right me-1"></i>
            اسحب الجدول لليمين أو اليسار لرؤية المزيد
        </div>

        <div class="mt-4">
            {{ $invoices->links('components.pagination') }}
        </div>
    </div>

    <script>
        $('#customer_id').select2({
            placeholder: "ابحث عن إسم العميل...",
            allowClear: true
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.getElementById('tableContainer');

            // Check if table needs scrolling
            function checkScroll() {
                if (tableContainer.scrollWidth > tableContainer.clientWidth) {
                    tableContainer.classList.add('has-scroll');
                } else {
                    tableContainer.classList.remove('has-scroll');
                }
            }

            // Check on load and resize
            checkScroll();
            window.addEventListener('resize', checkScroll);

            // Remove scroll hint after first interaction
            const scrollHint = document.querySelector('.scroll-hint');
            if (scrollHint) {
                tableContainer.addEventListener('scroll', function() {
                    scrollHint.style.display = 'none';
                }, {
                    once: true
                });
            }
        });
    </script>

    <style>
        .select2-container .select2-selection {
            height: 38px;
            border-radius: 8px;
            border: 1px solid #0d6efd;
            padding: 5px;
        }

        .select2-container .select2-selection__rendered {
            line-height: 30px;
        }
    </style>

@endsection
