@extends('layouts.app')

@section('title', 'بوالص الشحن')

@section('content')
    <h1 class="mb-4">بوالص الشحن</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="search" class="form-label text-dark fw-bold">بحث عن بوليصة:</label>
                <div class="d-flex flex-grow-1">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن بوليصة بالرقم او بإسم العميل او بتاريخ البوليصة... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="col-12 col-sm-4 col-lg-2">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="typeFilter" class="form-label text-dark fw-bold">تصفية حسب النوع:</label>
                <div class="d-flex flex-grow-1">
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

        <div class="col-12 col-sm-4 col-lg-2">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
                <div class="d-flex flex-grow-1">
                    <select id="statusFilter" name="is_received" class="form-select border-primary"
                        onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('is_received') === 'all' || !request()->query('is_received') ? 'selected' : '' }}>
                            جميع البوالص</option>
                        <option value="تم التسليم" {{ request()->query('is_received') === 'تم التسليم' ? 'selected' : '' }}>
                            تم التسليم
                        </option>
                        <option value="في الانتظار"
                            {{ request()->query('is_received') === 'في الانتظار' ? 'selected' : '' }}>
                            في الانتظار
                        </option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>

        <div class="col-12 col-sm-4 col-lg-2 d-flex align-items-end">
            <a href="{{ route('shipping.policies.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-file-circle-plus pe-1"></i>
                <span class="d-none d-sm-inline">إنشاء بوليصة</span>
                <span class="d-inline d-sm-none">إضافة</span>
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم البوليصة</th>
                    <th class="text-center bg-dark text-white text-nowrap">إسم العميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">نوع البوليصة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ البوليصة</th>
                    <th class="text-center bg-dark text-white text-nowrap">مكان التحميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">مكان التسليم</th>
                    <th class="text-center bg-dark text-white text-nowrap">الحالة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
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
                            <td class="text-center text-primary fw-bold text-nowrap">{{ $policy->code }}</td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('users.customer.profile', $policy->customer) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $policy->customer->name }}
                                </a>
                            </td>
                            <td class="text-center text-nowrap">
                                <span
                                    class="badge {{ $policy->type === 'ناقل داخلي' ? 'status-available' : 'status-danger' }}">
                                    {{ $policy->type }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}
                            </td>
                            <td class="text-center text-nowrap"><i class="fas fa-map-marker-alt text-danger"></i>
                                {{ $policy->from }}</td>
                            <td class="text-center text-nowrap"><i class="fas fa-map-marker-alt text-danger"></i>
                                {{ $policy->to }}</td>
                            <td class="text-center text-nowrap">
                                @if ($policy->is_received)
                                    <span class="badge status-delivered">تم التسليم</span>
                                @else
                                    <span class="badge status-waiting">في الانتظار</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('admin.user.profile', $policy->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $policy->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="action-icons text-center text-nowrap">
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

    <div class="scroll-hint">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <script>
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

@endsection
