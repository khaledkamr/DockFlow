@extends('layouts.app')

@section('title', 'المعاملات')

@section('content')
    <h1 class="mb-4">معاملات التخليص الجمركي</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-9">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن معاملة:</label>
                <div class="d-flex gap-2">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن معاملة بإسم العميل او بكود المعاملة او بالتاريخ... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 col-lg-3 d-flex align-items-end">
            <a href="{{ route('transactions.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-plus pe-1"></i>
                <span class="d-inline">إضافة معاملة</span>
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم المعاملة</th>
                    <th class="text-center bg-dark text-white text-nowrap">رقم البوليصة</th>
                    <th class="text-center bg-dark text-white text-nowrap">إسم العميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">عدد الحاويات</th>
                    <th class="text-center bg-dark text-white text-nowrap">الحالة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ المعاملة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($transactions->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي معاملات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="text-center text-primary fw-bold">
                                <a href="{{ route('transactions.details', $transaction) }}" class="text-decoration-none">
                                    {{ $transaction->code }}
                                </a>
                            </td>
                            <td class="text-center text-primary fw-bold">{{ $transaction->policy_number ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.customer.profile', $transaction->customer) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $transaction->customer->name }}
                                </a>
                            </td>
                            <td class="text-center fw-bold">{{ $transaction->containers->count() }}</td>
                            <td class="text-center">
                                @if ($transaction->status == 'مغلقة')
                                    <span class="badge status-delivered">مغلقة</span>
                                @else
                                    <span class="badge status-waiting">معلقة</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($transaction->date ?? $transaction->created_at)->format('Y/m/d') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $transaction->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $transaction->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="action-icons text-center">
                                <a href="{{ route('transactions.details', $transaction) }}" class="btn btn-sm btn-primary">
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

    <div class="mb-5"></div>

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
