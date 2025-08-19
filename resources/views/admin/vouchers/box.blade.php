<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم السنــد</th>
                <th class="text-center bg-dark text-white">نوع السنــد</th>
                <th class="text-center bg-dark text-white">إسم الحساب</th>
                <th class="text-center bg-dark text-white">رقم الحساب</th>
                <th class="text-center bg-dark text-white">التاريــخ</th>
                <th class="text-center bg-dark text-white">مدين</th>
                <th class="text-center bg-dark text-white">دائن</th>
                <th class="text-center bg-dark text-white">الرصيد</th>
            </tr>
        </thead>
        <tbody>
            @if ($vouchers->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي سند قبض او سند صرف!</div>
                    </td>
                </tr>
            @else
                @foreach ($vouchers as $i => $voucher)
                    <tr>
                        <td class="text-center">{{ $voucher->code }}</td>
                        <td class="text-center">{{ $voucher->type }}</td>
                        <td class="text-center">{{ $voucher->account->name }}</td>
                        <td class="text-center">{{ $voucher->account->code }}</td>
                        <td class="text-center">{{ $voucher->date }}</td>
                        <td class="text-center text-success fw-bold">{{ $voucher->type == 'سند صرف نقدي' ?  (int) $voucher->amount : '0.00' }}</td>
                        <td class="text-center text-success fw-bold">{{ $voucher->type == 'سند قبض نقدي' ?  (int) $voucher->amount : '0.00' }}</td>
                        <td class="text-center text-success fw-bold">{{ $balanceArray[$i] }}</td>
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

                @endforeach
            @endif
        </tbody>
    </table>
</div>