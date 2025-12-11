<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="ميزان مراجعة">
    <div class="col-md-2">
        <label class="form-label">الحركات المدينة</label>
        <select name="debit_movements" class="form-control border-primary">
            <option value="1" {{ request('debit_movements') == '1' ? 'selected' : '' }}>عرض</option>
            <option value="0" {{ request('debit_movements') == '0' ? 'selected' : '' }}>إخفاء</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">الحركات الدائنة</label>
        <select name="credit_movements" class="form-control border-primary">
            <option value="1" {{ request('credit_movements') == '1' ? 'selected' : '' }}>عرض</option>
            <option value="0" {{ request('credit_movements') == '0' ? 'selected' : '' }}>إخفاء</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">الحسابات الصفرية</label>
        <select name="zero_accounts" class="form-control border-primary">
            <option value="1" {{ request('zero_accounts') == '1' ? 'selected' : '' }}>عرض</option>
            <option value="0" {{ request('zero_accounts') == '0' ? 'selected' : '' }}>إخفاء</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" name="to" class="form-control border-primary" value="{{ request('to', now()->endOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary fw-bold w-100" onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
            عرض التقرير
            <i class="fa-solid fa-eye ms-1"></i>
        </button>
    </div>
</form>

<div id="report" class="bg-white p-3 rounded-3 shadow-sm border-0 mb-5">
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
                    @if('0' === request()->query('debit_movements') && $account->calculateBalance(request()->query('from'), request()->query('to'))->balance['debit'] > 0)
                        @continue
                    @endif
                    @if('0' === request()->query('credit_movements') && $account->calculateBalance(request()->query('from'), request()->query('to'))->balance['credit'] > 0)
                        @continue
                    @endif
                    @if('0' === request()->query('zero_accounts') && $account->calculateBalance(null, request()->query('to'))->balance['debit'] == 0 && $account->calculateBalance(null, request()->query('to'))->balance['credit'] == 0)
                        @continue
                    @endif
                    <tr class="table-primary">
                        @php
                            $from = request()->query('from', now()->startOfYear()->format('Y-m-d'));
                            $to = request()->query('to', now()->endOfYear()->format('Y-m-d'));
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
