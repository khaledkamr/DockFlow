<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="كشف حساب">
    <div class="col-md-6">
        <label class="form-label">اسم الحساب</label>
        <select name="account" id="account_id" class="form-select border-primary" required>
            <option value="">اختر الحساب</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ request('account') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }} ({{ $account->code }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" name="to" class="form-control border-primary" value="{{ request('to', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary fw-bold w-100" onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
            عرض التقرير
            <i class="fa-solid fa-eye ms-1"></i>
        </button>
    </div>
</form>

<div id="report" class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-end mb-2">
        <div></div>
        <div class="export-buttons d-flex gap-2 align-items-center">
            <form action="{{ route('export.excel', 'account_statement') }}" method="GET">
                <input type="hidden" name="account" value="{{ request()->query('account') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>
            
            <form action="{{ route('print', 'account_statement') }}" method="POST" target="_blank">
                @csrf
                @foreach(request()->query() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-outline-primary" target="top" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="bg-dark text-center text-white text-nowrap">تاريخ</th>
                    <th class="bg-dark text-center text-white text-nowrap">رقم القيد</th>
                    <th class="bg-dark text-center text-white text-nowrap">نوع القيد</th>
                    <th class="bg-dark text-center text-white text-nowrap">البيان</th>
                    <th class="bg-dark text-center text-white text-nowrap">مدين</th>
                    <th class="bg-dark text-center text-white text-nowrap">دائن</th>
                    <th class="bg-dark text-center text-white text-nowrap">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="fw-bold text-center">الرصيد الافتتاحي</td>
                    <td class="fw-bold text-center">{{ $opening_balance > 0 ? $opening_balance : '0.00' }}</td>
                    <td class="fw-bold text-center">{{ $opening_balance < 0 ? abs($opening_balance) : '0.00' }}</td>
                    <td class="fw-bold text-center">{{ $opening_balance ?? '0.00' }}</td>
                </tr>
                @php
                    $balance = $opening_balance ?? 0.00;
                @endphp
                @if($statement)
                    @foreach($statement as $line)
                    @php
                        $balance += $line->debit - $line->credit;
                    @endphp
                        <tr class="text-center">
                            <td>{{ Carbon\Carbon::parse($line->journal->date)->format('Y/m/d') }}</td>
                            <td class="fw-bold">
                                <a href="{{ route('journal.details', $line->journal) }}" class="text-decoration-none text-dark">
                                    {{ $line->journal->code }}
                                </a>
                            </td>
                            <td>{{ $line->journal->type }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                            <td>{{ number_format($line->credit, 2) }}</td>
                            <td>{{ number_format($balance, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-primary fw-bold">
                        <td colspan="4" class="text-center fs-6">الإجماليـــــات</td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->debit), 2) }}</td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->credit), 2) }}</td>
                        <td class="text-center">{{ number_format($balance, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="status-danger fs-6">لا توجد حركات</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#account_id').select2({
            placeholder: "اختر حساب",
            allowClear: true
        });
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 38px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        padding: 5px;
    }

    .select2-container .select2-selection__rendered {
        line-height: 30px;
    }
</style>
