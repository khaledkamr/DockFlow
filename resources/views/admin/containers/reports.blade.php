@extends('layouts.admin')

@section('title', 'تقارير الحاويات')

@section('content')
<h1 class="mb-4">تقارير الحاويات</h1>

<div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
    <form method="GET" class="row g-3" id="reportForm">
        <div class="col">
            <label class="form-label">من تاريخ</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control border-primary">
        </div>
        <div class="col">
            <label class="form-label">إلى تاريخ</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control border-primary">
        </div>
        <div class="col">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select border-primary">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>الكل</option>
                <option value="متوفر" {{ request('status') == 'متوفر' ? 'selected' : '' }}>متوفر</option>
                <option value="مُسلم" {{ request('status') == 'مُسلم' ? 'selected' : '' }}>مُسلم</option>
                <option value="متأخر" {{ request('status') == 'متأخر' ? 'selected' : '' }}>متأخر</option>
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
            <select name="customer" class="form-select border-primary">
                <option value="all" {{ request('customer') == 'all' ? 'selected' : '' }}>الكل</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 text-start">
            <button id="submitBtn" class="btn btn-primary fw-bold px-4">
                <span id="btnIcon"><i class="fa-solid fa-file-circle-check"></i></span>
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
            <h3 class="text-primary fw-bold mb-0">{{ $containers->where('status', 'متوفر')->count() }}</h3>
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
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
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

            <button class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button>

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
                    <th class="text-center bg-dark text-white">تم الإستلام بواسطة</th>
                    <th class="text-center bg-dark text-white">تم التسليم بواسطة</th>
                    <th class="text-center bg-dark text-white">تاريخ الدخول</th>
                    <th class="text-center bg-dark text-white">تاريخ الخروج</th>
                </tr>
            </thead>
            <tbody>
                @if ($containers->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي حاويات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($containers as $index => $container)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center text-primary fw-bold">{{ $container->code }}</td>
                            <td class="text-center">{{ $container->customer->name }}</td>
                            <td class="text-center">{{ $container->containerType->name }}</td>
                            <td class="text-center">{{ $container->location ?? '-' }}</td>
                            <td class="text-center">
                                @if($container->status == 'متوفر')
                                    <div class="status-available">{{ $container->status }}</div>
                                @elseif($container->status == 'مُسلم')
                                    <div class="status-delivered">
                                        {{ $container->status }}
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @elseif($container->status == 'متأخر')
                                    <div class="status-danger">{{ $container->status }}</div>
                                @elseif($container->status == 'في الإنتظار')
                                    <div class="status-waiting">{{ $container->status }}</div>
                                @endif
                            </td>
                            <td class="text-center {{ $container->received_by ? 'text-dark' : 'text-muted' }}">
                                {{ $container->received_by ?? 'لم يتم الإستلام بعد' }}
                            </td>
                            <td class="text-center {{ $container->delivered_by ? 'text-dark' : 'text-muted' }}">
                                {{ $container->delivered_by ?? 'لم يتم التسليم بعد' }}
                            </td>
                            <td class="text-center">{{ $container->date ?? '-' }}</td>
                            <td class="text-center">{{ $container->exit_date ?? '-' }}</td>
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



@endsection