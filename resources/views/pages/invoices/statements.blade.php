@extends('layouts.app')

@section('title', 'المطالبات')

@section('content')
<h1 class="mb-4">المطالبات</h1>

<div class="row mb-4">
    <div class="col-8">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحــث عن مطالبة:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن مطالبة بالرقم او بإسم العميل او بتاريخ المطالبة... "
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
            <label class="form-label text-dark fw-bold">تصفية حسب طريقــة الدفــع:</label>
            <div class="d-flex">
                <select name="paymentMethod" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('paymentMethod') === 'all' || !request()->query('paymentMethod') ? 'selected' : '' }}>
                        جميع الطرق</option>
                    <option value="آجل" {{ request()->query('paymentMethod') === 'آجل' ? 'selected' : '' }}>
                        آجل</option>
                    <option value="تحويل بنكي" {{ request()->query('paymentMethod') === 'تحويل بنكي' ? 'selected' : '' }}>
                        تحويل بنكي</option>
                    <option value="كاش" {{ request()->query('paymentMethod') === 'كاش' ? 'selected' : '' }}>
                        كاش</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم المطالبة</th>
                <th class="text-center bg-dark text-white">العميــل</th>
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">طريقــة الدفـــع</th>
                <th class="text-center bg-dark text-white">تاريــخ المطالبة</th>
                <th class="text-center bg-dark text-white">تم بواسطة</th>
                <th class="text-center bg-dark text-white">الإجرائات</th>
            </tr>
        </thead>
        <tbody>
            @if ($invoiceStatements->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي مطالبات!</div>
                    </td>
                </tr>
            @else
                @foreach ($invoiceStatements as $statement)
                    <tr>
                        <td class="text-center text-primary fw-bold">
                            <a href="{{ route('invoices.statements.details', $statement) }}" class="text-decoration-none">
                                {{ $statement->code }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $statement->customer) }}"
                                class="text-dark fw-bold text-decoration-none">
                                {{ $statement->customer->name }}
                            </a>
                        </td>
                        <td class="text-center fw-bold">{{ $statement->amount }}</td>
                        <td class="text-center">{{ $statement->payment_method }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($statement->date)->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.profile', $statement->made_by) }}" class="text-dark text-decoration-none">
                                {{ $statement->made_by->name ?? '-' }}
                            </a>
                        </td>
                        <td class="d-flex justify-content-center align-items-center gap-2 text-center">
                            <a href="{{ route('invoices.statements.details', $statement) }}" class="btn btn-sm btn-primary">
                                عرض
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection