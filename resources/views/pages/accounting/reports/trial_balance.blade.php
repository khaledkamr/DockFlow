<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="ميزان مراجعة">
    <div class="col-md-4">
        <label class="form-label">الحساب</label>
        <select class="form-select border-primary">
            <option value="">اختر الحساب</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ request('account') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
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
    <div class="d-flex justify-content-end align-items-end mb-3">
        <div class="export-buttons d-flex gap-2 align-items-center">
            <form action="{{ route('export.excel', 'trail_balance') }}" method="GET">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>

            <form action="" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="account" value="{{ request()->query('account') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-primary" target="top" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped-columns">
            <thead>
                <tr>
                    <th colspan="2" class="bg-dark text-center text-white">الحساب</th>
                    <th colspan="2" class="bg-dark text-center text-white">رصيد اول المدة</th>
                    <th colspan="2" class="bg-dark text-center text-white">الحركة</th>
                    <th colspan="2" class="bg-dark text-center text-white">رصيد اخر المدة</th>
                </tr>
                <tr>
                    <th class="bg-dark text-center text-white">الرقم</th>
                    <th class="bg-dark text-center text-white">الاسم</th>
                    <th class="bg-dark text-center text-white">مدين</th>
                    <th class="bg-dark text-center text-white">دائن</th>
                    <th class="bg-dark text-center text-white">مدين</th>
                    <th class="bg-dark text-center text-white">دائن</th>
                    <th class="bg-dark text-center text-white">مدين</th>
                    <th class="bg-dark text-center text-white">دائن</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trialBalance as $account)
                    <tr class="table-primary">
                        @php
                            $from = request()->query('from');
                            $to = request()->query('to');
                        @endphp
                        <td class="text-center">{{ $account->code }}</td>
                        <td class="fw-bold">{{ $account->name }} ({{ $account->level }})</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->debit }}</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->credit }}</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance($from, $to)->debit }}</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance($from, $to)->credit }}</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance($from, $to)->balance['debit'] }}</td>
                        <td class="text-center fw-bold">{{ $account->calculateBalance($from, $to)->balance['credit'] }}</td>
                    </tr>
                    @if($account->children->count())
                        @include('pages.accounting.reports.trial_balance_row', ['children' => $account->children])
                    @endif
                @endforeach
                <tr class="table-secondary">
                    <td colspan="2" class="text-center fw-bold">الإجمالي</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->debit) }}</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->credit) }}</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance($from, $to)->debit) }}</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance($from, $to)->credit) }}</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance($from, $to)->balance['debit']) }}</td>
                    <td class="text-center fw-bold">{{ $trialBalance->sum(fn($account) => $account->calculateBalance($from, $to)->balance['credit']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
