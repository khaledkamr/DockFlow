@extends('layouts.app')

@section('title', 'تقارير الحاويات')

@section('content')
    <h1 class="mb-4">تقارير الفواتير</h1>

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
                    <label class="form-label">الترحيل</label>
                    <select name="is_posted" class="form-select border-primary">
                        <option value="all" {{ request('is_posted') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="true" {{ request('is_posted') == 'true' ? 'selected' : '' }}>تم الترحيل</option>
                        <option value="false" {{ request('is_posted') == 'false' ? 'selected' : '' }}>لم يتم الترحيل
                        </option>
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

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-5">
        <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div>
                <form method="GET" action="">
                    <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="form-select form-select-sm d-inline-block w-auto">
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
                    @foreach(request()->except('per_page') as $key => $value)
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
                    <button type="submit" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="طباعة">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-container" id="tableContainer">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center bg-dark text-white text-nowrap">#</th>
                        <th class="text-center bg-dark text-white text-nowrap">رقم الفاتورة</th>
                        <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                        <th class="text-center bg-dark text-white text-nowrap">العميل</th>
                        <th class="text-center bg-dark text-white text-nowrap">نوع الفاتورة</th>
                        <th class="text-center bg-dark text-white text-nowrap">طريقة الدفع</th>
                        <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                        <th class="text-center bg-dark text-white text-nowrap">الضريبة المضافة</th>
                        <th class="text-center bg-dark text-white text-nowrap">الإجمالي</th>
                        <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($invoices->isEmpty() || !request()->hasAny(['customer', 'from', 'to', 'type', 'payment_method']))
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="status-danger fs-6">لم يتم العثور على اي فواتير!</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold">
                                    @if ($invoice->type == 'تخزين')
                                        <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    @elseif($invoice->type == 'خدمات')
                                        <a href="{{ route('invoices.services.details', $invoice) }}"
                                            class="text-decoration-none">
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
                                <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center text-nowrap">{{ $invoice->customer->name }}</td>
                                <td class="text-center">{{ $invoice->type }}</td>
                                <td class="text-center">{{ $invoice->payment_method }}</td>
                                <td class="text-center">{{ $invoice->amount_before_tax }}</td>
                                <td class="text-center">{{ $invoice->tax }}</td>
                                <td class="text-center">{{ $invoice->total_amount }}</td>
                                <td class="text-center">{{ $invoice->made_by->name }}</td>
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
                }, { once: true });
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
