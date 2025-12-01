<div class="row g-2 mb-3">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-success h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-money-bills fs-4 text-success"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-success fw-bold mb-1">نقدي</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                {{ $vouchers->where('type', 'سند قبض نقدي')->count() ?? 0 }} سند
                            </span>
                        </div>
                    </div>
                    <p class="text-success fw-bold mb-0 mt-1 fs-6">
                        {{ number_format($vouchers->where('type', 'سند قبض نقدي')->sum('amount') ?? 0) }} ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-info h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-money-check fs-4 text-info"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-info fw-bold mb-1">بشيك</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                {{ $vouchers->where('type', 'سند قبض بشيك')->count() ?? 0 }} سند
                            </span>
                        </div>
                    </div>
                    <p class="text-info fw-bold mb-0 mt-1 fs-6">
                        {{ number_format($vouchers->where('type', 'سند قبض بشيك')->sum('amount') ?? 0) }} ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-warning h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-credit-card fs-4 text-warning"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-warning fw-bold mb-1">فيزا</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                {{ $vouchers->where('type', 'سند قبض فيزا')->count() ?? 0 }} سند
                            </span>
                        </div>
                    </div>
                    <p class="text-warning fw-bold mb-0 mt-1 fs-6">
                        {{ number_format($vouchers->where('type', 'سند قبض فيزا')->sum('amount') ?? 0) }} ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-primary h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-building-columns fs-4 text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-primary fw-bold mb-1">تحويل بنكي</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                {{ $vouchers->where('type', 'سند قبض تحويل بنكي')->count() ?? 0 }} سند
                            </span>
                        </div>
                    </div>
                    <p class="text-primary fw-bold mb-0 mt-1 fs-6">
                        {{ number_format($vouchers->where('type', 'سند قبض تحويل بنكي')->sum('amount') ?? 0) }} ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <form method="GET" action="" class="d-flex flex-column h-100">
            <input type="hidden" name="view" value="سندات قبض">
            <div class="d-flex flex-grow-1">
                <input type="text" name="voucher_search" class="form-control border-primary"
                    placeholder=" ابحث عن سند برقم السند او التاريخ... " value="{{ request()->query('voucher_search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-12 col-md-4 d-none d-sm-block">
        <form method="GET" action="" class="d-flex flex-column">
            <input type="hidden" name="view" value="سندات قبض">
            <select id="statusFilter" name="voucher_type" class="form-select border-primary" onchange="this.form.submit()">
                <option value="all" {{ request()->query('voucher_type') === 'all' || !request()->query('voucher_type') ? 'selected' : '' }}>
                    جميع السندات
                </option>
                <option value="سند قبض نقدي" {{ request()->query('voucher_type') === 'سند قبض نقدي' ? 'selected' : '' }}>
                    نقدي
                </option>
                <option value="سند قبض بشيك" {{ request()->query('voucher_type') === 'سند قبض بشيك' ? 'selected' : '' }}>
                    بشيك
                </option>
                <option value="سند قبض فيزا" {{ request()->query('voucher_type') === 'سند قبض فيزا' ? 'selected' : '' }}>
                    فيزا
                </option>
                <option value="سند قبض تحويل بنكي" {{ request()->query('voucher_type') === 'سند قبض تحويل بنكي' ? 'selected' : '' }}>
                    تحويل بنكي
                </option>
            </select>
            @if (request()->query('voucher_search'))
                <input type="hidden" name="voucher_search" value="{{ request()->query('voucher_search') }}">
            @endif
        </form>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('voucher.payment.create') }}" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-plus me-2"></i>
            <span>إضافة سند قبض</span>
        </a>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم السنــد</th>
                <th class="text-center bg-dark text-white">النوع</th>
                <th class="text-center bg-dark text-white">إسم الحساب</th>
                <th class="text-center bg-dark text-white">رقم الحساب</th>
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">التاريــخ</th>
                <th class="text-center bg-dark text-white">أعد بواسطة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($vouchers->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي سند قبض!</div>
                    </td>
                </tr>
            @else
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td class="text-center text-primary fw-bold">
                            <a href="{{ route('voucher.details', $voucher) }}" class="text-decoration-none">
                                {{ $voucher->code }}
                            </a>
                        </td>
                        <td class="text-center">{{ $voucher->type }}</td>
                        <td class="text-center">{{ $voucher->credit_account->name }}</td>
                        <td class="text-center">{{ $voucher->credit_account->code }}</td>
                        <td class="text-center text-success fw-bold">{{ (int) $voucher->amount }} ريال</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($voucher->date)->format('Y/m/d') }}</td>
                        <td class="text-center">{{ $voucher->made_by->name ?? '-' }}</td>
                        <td class="action-icons text-center">
                            @if(!$voucher->is_posted)
                                <a href="{{ route('post.voucher', $voucher) }}" class="btn btn-sm btn-primary">
                                    ترحيل
                                </a>
                            @endif
                            <button class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#delete{{ $voucher->id }}">
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
                                <form action="" method="POST">
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
                                    <form action="{{ route('voucher.delete', $voucher->id) }}" method="POST">
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