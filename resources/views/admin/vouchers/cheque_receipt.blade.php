<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم السنــد</th>
                <th class="text-center bg-dark text-white">إسم الحساب</th>
                <th class="text-center bg-dark text-white">رقم الحساب</th>
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">التاريــخ</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($vouchers->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي سند صرف بشيك!</div>
                    </td>
                </tr>
            @else
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td class="text-center">{{ $voucher->code }}</td>
                        <td class="text-center">{{ $voucher->account->name }}</td>
                        <td class="text-center">{{ $voucher->account->code }}</td>
                        <td class="text-center text-success fw-bold">{{ (int) $voucher->amount }} ريال</td>
                        <td class="text-center">{{ $voucher->date }}</td>
                        <td class="action-icons text-center">
                            <button class="bg-primary text-white text-decoration-none border-0 rounded-2 m-0 pe-2 ps-2 p-1" type="button" data-bs-toggle="modal" data-bs-target="#post{{ $voucher->id }}">
                                ترحيل
                            </button>
                            <button class="bg-danger text-white text-decoration-none border-0 rounded-2 m-0 pe-2 ps-2 p-1" type="button" data-bs-toggle="modal" data-bs-target="#delete{{ $voucher->id }}">
                                حذف
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="post{{ $voucher->id }}" tabindex="-1" aria-labelledby="postLabel{{ $voucher->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="postLabel{{ $voucher->id }}">تأكيد ترحيل</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.create.voucher') }}" method="POST">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <div>هل انت متاكد من ترحيل السند</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-1">ترحيل</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="delete{{ $voucher->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $voucher->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteLabel{{ $voucher->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-dark">
                                    هل انت متأكد من حذف السند؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('admin.delete.voucher', $voucher->id) }}" method="POST">
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