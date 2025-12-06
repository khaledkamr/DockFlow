@extends('layouts.app')

@section('title', 'تقارير اشعارات النقل')

@section('content')
    <h1 class="mb-4">تقارير اشعارات النقل</h1>

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form method="GET" id="reportForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 100) }}">
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-6 col-lg-3">
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
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from"
                        value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                        class="form-control border-primary">
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">نوع الناقل</label>
                    <select name="type" class="form-select border-primary">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="ناقل داخلي" {{ request('type') == 'ناقل داخلي' ? 'selected' : '' }}>
                            ناقل داخلي
                        </option>
                        <option value="ناقل خارجي" {{ request('type') == 'ناقل خارجي' ? 'selected' : '' }}>
                            ناقل خارجي
                        </option>
                    </select>
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">حالة التسليم</label>
                    <select name="status" class="form-select border-primary">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="تم التسليم" {{ request('status') == 'تم التسليم' ? 'selected' : '' }}>تم التسليم
                        </option>
                        <option value="تحت التسليم" {{ request('status') == 'تحت التسليم' ? 'selected' : '' }}>تحت التسليم
                        </option>
                    </select>
                </div>
                
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-6 col-lg-3">
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
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">السائق</label>
                    <select name="driver" id="driver_id" class="form-select border-primary">
                        <option value="all" {{ request('driver') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ request('driver') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">السيارة</label>
                    <select name="vehicle" id="vehicle_id" class="form-select border-primary">
                        <option value="all" {{ request('vehicle') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">مكان التحميل</label>
                    <select name="loading_location" id="loading_location" class="form-select border-primary">
                        <option value="all" {{ request('loading_location') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($loadingLocations as $location)
                            <option value="{{ $location }}"
                                {{ request('loading_location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-6 col-lg">
                    <label class="form-label">مكان التسليم</label>
                    <select name="delivery_location" id="delivery_location" class="form-select border-primary">
                        <option value="all" {{ request('delivery_location') == 'all' ? 'selected' : '' }}>الكل</option>
                        @foreach ($deliveryLocations as $location)
                            <option value="{{ $location }}"
                                {{ request('delivery_location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-start">
                    <button id="submitBtn" class="btn btn-primary fw-bold px-4"
                        onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                        <span class="d-inline">عرض التقرير</span>
                        <i class="fa-solid fa-file-circle-check ms-1"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div
            class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div class="">
                <form method="GET" action="">
                    <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="form-select form-select-sm d-inline-block w-auto">
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                    </select>
                    @foreach (request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('export.excel', 'transport_orders') }}">
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="تصدير Excel">
                        <i class="fa-solid fa-file-excel"></i>
                    </button>
                </form>
                <form action="{{ route('print.transport.reports') }}" method="GET" target="_blank">
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
                        <th class="text-center bg-dark text-white text-nowrap">رقم الاشعار</th>
                        <th class="text-center bg-dark text-white text-nowrap">رقم المعاملة</th>
                        <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                        <th class="text-center bg-dark text-white text-nowrap">العميل</th>
                        <th class="text-center bg-dark text-white text-nowrap">نوع الناقل</th>
                        <th class="text-center bg-dark text-white text-nowrap">المورد</th>
                        <th class="text-center bg-dark text-white text-nowrap">السائق</th>
                        <th class="text-center bg-dark text-white text-nowrap">السيارة</th>
                        <th class="text-center bg-dark text-white text-nowrap">الحاوية</th>
                        <th class="text-center bg-dark text-white">مكان التحميل</th>
                        <th class="text-center bg-dark text-white">مكان التسليم</th>
                        <th class="text-center bg-dark text-white text-nowrap">الحالة</th>
                        <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($transportOrders->isEmpty() || !request()->hasAny(['customer','from','to','type','status','supplier','driver','vehicle','loading_location','delivery_location',]))
                        <tr>
                            <td colspan="14" class="text-center">
                                <div class="status-danger fs-6">لم يتم العثور على اي بوالص!</div>
                            </td>
                        </tr>
                    @else
                        @php
                            $index = 1;
                        @endphp
                        @foreach ($transportOrders as $transportOrder)
                            <tr>
                                <td class="text-center">{{ $index++ }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('transactions.transportOrders.details', $transportOrder) }}"
                                        class="text-decoration-none" target="_blank">
                                        {{ $transportOrder->code }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('transactions.details', $transportOrder) }}" 
                                        class="text-decoration-none" target="_blank">
                                        {{ $transportOrder->transaction->code }}
                                    </a>
                                </td>
                                <td class="text-center">{{ Carbon\Carbon::parse($transportOrder->date)->format('Y/m/d') }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $transportOrder->customer) }}" class="text-decoration-none text-dark" target="_blank">
                                        {{ $transportOrder->customer->name }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $transportOrder->type }}</td>
                                <td class="text-center">{{ $transportOrder->supplier->name ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $transportOrder->supplier ? $transportOrder->driver_name : $transportOrder->driver->name ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $transportOrder->supplier ? $transportOrder->vehicle_plate : $transportOrder->vehicle->plate_number ?? '-' }}
                                </td>
                                <td class="text-center fw-bold">
                                    @if($transportOrder->containers->isEmpty())
                                        -
                                    @else
                                        <a href="{{ route('container.details', $transportOrder->containers->first()) }}" class="text-decoration-none text-dark" target="_blank">
                                            {{ $transportOrder->containers->first()->code }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">{{ $transportOrder->from }}</td>
                                <td class="text-center">{{ $transportOrder->to }}</td>
                                <td class="text-center">
                                    <div class="badge status-delivered">تم التسليم</div>
                                </td>
                                <td class="text-center">{{ $transportOrder->total_cost }}</td>
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
            {{ $transportOrders->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>

    <script>
        $('#customer_id').select2({
            placeholder: "ابحث عن إسم العميل...",
            allowClear: true
        });
        $('#supplier_id').select2({
            placeholder: "ابحث عن إسم المورد...",
            allowClear: true
        });
        $('#driver_id').select2({
            placeholder: "ابحث عن إسم السائق...",
            allowClear: true
        });
        $('#vehicle_id').select2({
            placeholder: "ابحث عن رقم السيارة...",
            allowClear: true
        });
        $('#loading_location').select2({
            placeholder: "ابحث عن مكان التحميل...",
            allowClear: true
        });
        $('#delivery_location').select2({
            placeholder: "ابحث عن مكان التسليم...",
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
