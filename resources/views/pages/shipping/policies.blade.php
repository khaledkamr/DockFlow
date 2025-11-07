@extends('layouts.app')

@section('title', 'بوالص الشحن')

@section('content')
    <h1 class="mb-4">بوالص الشحن</h1>

    <div class="row mb-4">
        <div class="col-6">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن بوليصة:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن بوليصة بالرقم او بإسم العميل او بتاريخ البوليصة... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="col-2">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="typeFilter" class="form-label text-dark fw-bold">تصفية حسب النوع:</label>
                <div class="d-flex">
                    <select id="typeFilter" name="type" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('type') === 'all' || !request()->query('type') ? 'selected' : '' }}>
                            جميع البوالص</option>
                        <option value="ناقل داخلي" {{ request()->query('type') === 'ناقل داخلي' ? 'selected' : '' }}>
                            ناقل داخلي
                        </option>
                        <option value="ناقل خارجي" {{ request()->query('type') === 'ناقل خارجي' ? 'selected' : '' }}>
                            ناقل خارجي
                        </option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>

        <div class="col-2">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="is_received" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('is_received') === 'all' || !request()->query('is_received') ? 'selected' : '' }}>
                            جميع البوالص</option>
                        <option value="تم التسليم" {{ request()->query('is_received') === 'تم التسليم' ? 'selected' : '' }}>
                            تم التسليم
                        </option>
                        <option value="في الانتظار" {{ request()->query('is_received') === 'في الانتظار' ? 'selected' : '' }}>
                            في الانتظار
                        </option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>

        <div class="col-2 d-flex align-items-end justify-content-end">
            <a href="{{ route('shipping.policies.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-file-circle-plus pe-1"></i>
                إنشاء بوليصة
            </a>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">رقم البوليصة</th>
                    <th class="text-center bg-dark text-white">إسم العميل</th>
                    <th class="text-center bg-dark text-white">نوع البوليصة</th>
                    <th class="text-center bg-dark text-white">تاريخ البوليصة</th>
                    <th class="text-center bg-dark text-white">عدد البضائع</th>
                    <th class="text-center bg-dark text-white">الحالة</th>
                    <th class="text-center bg-dark text-white">تم بواسطة</th>
                    <th class="text-center bg-dark text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($policies->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي بوالص!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($policies as $policy)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $policy->code }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.customer.profile', $policy->customer) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $policy->customer->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $policy->type }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                            <td class="text-center">{{ $policy->goods ? $policy->goods->count() : 0 }}</td>
                            <td class="text-center">
                                @if($policy->is_received)
                                    <span class="badge status-delivered">تم التسليم</span>
                                @else
                                    <span class="badge status-waiting">في الانتظار</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $policy->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $policy->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="action-icons text-center">
                                <a href="{{ route('shipping.policies.details', $policy) }}" class="btn btn-sm btn-primary">
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
