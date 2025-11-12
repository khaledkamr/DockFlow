@extends('layouts.app')

@section('title', 'تقارير الحاويات')

@section('content')
<h1 class="mb-4">تقارير الحاويات</h1>

<div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
    <form method="GET" class="row g-3" id="reportForm">
        <div class="col">
            <label class="form-label">من تاريخ</label>
            <input type="date" name="from" value="{{ request('from', Carbon\Carbon::now()->startOfYear()->format('Y-m-d')) }}" class="form-control border-primary">
        </div>
        <div class="col">
            <label class="form-label">إلى تاريخ</label>
            <input type="date" name="to" value="{{ request('to', Carbon\Carbon::now()->format('Y-m-d')) }}" class="form-control border-primary">
        </div>
        <div class="col">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select border-primary">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>الكل</option>
                <option value="في الساحة" {{ request('status') == 'في الساحة' ? 'selected' : '' }}>في الساحة</option>
                <option value="تم التسليم" {{ request('status') == 'تم التسليم' ? 'selected' : '' }}>تم التسليم</option>
                <option value="متأخر" {{ request('status') == 'متأخر' ? 'selected' : '' }}>متأخر</option>
                <option value="قيد النقل" {{ request('status') == 'قيد النقل' ? 'selected' : '' }}>قيد النقل</option>
                <option value="في الميناء" {{ request('status') == 'في الميناء' ? 'selected' : '' }}>في الميناء</option>
            </select>
        </div>
        <div class="col">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select border-primary">
                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-label">العميل</label>
            <select name="customer" id="customer_id" class="form-select border-primary">
                <option value="all" {{ request('customer') == 'all' ? 'selected' : '' }}>الكل</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 text-start">
            <button id="submitBtn" class="btn btn-primary fw-bold px-4" onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                <i class="fa-solid fa-file-circle-check"></i>
                عرض التقرير
            </button>
        </div>
    </form>
</div>

<div class="row text-center mb-4">
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <h6>إجمالي الحاويات</h6>
            <h3 class="fw-bold mb-0">{{ $containers->count() }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <h6>في الساحة</h6>
            <h3 class="text-primary fw-bold mb-0">{{ $containers->where('status', 'في الساحة')->count() }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <h6>تم تسليمها</h6>
            <h3 class="text-primary fw-bold mb-0">{{ $containers->where('exit_date', '!=', null)->count() }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <h6>متأخره</h6>
            <h3 class="text-danger fw-bold mb-0">{{ $containers->where('status', 'متأخر')->count() }}</h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="">
            <form method="GET" action="">
                <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                </select>
            </form>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('export.excel', 'containers') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="customer" value="{{ request('customer') }}">
                <input type="hidden" name="from" value="{{ request('from') }}">
                <input type="hidden" name="to" value="{{ request('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>

            {{-- <button class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button> --}}

            <form action="{{ route('print', 'containers') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <input type="hidden" name="status" value="{{ request()->query('status') }}">
                <input type="hidden" name="type" value="{{ request()->query('type') }}">
                <input type="hidden" name="customer" value="{{ request()->query('customer') }}">
                <button type="submit" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">#</th>
                    <th class="text-center bg-dark text-white">كود الحاويــة</th>
                    <th class="text-center bg-dark text-white">صاحــب الحاويــة</th>
                    <th class="text-center bg-dark text-white">الفئـــة</th>
                    <th class="text-center bg-dark text-white">الموقــع</th>
                    <th class="text-center bg-dark text-white">الحالـــة</th>
                    <th class="text-center bg-dark text-white">تاريخ الدخول</th>
                    <th class="text-center bg-dark text-white">تاريخ الخروج</th>
                </tr>
            </thead>
            <tbody>
                @if ($containers->isEmpty() || !request()->hasAny(['customer', 'from', 'to', 'type', 'status']))
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي حاويات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($containers as $index => $container)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold">
                            <a href="{{ route('container.details', $container) }}" class="text-decoration-none">
                                {{ $container->code }}
                            </a>
                        </td>
                            <td class="text-center">{{ $container->customer->name }}</td>
                            <td class="text-center">{{ $container->containerType->name }}</td>
                            <td class="text-center">{{ $container->location ?? '-' }}</td>
                            <td class="text-center">
                                @if($container->status == 'في الساحة')
                                    <div class="status-available">{{ $container->status }}</div>
                                @elseif($container->status == 'تم التسليم')
                                    <div class="status-delivered">
                                        {{ $container->status }}
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @elseif($container->status == 'خدمات')
                                    <div class="status-waiting">{{ $container->status }}</div>
                                @elseif($container->status == 'متأخر')
                                    <div class="status-danger">{{ $container->status }}</div>
                                @elseif($container->status == 'في الميناء')
                                    <div class="status-info">{{ $container->status }}</div>
                                @elseif($container->status == 'قيد النقل')
                                    <div class="status-purple">{{ $container->status }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $container->date ? Carbon\Carbon::parse($container->date)->format('Y/m/d') : '-' }}</td>
                            <td class="text-center">{{ $container->exit_date ? Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $containers->appends(request()->query())->onEachSide(1)->links() }}
    </div>
</div>

<script>
    $('#customer_id').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
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