@extends('layouts.app')

@section('title', 'تقارير فواتير المصاريف')

@section('content')
    <h1 class="mb-4">تقارير فواتير المصاريف</h1>

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form method="GET" id="reportForm">
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <label class="form-label">المورد</label>
                    <select name="supplier" id="supplier_id" class="form-select border-primary">
                        <option value="all" {{ request('supplier') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-4 col-lg">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from"
                        value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-sm-6 col-md-4 col-lg">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-sm-6 col-md-4 col-lg">
                    <label class="form-label">طريقة الدفع</label>
                    <select name="payment_method" class="form-select border-primary">
                        <option value="all" {{ request('payment_method') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="آجل" {{ request('payment_method') == 'آجل' ? 'selected' : '' }}>آجل</option>
                        <option value="كاش" {{ request('payment_method') == 'كاش' ? 'selected' : '' }}>كاش</option>
                        <option value="تحويل بنكي" {{ request('payment_method') == 'تحويل بنكي' ? 'selected' : '' }}>تحويل
                            بنكي</option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-4 col-lg">
                    <label class="form-label">الترحيل</label>
                    <select name="is_posted" class="form-select border-primary">
                        <option value="all" {{ request('is_posted') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="1" {{ request('is_posted') == '1' ? 'selected' : '' }}>تم الترحيل</option>
                        <option value="0" {{ request('is_posted') == '0' ? 'selected' : '' }}>لم يتم الترحيل
                        </option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <label class="form-label">مركز التكلفة</label>
                    <select name="cost_center" id="cost_center_id" class="form-select border-primary">
                        <option value="all" {{ request('cost_center') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($costCenters as $costCenter)
                            <option value="{{ $costCenter->id }}"
                                {{ request('cost_center') == $costCenter->id ? 'selected' : '' }}>
                                {{ $costCenter->name }}
                            </option>
                        @endforeach
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
            <div class="">
                <form method="GET" action="">
                    <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="form-select form-select-sm d-inline-block w-auto">
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                    </select>
                </form>
            </div>
            <div class="d-flex gap-2">
                <form action="" method="GET" target="_blank">
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
                        <th class="text-center bg-dark text-white text-nowrap">فاتورة المورد</th>
                        <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                        <th class="text-center bg-dark text-white text-nowrap">المورد</th>
                        <th class="text-center bg-dark text-white text-nowrap">طريقة الدفع</th>
                        <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                        <th class="text-center bg-dark text-white text-nowrap">الضريبة المضافة</th>
                        <th class="text-center bg-dark text-white text-nowrap">الإجمالي</th>
                        <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($invoices->isEmpty() || !request()->hasAny(['supplier', 'from', 'to', 'payment_method']))
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="status-danger fs-6">لم يتم العثور على اي فواتير!</div>
                            </td>
                        </tr>
                    @else
                        @php
                            $index = 1;
                        @endphp
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="text-center">{{ $index++ }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('expense.invoices.details', $invoice) }}"
                                        class="text-decoration-none">
                                        {{ $invoice->code }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $invoice->supplier_invoice_number ?? '---' }}</td>
                                <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center text-nowrap">{{ $invoice->supplier->name }}</td>
                                <td class="text-center">{{ $invoice->payment_method }}</td>
                                <td class="text-center">{{ number_format($invoice->amount_before_tax, 2) }}</td>
                                <td class="text-center">{{ number_format($invoice->tax, 2) }}</td>
                                <td class="text-center">{{ number_format($invoice->total_amount, 2) }}</td>
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
        $('#supplier_id').select2({
            placeholder: "ابحث عن إسم المورد...",
            allowClear: true
        });

        $('#cost_center_id').select2({
            placeholder: "ابحث عن إسم مركز التكلفة...",
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

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .table-container.has-scroll {
                box-shadow: inset -10px 0 10px -10px rgba(0, 0, 0, 0.1);
            }
        }
    </style>

@endsection
