@extends('layouts.app')

@section('title', 'إشعارات الفاتورة')

@section('content')
    <h1 class="mb-4">إشعـــارات الفاتـــورة</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحــث عن إشعــار:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن إشعار برقمه أو برقم الفاتورة... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-6 col-md-2">
            <form method="GET" action="" class="d-flex flex-column">
                <label class="form-label text-dark fw-bold d-none d-md-inline">تصفية حسب النوع:</label>
                <label class="form-label text-dark fw-bold d-inline d-md-none">النوع:</label>
                <div class="d-flex">
                    <select name="type" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">جميع الإشعارات</option>
                        <option value="credit" {{ request()->query('type') === 'credit' ? 'selected' : '' }}>
                            إشعارات دائنة
                        </option>
                        <option value="debit" {{ request()->query('type') === 'debit' ? 'selected' : '' }}>
                            إشعارات مدينة
                        </option>
                    </select>
                    @foreach (request()->except('type') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </div>
            </form>
        </div>
        <div class="col-6 col-md-2">
            <form method="GET" action="" class="d-flex flex-column">
                <label class="form-label text-dark fw-bold d-none d-md-inline">تصفية حسب الحالة:</label>
                <label class="form-label text-dark fw-bold d-inline d-md-none">الحالة:</label>
                <div class="d-flex">
                    <select name="status" class="form-select border-primary" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="posted" {{ request()->query('status') === 'posted' ? 'selected' : '' }}>
                            منشورة
                        </option>
                        <option value="draft" {{ request()->query('status') === 'draft' ? 'selected' : '' }}>
                            مسودة
                        </option>
                    </select>
                    @foreach (request()->except('status') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </div>
            </form>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <a href="{{ route('invoices.notes.create') }}" class="btn btn-primary fw-bold w-100">
                <span class="d-none d-sm-inline"><i class="fa-solid fa-plus d-sm-inline d-none "></i> إشعار جديد</span>
                <i class="fa-solid fa-plus d-inline d-sm-none"></i>
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الإشعار</th>
                    <th class="text-center bg-dark text-white text-nowrap">النوع</th>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الفاتورة</th>
                    <th class="text-center bg-dark text-white text-nowrap">المبلغ</th>
                    <th class="text-center bg-dark text-white text-nowrap">الضريبة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجمالي</th>
                    <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                    <th class="text-center bg-dark text-white text-nowrap">أنشئ بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">حالة الارسال</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($notes->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="status-danger fs-6">لا توجد أي إشعـــارات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($notes as $note)
                        @php
                            $isCredit = $note->type === 'credit';
                            $typeLabel = $isCredit ? 'إشعار دائن' : 'إشعار مدين';
                            $badgeClass = $isCredit ? 'status-danger' : 'status-delivered';
                        @endphp
                        <tr>
                            <td class="text-center text-primary fw-bold">
                                <a href="{{ route('invoices.notes.details', $note) }}"
                                    class="text-decoration-none">
                                    {{ $note->code }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('invoices.unified.details', $note->invoice) }}"
                                    class="text-decoration-none text-primary fw-bold">
                                    {{ $note->invoice->code }}
                                </a>
                            </td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($note->amount, 2) }} <i data-lucide="saudi-riyal"></i>
                            </td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($note->tax, 2) }} <i data-lucide="saudi-riyal"></i>
                            </td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($note->total, 2) }} <i data-lucide="saudi-riyal"></i>
                            </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($note->date)->format('Y/m/d') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $note->made_by) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $note->made_by->name ?? '-' }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if ($note->zatca_status === 'sent with out errors')
                                    <span class="badge status-delivered">تم الارسال</span>
                                @else
                                    <span class="badge status-danger">لم يتم الارسال</span>
                                @endif
                            </td>
                            <td class="d-flex justify-content-center align-items-center gap-2 text-center">
                                <a href="" class="btn btn-sm btn-outline-primary">
                                    <span class="d-none d-sm-inline">ارسال</span>
                                    <i class="fa-solid fa-paper-plane d-inline d-sm-none"></i>
                                </a>

                                <a href="{{ route('invoices.notes.details', $note) }}" class="btn btn-sm btn-primary">
                                    <span class="d-none d-sm-inline">عرض</span>
                                    <i class="fa-solid fa-edit d-inline d-sm-none"></i>
                                </a>

                                @if (auth()->user()->roles()->pluck('name')->contains('Admin'))
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $note->id }}">
                                        <span class="d-none d-sm-inline">حذف</span><i
                                            class="fa-solid fa-trash d-inline d-sm-none"></i>
                                    </button>

                                    <div class="modal fade" id="deleteModal{{ $note->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $note->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title fw-bold"
                                                        id="deleteModalLabel{{ $note->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body fs-6">
                                                    هل أنت متأكد من حذف الإشعار <strong>{{ $note->code }}</strong>؟
                                                    @if ($note->is_posted)
                                                        <div class="alert alert-danger mt-3">
                                                            <i class="fas fa-exclamation-circle me-2"></i>
                                                            <strong>تنبيه:</strong> هذا الإشعار تم نشره بالفعل. يجب الحذر
                                                            عند حذفه.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                        class="btn btn-secondary fw-bold"data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('invoices.notes.delete', $note) }}"
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
        {{ $notes->links('components.pagination') }}
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
