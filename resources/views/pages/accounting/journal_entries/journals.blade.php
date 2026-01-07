<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <form method="GET" action="" class="d-flex flex-column h-100">
            <div class="d-flex flex-grow-1">
                <input type="text" name="journal_search" class="form-control border-primary"
                    placeholder=" ابحث عن قيد برقم القيد او التاريخ... " value="{{ request()->query('journal_search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-12 col-md-4 d-none d-sm-block">
        <form method="GET" action="" class="d-flex flex-column">
            <input type="hidden" name="view" value="قيود يومية">
            <select id="statusFilter" name="journal_type" class="form-select border-primary" onchange="this.form.submit()">
                <option value="all" {{ request()->query('journal_type') === 'all' || !request()->query('journal_type') ? 'selected' : '' }}>
                    جميع القيود
                </option>
                <option value="all_journals" {{ request()->query('journal_type') === 'all_journals' ? 'selected' : '' }}>
                    القيود اليومية
                </option>
                <option value="all_receipts" {{ request()->query('journal_type') === 'all_receipts' ? 'selected' : '' }}>
                    جميع سندات القبض
                </option>
                <option value="all_payments" {{ request()->query('journal_type') === 'all_payments' ? 'selected' : '' }}>
                    جميع سندات الصرف
                </option>
            </select>
            @if (request()->query('search'))
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
            @endif
        </form>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('money.create.journal') }}" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-plus me-2"></i>
            <span>إضافة قيد</span>
        </a>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم القيد</th>
                <th class="text-center bg-dark text-white">نوع القيد</th>
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">التاريــخ</th>
                <th class="text-center bg-dark text-white">أعد بواسطة</th>
                <th class="text-center bg-dark text-white">تم التعديل بواسطة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($journals->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي قيود  !</div>
                    </td>
                </tr>
            @else
                @foreach ($journals as $journal)
                    <tr>
                        <td class="text-center text-primary fw-bold">
                            <a href="{{ route('journal.details', $journal) }}" class="text-decoration-none">
                                {{ $journal->code }}
                            </a>
                        </td>
                        <td class="text-center fw-bold">{{ $journal->type }}</td>
                        <td class="text-center fw-bold">{{ $journal->totalDebit }} <i data-lucide="saudi-riyal"></i></td>
                        <td class="text-center">{{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.profile', $journal->made_by) }}" class="text-decoration-none text-dark">
                                {{ $journal->made_by->name ?? '-' }}
                            </a>
                        </td>
                        <td class="text-center {{ $journal->modifier_id ? 'text-dark' : 'text-muted' }}">
                            {{ $journal->modified_by->name ?? 'لم يتم التعديل' }}
                        </td>
                        <td class="action-icons text-center">
                            <a href="{{ route('journal.details', $journal) }}" class="btn btn-sm btn-primary">
                                عرض
                            </a>
                            <a href="{{ route('journal.edit', $journal) }}" class="btn btn-sm btn-success" type="button">
                                تعديل
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="delete{{ $journal->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $journal->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white fw-bold" id="deleteLabel{{ $journal->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-dark">
                                    هل انت متأكد من حذف هذا القيد؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('voucher.delete', $journal->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $journals->links('components.pagination') }}
</div>