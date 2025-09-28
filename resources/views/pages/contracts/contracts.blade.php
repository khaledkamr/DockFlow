@extends('layouts.app')

@section('title', 'العقود')

@section('content')

<h2 class="mb-4">العقـــود</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحث عن عقد:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن عقد بإسم العميل او بتاريخ العقد... "
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span>بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
            <div class="d-flex">
                <select id="statusFilter" name="status" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                        جميع العقود</option>
                    <option value="جاري" {{ request()->query('status') === 'جاري' ? 'selected' : '' }}>
                        جاري</option>
                    <option value="منتهي" {{ request()->query('status') === 'منتهي' ? 'selected' : '' }}>
                        منتهي</option>
                    <option value="ملغي" {{ request()->query('status') === 'ملغي' ? 'selected' : '' }}>
                        ملغي</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100 fw-bold">
            <i class="fa-solid fa-file-circle-plus pe-1"></i>
            أضف عقد
        </a>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم العقد</th>
                <th class="text-center bg-dark text-white">الطرف الأول</th>
                <th class="text-center bg-dark text-white">ممثل الطرف الأول</th>
                <th class="text-center bg-dark text-white">الطرف الثاني</th>
                <th class="text-center bg-dark text-white">ممثل الطرف الثاني</th>
                <th class="text-center bg-dark text-white">تاريخ العقد</th>
                <th class="text-center bg-dark text-white">تاريخ الإنتهاء</th>
                <th class="text-center bg-dark text-white">تم بواسطة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($contracts->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي عقود!</div>
                    </td>
                </tr>
            @else
                @foreach ($contracts as $contract)
                    <tr>
                        <td class="text-center text-primary fw-bold">{{ $contract->id }}</td>
                        <td class="text-center">{{ $contract->company->name }}</td>
                        <td class="text-center">{{ $contract->company_representative }}</td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $contract->customer->id) }}"
                                class="text-dark text-decoration-none">
                                {{ $contract->customer->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $contract->customer_representative }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') }}</td>
                        <td class="text-center">{{ $contract->made_by->name ?? "-" }}</td>
                        <td class="action-icons text-center">
                            <a href="{{ route('contracts.details', $contract) }}" 
                                class="btn btn-sm btn-primary rounded-3 m-0">
                                عرض
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $contracts->appends(request()->query())->onEachSide(1)->links() }}
</div>
@endsection