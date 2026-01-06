<div class="row g-2 mb-3">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-success h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-money-bill-wave fs-4 text-success"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-success fw-bold mb-1">سندات القبض النقدي</h5>
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
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-danger h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-coins fs-4 text-danger"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-danger fw-bold mb-1">سندات الصرف النقدي</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                {{ $vouchers->where('type', 'سند صرف نقدي')->count() ?? 0 }} سند صرف
                            </span>
                        </div>
                    </div>
                    <p class="text-danger fw-bold mb-0 mt-1 fs-6">
                        {{ number_format($vouchers->whereIn('type', ['سند صرف نقدي', 'سند صرف نقدي'])->sum('amount') ?? 0) }}
                        ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-primary h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-piggy-bank fs-4 text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="text-primary fw-bold mb-1">رصيد الصندوق</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                {{ $vouchers->count() ?? 0 }} سند
                            </span>
                        </div>
                    </div>
                    <p class="text-primary fw-bold mb-0 mt-1 fs-6">
                        {{ number_format(end($balanceArray) ?? 0) }} ريال
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم السنــد</th>
                <th class="text-center bg-dark text-white">نوع السنــد</th>
                <th class="text-center bg-dark text-white">الحساب الدائن</th>
                <th class="text-center bg-dark text-white">الحساب المدين</th>
                <th class="text-center bg-dark text-white">التاريــخ</th>
                <th class="text-center bg-dark text-white">مدين</th>
                <th class="text-center bg-dark text-white">دائن</th>
                <th class="text-center bg-dark text-white">الرصيد</th>
            </tr>
        </thead>
        <tbody>
            @if ($vouchers->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي سند قبض او سند صرف!</div>
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
                        <td class="text-center">{{ $voucher->debit_account->name }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($voucher->date)->format('Y/m/d') }}</td>
                        <td class="text-center text-dark fw-bold">
                            {{ $voucher->type == 'سند صرف نقدي' ? (int) $voucher->amount : '0.00' }}</td>
                        <td class="text-center text-dark fw-bold">
                            {{ $voucher->type == 'سند قبض نقدي' ? (int) $voucher->amount : '0.00' }}</td>
                        <td class="text-center text-success fw-bold">{{ $balanceArray[$loop->iteration - 1] }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
