@extends('layouts.app')

@section('title', 'بوالص التخزين')

@section('content')
    <h1 class="mb-4">بوالص التخزين و التسليم</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن بوليصة:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن بوليصة بإسم العميل او بتاريخ البوليصة... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 col-sm-4 col-lg-3">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="typeFilter" class="form-label text-dark fw-bold">تصفية حسب النوع:</label>
                <div class="d-flex">
                    <select id="typeFilter" name="type" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('type') === 'all' || !request()->query('type') ? 'selected' : '' }}>
                            جميع البوالص</option>
                        <option value="تخزين" {{ request()->query('type') === 'تخزين' ? 'selected' : '' }}>
                            بوليصة تخزين</option>
                        <option value="تسليم" {{ request()->query('type') === 'تسليم' ? 'selected' : '' }}>
                            بوليصة تسليم</option>
                        <option value="خدمات" {{ request()->query('type') === 'خدمات' ? 'selected' : '' }}>
                            بوليصة خدمات</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-6 col-sm-4 col-md-6 col-lg-2 d-flex align-items-end">
            <a href="{{ route('policies.storage.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-file-circle-plus pe-1"></i>
                <span class="d-none d-md-inline">بوليصة تخزين</span><span class="d-inline d-md-none">تخزين</span>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-6 col-lg-2 d-flex align-items-end">
            <a href="{{ route('policies.receive.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-file-circle-plus pe-1"></i>
                <span class="d-none d-md-inline">بوليصة تسليم</span><span class="d-inline d-md-none">تسليم</span>
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
                    <th class="text-center bg-dark text-white text-nowrap">عدد الحاويات</th>
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
                            <td class="text-center text-primary fw-bold">{{ $policy->code }}</td>
                            <td class="text-center">
                                @if ($policy->external_customer)
                                    <span class="text-dark fw-bold">{{ $policy->external_customer }}</span>
                                @else
                                    <a href="{{ route('users.customer.profile', $policy->customer) }}"
                                        class="text-dark text-decoration-none fw-bold">
                                        {{ $policy->customer->name }}
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($policy->type == 'تخزين')
                                    <span class="badge status-available">{{ $policy->type }}</span>
                                @elseif($policy->type == 'تسليم')
                                    <span class="badge status-delivered">{{ $policy->type }}</span>
                                @elseif($policy->type == 'خدمات')
                                    <span class="badge status-waiting">{{ $policy->type }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                            <td class="text-center">{{ $policy->containers ? $policy->containers->count() : 0 }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $policy->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $policy->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="action-icons text-center">
                                @if ($policy->type == 'تخزين')
                                    <a href="{{ route('policies.storage.details', $policy) }}"
                                        class="btn btn-sm btn-primary">
                                        عرض
                                    </a>
                                @elseif($policy->type == 'تسليم')
                                    <a href="{{ route('policies.receive.details', $policy) }}"
                                        class="btn btn-sm btn-primary">
                                        عرض
                                    </a>
                                @elseif($policy->type == 'خدمات')
                                    <a href="{{ route('policies.services.details', $policy) }}"
                                        class="btn btn-sm btn-primary">
                                        عرض
                                    </a>
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
        {{ $policies->appends(request()->query())->onEachSide(1)->links() }}
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
