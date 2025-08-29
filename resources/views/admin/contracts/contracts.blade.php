@extends('layouts.admin')

@section('title', 'العقود')

@section('content')
<style>
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-running {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-completed {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-canceled {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

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
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($contracts->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-canceled fs-6">لم يتم العثور على اي إتفاقيات!</div>
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
                        <td class="text-center">{{ $contract->start_date }}</td>
                        <td class="text-center">{{ $contract->end_date }}</td>
                        <td class="action-icons text-center">
                            <a href="{{ route('contracts.details', $contract->id) }}" 
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