@extends('layouts.app')

@section('title', 'العقود')

@section('content')

    <h2 class="mb-4">العقـــود</h2>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="search" class="form-label text-dark fw-bold">بحث عن عقد:</label>
                <div class="d-flex flex-grow-1">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن عقد بإسم العميل او بتاريخ العقد... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-6 col-sm-7 col-lg-4">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
                <div class="d-flex flex-grow-1">
                    <select id="statusFilter" name="status" class="form-select border-primary"
                        onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                            جميع العقود</option>
                        <option value="جاري" {{ request()->query('status') === 'جاري' ? 'selected' : '' }}>
                            جاري</option>
                        <option value="منتهي" {{ request()->query('status') === 'منتهي' ? 'selected' : '' }}>
                            منتهي</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-6 col-sm-5 col-lg-2 d-flex align-items-end">
            <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-file-circle-plus pe-1"></i>
                <span class="d-inline">أضف عقد</span>
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم العقد</th>
                    <th class="text-center bg-dark text-white text-nowrap">العميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ العقد</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الإنتهاء</th>
                    <th class="text-center bg-dark text-white text-nowrap">الحالة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
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
                            <td class="text-center text-primary fw-bold text-nowrap">{{ $loop->iteration }}</td>
                            <td class="text-center fw-bold text-nowrap">
                                <a href="{{ route('users.customer.profile', $contract->customer) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $contract->customer->name }}
                                </a>
                            </td>
                            <td class="text-center text-nowrap">
                                {{ Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') }}</td>
                            <td class="text-center text-nowrap">
                                {{ Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') }}
                            </td>
                            <td class="text-center">
                                @if ($contract->end_date < \Carbon\Carbon::now())
                                    <span class="badge status-danger ms-2">منتهي</span>
                                @else
                                    <span class="badge status-delivered ms-2">ساري</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">{{ $contract->made_by->name ?? '-' }}</td>
                            <td class="action-icons text-center text-nowrap">
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

    <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <div class="mt-4">
        {{ $contracts->links('components.pagination') }}
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
