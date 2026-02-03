<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="أعمار الذمم">

    <div class="col-md-2">
        <label class="form-label">نوع التقرير</label>
        <select name="report_type" id="report_type" class="form-select border-primary"
            onchange="toggleCustomerSelect()">
            <option value="all" {{ request('report_type', 'all') == 'all' ? 'selected' : '' }}>جميع العملاء</option>
            <option value="single" {{ request('report_type') == 'single' ? 'selected' : '' }}>عميل محدد</option>
        </select>
    </div>
    <div class="col-md-4" id="customer_select_container"
        style="{{ request('report_type') == 'single' ? '' : 'display: none;' }}">
        <label class="form-label">اختر العميل</label>
        <select name="customer_id" id="customer_id" class="form-select border-primary">
            <option value="">اختر العميل</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary"
            value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" name="to" class="form-control border-primary"
            value="{{ request('to', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary fw-bold w-100"
            onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
            عرض التقرير
            <i class="fa-solid fa-eye ms-1"></i>
        </button>
    </div>
</form>

<div id="report" class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-end mb-2">
        <div>
            <h5 class="mb-3 fw-bold">تقرير أعمار الذمم - {{ $selectedCustomer ? $selectedCustomer->name : 'جميع العملاء' }}</h5>
        </div>
        <div class="export-buttons d-flex gap-2 align-items-center">
            <form action="{{ route('export.excel', 'aging_report') }}" method="GET">
                <input type="hidden" name="customer_id" value="{{ request()->query('customer_id') }}">
                <input type="hidden" name="report_type" value="{{ request()->query('report_type') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>

            <form action="{{ route('print.aging.report') }}" method="GET" target="_blank">
                @foreach (request()->query() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-outline-primary" target="top" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>

    @if (request('report_type') == 'single' && request('customer_id'))
        <div class="table-container">
            @if ($selectedCustomer)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-dark text-center text-white text-nowrap">اسم العميل</th>
                            <th class="bg-dark text-center text-white text-nowrap">رقم الفاتورة</th>
                            <th class="bg-dark text-center text-white text-nowrap">نوع الفاتورة</th>
                            <th class="bg-dark text-center text-white text-nowrap">تاريخ الفاتورة</th>
                            <th class="bg-dark text-center text-white text-nowrap">موعد السداد</th>
                            <th class="bg-dark text-center text-white text-nowrap">أيام التأخير</th>
                            <th class="bg-dark text-center text-white text-nowrap">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unpaidInvoices as $invoice)
                            <tr>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $selectedCustomer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $selectedCustomer->name }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    @if ($invoice->type == 'خدمات')
                                        <a href="{{ route('invoices.services.details', $invoice) }}"
                                            class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'تخزين')
                                        <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'تخليص')
                                        <a href="{{ route('invoices.clearance.details', $invoice) }}"
                                            class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'شحن')
                                        <a href="{{ route('invoices.shipping.details', $invoice) }}"
                                            class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">{{ $invoice->type }}</td>
                                <td class="text-center text-nowrap">{{ \Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center text-nowrap">
                                    {{ $invoice->payment_due_date ?  $invoice->payment_due_date->format('Y/m/d') : '' }}
                                </td>
                                <td class="text-center fw-bold text-nowrap {{ $invoice->late_days > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ (int) $invoice->late_days }} يوم
                                </td>
                                <td class="text-center text-nowrap">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">لا توجد فواتير غير مسددة</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold table-primary">
                            <td class="text-center text-nowrap" colspan="6">الإجمالي</td>
                            <td class="text-center text-nowrap">
                                {{ number_format($unpaidInvoices->sum('total_amount'), 2) }} ر.س
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="alert alert-warning text-center">لم يتم العثور على العميل المحدد</div>
            @endif
        </div>
    @else
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-dark text-center text-white text-nowrap">اسم العميل</th>
                        <th class="bg-dark text-center text-white text-nowrap">حالي (0 يوم)</th>
                        <th class="bg-dark text-center text-white text-nowrap">1-30 يوم</th>
                        <th class="bg-dark text-center text-white text-nowrap">31-60 يوم</th>
                        <th class="bg-dark text-center text-white text-nowrap">+90 يوم</th>
                        <th class="bg-dark text-center text-white text-nowrap">إجمالي الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @if (request()->query('from') && request()->query('to'))
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $customer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 0, 0), 2) }}
                                    ر.س
                                    ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 0, 0) }})
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 1, 30), 2) }}
                                    ر.س
                                    ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 1, 30) }})
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 31, 60), 2) }}
                                    ر.س
                                    ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 31, 60) }})
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 61, null), 2) }}
                                    ر.س
                                    ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 61, null) }})
                                </td>
                                <td class="text-center fw-bold text-nowrap">
                                    {{ number_format($customer->totalAgingBalance(request()->query('from'), request()->query('to')), 2) }}
                                    ر.س
                                    ({{ $customer->totalAgingBalanceCount(request()->query('from'), request()->query('to')) }})
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center text-muted">

                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-primary">
                        <td class="text-center text-nowrap">الإجمالي</td>
                        <td class="text-center text-nowrap">
                            {{ number_format(
                                $customers->sum(function ($customer) {
                                    return $customer->agingBalance(request()->query('from'), request()->query('to'), 0, 0);
                                }),
                                2,
                            ) }}
                        </td>
                        <td class="text-center text-nowrap">
                            {{ number_format(
                                $customers->sum(function ($customer) {
                                    return $customer->agingBalance(request()->query('from'), request()->query('to'), 1, 30);
                                }),
                                2,
                            ) }}
                        </td>
                        <td class="text-center text-nowrap">
                            {{ number_format(
                                $customers->sum(function ($customer) {
                                    return $customer->agingBalance(request()->query('from'), request()->query('to'), 31, 60);
                                }),
                                2,
                            ) }}
                        </td>
                        <td class="text-center text-nowrap">
                            {{ number_format(
                                $customers->sum(function ($customer) {
                                    return $customer->agingBalance(request()->query('from'), request()->query('to'), 61, null);
                                }),
                                2,
                            ) }}
                        </td>
                        <td class="text-center text-nowrap">
                            {{ number_format(
                                $customers->sum(function ($customer) {
                                    return $customer->totalAgingBalance(request()->query('from'), request()->query('to'));
                                }),
                                2,
                            ) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

<script>
    function toggleCustomerSelect() {
        var reportType = document.getElementById('report_type').value;
        var customerContainer = document.getElementById('customer_select_container');

        if (reportType === 'single') {
            customerContainer.style.display = 'block';
        } else {
            customerContainer.style.display = 'none';
            document.getElementById('customer_id').value = '';
            // Reset select2 if it's initialized
            if ($('#customer_id').hasClass('select2-hidden-accessible')) {
                $('#customer_id').val('').trigger('change');
            }
        }
    }

    $(document).ready(function() {
        $('#customer_id').select2({
            placeholder: "اختر العميل",
            allowClear: true,
            dir: "rtl",
            language: "ar"
        });

        // Initialize visibility on page load
        toggleCustomerSelect();
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
