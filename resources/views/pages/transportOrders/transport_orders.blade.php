@extends('layouts.app')

@section('title', 'إشعارات النقل')

@section('content')
    <h1 class="mb-4">إشعارات النقل</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-9">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن إشعار:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن إشعار بإسم العميل او بكود الإشعار او بالتاريخ... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 col-lg-3 d-flex align-items-end">
            <a href="{{ route('transactions.transportOrders.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-plus pe-1"></i>
                إضافة إشعار نقل
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">كود الإشعار</th>
                    <th class="text-center bg-dark text-white text-nowrap">كود المعاملة</th>
                    <th class="text-center bg-dark text-white text-nowrap">إسم العميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الإشعار</th>
                    <th class="text-center bg-dark text-white text-nowrap">مكان التحميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">مكان التسليم</th>
                    <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($transportOrders->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي إشعارات نقل!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($transportOrders as $order)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $order->code }}</td>
                            <td class="text-center text-primary fw-bold">
                                <a href="{{ route('transactions.details', $order->transaction) }}"
                                    class="text-decoration-none">
                                    {{ $order->transaction->code }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('users.customer.profile', $order->customer) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $order->customer->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($order->date ?? $order->created_at)->format('Y/m/d') }}
                            </td>
                            <td class="text-center fw-bold">
                                <i class="fas fa-map-marker-alt text-danger"></i>{{ $order->from }}
                            </td>
                            <td class="text-center fw-bold">
                                <i class="fas fa-map-marker-alt text-danger"></i>{{ $order->to }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $order->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $order->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="action-icons text-center">
                                <a href="{{ route('transactions.transportOrders.details', $order) }}"
                                    class="btn btn-sm btn-primary">
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
        {{ $transportOrders->links('components.pagination') }}
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
