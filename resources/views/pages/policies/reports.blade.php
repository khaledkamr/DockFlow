@extends('layouts.app')

@section('title', 'تقارير بوالص التخزين')

@section('content')
    <h1 class="mb-4">تقارير بوالص التخزين</h1>

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form method="GET" class="row g-3" id="reportForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 100) }}">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">العميل</label>
                <select name="customer" id="customer_id" class="form-select border-primary">
                    <option value="all" {{ request('customer') == 'all' ? 'selected' : '' }}>الكل</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-6 col-lg">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="from"
                    value="{{ request('from', Carbon\Carbon::now()->startOfYear()->format('Y-m-d')) }}"
                    class="form-control border-primary">
            </div>
            <div class="col-6 col-md-6 col-lg">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="to" value="{{ request('to', Carbon\Carbon::now()->format('Y-m-d')) }}"
                    class="form-control border-primary">
            </div>
            <div class="col-6 col-md-6 col-lg">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select border-primary">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg">
                <label class="form-label">مفوترة</label>
                <select name="invoiced" id="invoiced" class="form-select border-primary">
                    <option value="all" {{ request('invoiced') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="invoiced" {{ request('invoiced') == 'invoiced' ? 'selected' : '' }}>
                        مع فاتورة
                    </option>
                    <option value="not_invoiced" {{ request('invoiced') == 'not_invoiced' ? 'selected' : '' }}>
                        بدون فاتورة
                    </option>
                </select>
            </div>
            <div class="col-12 text-start">
                <button id="submitBtn" class="btn btn-primary fw-bold px-4"
                    onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                    <span class="d-inline">عرض التقرير</span>
                    <i class="fa-solid fa-file-circle-check"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
            <div class="">
                <form method="GET" action="">
                    <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="form-select form-select-sm d-inline-block w-auto">
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                        <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ $perPage == 1000 ? 'selected' : '' }}>1000</option>
                    </select>
                    @foreach (request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('export.excel', 'policies_report') }}">
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="تصدير Excel">
                        <i class="fa-solid fa-file-excel"></i>
                    </button>
                </form>

                <form action="{{ route('print.policies.report') }}" method="GET" target="_blank">
                    @csrf
                    @foreach(request()->except('per_page') as $key => $value)
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
                        <th class="text-center bg-dark text-white text-nowrap">#</th>
                        <th class="text-center bg-dark text-white text-nowrap">رقم البوليصة</th>
                        <th class="text-center bg-dark text-white text-nowrap">النوع</th>
                        <th class="text-center bg-dark text-white text-nowrap">بوليصة التسليم</th>
                        <th class="text-center bg-dark text-white text-nowrap">العميل</th>
                        <th class="text-center bg-dark text-white text-nowrap">الحاوية</th>
                        <th class="text-center bg-dark text-white text-nowrap">تاريخ الدخول</th>
                        <th class="text-center bg-dark text-white text-nowrap">تاريخ الخروج</th>
                        <th class="text-center bg-dark text-white text-nowrap">أيام التخزين</th>
                        <th class="text-center bg-dark text-white text-nowrap">الفاتورة</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($policies->isEmpty() || !request()->hasAny(['customer', 'from', 'to', 'type']))
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="status-danger fs-6">لم يتم العثور على اي بوالص!</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($policies as $policy)
                            <tr>
                                <td class="text-center text-nowrap">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold text-nowrap">
                                    @if ($policy->type == 'تخزين')
                                        <a href="{{ route('policies.storage.details', $policy) }}"
                                            class="text-decoration-none">
                                            {{ $policy->code }}
                                        </a>
                                    @elseif($policy->type == 'خدمات')
                                        <a href="{{ route('policies.services.details', $policy) }}"
                                            class="text-decoration-none">
                                            {{ $policy->code }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($policy->type == 'تخزين')
                                        <span class="badge status-available">{{ $policy->type }}</span>
                                    @elseif($policy->type == 'خدمات')
                                        <span class="badge status-waiting">{{ $policy->type }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first())
                                        <a href="{{ route('policies.receive.details', $policy->containers->first()->policies->where('type', 'تسليم')->first()) }}" class="text-decoration-none fw-bold">
                                            {{ $policy->containers->first()->policies->where('type', 'تسليم')->first()->code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $policy->customer) }}" class="text-decoration-none text-dark">
                                        {{ $policy->customer->name }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    @if($policy->containers->first())
                                        <a href="{{ route('container.details', $policy->containers->first()) }}" class="text-decoration-none text-dark">
                                            {{ $policy->containers->first()->code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($policy->containers->first())
                                        {{ Carbon\Carbon::parse($policy->containers->first()->date)->format('Y/m/d') }}
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($policy->containers->first() && $policy->containers->first()->exit_date)
                                        {{ Carbon\Carbon::parse($policy->containers->first()->exit_date)->format('Y/m/d') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($policy->type == 'تخزين')
                                        {{ $policy->containers->first() ? $policy->containers->first()->storage_days : '-' }}
                                    @elseif($policy->type == 'خدمات')
                                        0
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($policy->containers->first())
                                        @if ($policy->containers->first()->invoices->where('type', 'تخزين')->first())
                                            <a href="{{ route('invoices.details', $policy->containers->first()->invoices->where('type', 'تخزين')->first()) }}" target="_blank" class="text-decoration-none fw-bold">
                                                {{ $policy->containers->first()->invoices->where('type', 'تخزين')->first()->code }}
                                            </a>
                                        @elseif($policy->containers->first()->invoices->where('type', 'تخليص')->first())
                                            <a href="{{ route('invoices.clearance.details', $policy->containers->first()->invoices->where('type', 'تخليص')->first()) }}" target="_blank" class="text-decoration-none fw-bold">
                                                {{ $policy->containers->first()->invoices->where('type', 'تخليص')->first()->code }}
                                            </a>
                                        @elseif($policy->containers->first()->invoices->where('type', 'خدمات')->first())
                                            <a href="{{ route('invoices.services.details', $policy->containers->first()->invoices->where('type', 'خدمات')->first()) }}" target="_blank" class="text-decoration-none fw-bold">
                                                {{ $policy->containers->first()->invoices->where('type', 'خدمات')->first()->code }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    @endif
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
            {{ $policies->links('components.pagination') }}
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
