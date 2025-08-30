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
                        <td class="text-center">{{ $journal->code }}</td>
                        <td class="text-center">قيد يومي</td>
                        <td class="text-center">{{ $journal->amount }}</td>
                        <td class="text-center">{{ $journal->date }}</td>
                        <td class="text-center">{{ $journal->made_by }}</td>
                        <td class="text-center {{ $journal->modified_by ? 'text-dark' : 'text-muted' }}">
                            {{ $journal->modified_by ?? 'لم يتم التعديل' }}
                        </td>
                        <td class="action-icons text-center">
                            <a href="{{ route('admin.journal.details', $journal->id) }}" 
                                class="btn btn-sm btn-primary">
                                عرض
                            </a>
                            <a class="btn btn-sm btn-secondary">
                                طباعة
                            </a>
                            <a class="btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#delete{{ $journal->id }}">
                                تعديل
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="delete{{ $journal->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $journal->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteLabel{{ $journal->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-dark">
                                    هل انت متأكد من حذف القيض؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('admin.delete.voucher', $journal->id) }}" method="POST">
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