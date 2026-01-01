@extends('layouts.app')

@section('title', 'فواتير المصاريف')

@section('content')
    <h1 class="mb-4">فواتير المصاريف</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحــث عن فاتـــورة:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن فاتورة بالرقم او بإسم المورد او بتاريخ الفاتورة... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-6 col-md-4">
            <form method="GET" action="" class="d-flex flex-column">
                <label class="form-label text-dark fw-bold d-none d-md-inline">تصفية حسب طريقــة الدفــع:</label>
                <label class="form-label text-dark fw-bold d-inline d-md-none">طريقــة الدفــع:</label>
                <div class="d-flex">
                    <select name="paymentMethod" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('paymentMethod') === 'all' || !request()->query('paymentMethod') ? 'selected' : '' }}>
                            جميع الطرق</option>
                        <option value="آجل" {{ request()->query('paymentMethod') === 'آجل' ? 'selected' : '' }}>
                            آجل</option>
                        <option value="تحويل بنكي"
                            {{ request()->query('paymentMethod') === 'تحويل بنكي' ? 'selected' : '' }}>
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
        <div class="col-6 col-md-3">
            <form method="GET" action="" class="d-flex flex-column">
                <label class="form-label text-dark fw-bold d-none d-md-inline">تصفية حسب الدفــع:</label>
                <label class="form-label text-dark fw-bold d-inline d-md-none">الدفــع:</label>
                <div class="d-flex">
                    <select name="isPaid" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('isPaid') === 'all' || !request()->query('isPaid') ? 'selected' : '' }}>
                            جميع الفواتير</option>
                        <option value="تم الدفع" {{ request()->query('isPaid') === 'تم الدفع' ? 'selected' : '' }}>
                            تم الدفع</option>
                        <option value="لم يتم الدفع" {{ request()->query('isPaid') === 'لم يتم الدفع' ? 'selected' : '' }}>
                            لم يتم الدفع</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الفاتـورة</th>
                    <th class="text-center bg-dark text-white text-nowrap">المــورد</th>
                    <th class="text-center bg-dark text-white text-nowrap">فاتورة المورد</th>
                    <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                    <th class="text-center bg-dark text-white text-nowrap">طريقـة الدفـع</th>
                    <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                    <th class="text-center bg-dark text-white text-nowrap">عملية الدفع</th>
                    <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجرائات</th>
                </tr>
            </thead>
            <tbody>
                @if ($expenseInvoices->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="status-danger fs-6">لا يوجد اي فواتيـــر مصاريف!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($expenseInvoices as $invoice)
                        <tr>
                            <td class="text-center text-primary fw-bold">
                                <a href="{{ route('expense.invoices.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            </td>
                            <td class="text-center">{{ $invoice->supplier->name }}</td>
                            <td class="text-center">{{ $invoice->supplier_invoice_number }}</td>
                            <td class="text-center fw-bold">{{ $invoice->total_amount }} <i data-lucide="saudi-riyal"></i></td>
                            <td class="text-center">{{ $invoice->payment_method }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                            @if ($invoice->is_paid)
                                <td class="text-center"><span class="badge status-delivered">تم الدفع</span></td>
                            @else
                                <td class="text-center"><span class="badge status-danger">لم يتم الدفع</span></td>
                            @endif
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $invoice->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $invoice->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="d-flex justify-content-center align-items-center gap-2 text-center">
                                <a href="{{ route('expense.invoices.details', $invoice) }}" class="btn btn-sm btn-primary">
                                    <span class="d-none d-sm-inline">عرض</span><i
                                        class="fa-solid fa-eye d-inline d-sm-none"></i>
                                </a>

                                @if (auth()->user()->roles()->pluck('name')->contains('Super Admin'))
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $invoice->id }}">
                                        <span class="d-none d-sm-inline">حذف</span><i
                                            class="fa-solid fa-trash d-inline d-sm-none"></i>
                                    </button>

                                    <div class="modal fade" id="deleteModal{{ $invoice->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $invoice->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $invoice->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body fs-6">
                                                    هل أنت متأكد من حذف الفاتورة <strong>{{ $invoice->code }}</strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary fw-bold"
                                                        data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('expense.invoices.delete', $invoice) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        {{ $invoices->links('components.pagination') }}
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
                }, {
                    once: true
                });
            }
        });
    </script>
@endsection
