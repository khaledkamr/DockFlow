<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="ميزان مراجعة">
    <div class="row">
        <div class="col-md-3">
            <label class="form-label">نوع الميزان</label>
            <select name="type" id="type" class="form-control border-primary">
                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>شامل</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->name }}" data-level="{{ $account->level }}" 
                        {{ (request('type') == $account->name && request('type_level') == $account->level) ? 'selected' : '' }}>
                        {{ $account->name }} ({{ $account->level }})
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="type_level" id="type_level" value="{{ request('type_level', '') }}">
        </div>
        <div class="col">
            <label class="form-label">من تاريخ</label>
            <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}" required>
        </div>
        <div class="col">
            <label class="form-label">إلى تاريخ</label>
            <input type="date" name="to" class="form-control border-primary" value="{{ request('to', now()->endOfYear()->format('Y-m-d')) }}" required>
        </div>
        <div class="col">
            <label class="form-label">الحركات المدينة</label>
            <select name="debit_movements" class="form-control border-primary">
                <option value="1" {{ request('debit_movements') == '1' ? 'selected' : '' }}>عرض</option>
                <option value="0" {{ request('debit_movements') == '0' ? 'selected' : '' }}>إخفاء</option>
            </select>
        </div>
        <div class="col">
            <label class="form-label">الحركات الدائنة</label>
            <select name="credit_movements" class="form-control border-primary">
                <option value="1" {{ request('credit_movements') == '1' ? 'selected' : '' }}>عرض</option>
                <option value="0" {{ request('credit_movements') == '0' ? 'selected' : '' }}>إخفاء</option>
            </select>
        </div>
        <div class="col">
            <label class="form-label">الحسابات الصفرية</label>
            <select name="zero_balances" class="form-control border-primary">
                <option value="1" {{ request('zero_balances') == '1' ? 'selected' : '' }}>عرض</option>
                <option value="0" {{ request('zero_balances') == '0' ? 'selected' : '' }}>إخفاء</option>
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-2">
            <label class="form-label">كشف بأرصدة</label>
            <select name="with_balances" class="form-control border-primary">
                <option value="0" {{ request('with_balances') == '0' ? 'selected' : '' }}>لا</option>
                <option value="1" {{ request('with_balances') == '1' ? 'selected' : '' }}>نعم</option>
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary fw-bold w-100" onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                عرض التقرير
                <i class="fa-solid fa-eye ms-1"></i>
            </button>
        </div>
    </div>
</form>

<div id="report" class="bg-white p-3 rounded-3 shadow-sm border-0 mb-5">
    <div class="d-flex justify-content-end align-items-end mb-2">
        <div class="export-buttons d-flex gap-2 align-items-center">
            <form action="{{ route('export.excel', 'trial_balance') }}" method="GET">
                @foreach(request()->query() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>

            <form action="{{ route('print.trial.balance') }}" method="GET" target="_blank">
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
        <table class="table table-striped-columns">
            <thead>
                <tr>
                    <th colspan="2" class="bg-dark text-center text-white">الحساب</th>
                    @if(request()->query('with_balances', '0') == '0')
                        <th colspan="2" class="bg-dark text-center text-white">رصيد اول المدة</th>
                        <th colspan="2" class="bg-dark text-center text-white">الحركة</th>
                    @endif
                    <th colspan="2" class="bg-dark text-center text-white">رصيد اخر المدة</th>
                </tr>
                <tr>
                    <th class="bg-dark text-center text-white">الرقم</th>
                    <th class="bg-dark text-center text-white">الإسم</th>
                    @if(request()->query('with_balances', '0') == '0')
                        <th class="bg-dark text-center text-white">مدين</th>
                        <th class="bg-dark text-center text-white">دائن</th>
                        <th class="bg-dark text-center text-white">مدين</th>
                        <th class="bg-dark text-center text-white">دائن</th>
                    @endif
                    <th class="bg-dark text-center text-white">مدين</th>
                    <th class="bg-dark text-center text-white">دائن</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $from = request()->query('from', now()->startOfYear()->format('Y-m-d'));
                    $to = request()->query('to', now()->endOfYear()->format('Y-m-d'));

                    $sum_beginning_debit = 0;
                    $sum_beginning_credit = 0;
                    $sum_movement_debit = 0;
                    $sum_movement_credit = 0;
                    $sum_final_debit = 0;
                    $sum_final_credit = 0;
                @endphp
                @foreach($trialBalance as $account)
                    @php
                        $balance = $account->calculateBalance($from, $to);
                        if('0' === request()->query('debit_movements') && $balance->final_debit > 0) {
                            continue;
                        }
                        if('0' === request()->query('credit_movements') && $balance->final_credit > 0) {
                            continue;
                        }
                        if('0' === request()->query('zero_balances') && $balance->final_debit == 0 && $balance->final_credit == 0) {
                            continue;
                        }

                        $sum_beginning_debit += $balance->beginning_debit;
                        $sum_beginning_credit += $balance->beginning_credit;
                        $sum_movement_debit += $balance->movement_debit;
                        $sum_movement_credit += $balance->movement_credit;
                        $sum_final_debit += $balance->final_debit;
                        $sum_final_credit += $balance->final_credit;
                    @endphp
                    <tr class="table-primary">
                        <td class="text-center">{{ $account->code }}</td>
                        <td class="fw-bold">{{ $account->name }} ({{ $account->level }})</td>
                        @if(request()->query('with_balances', '0') == '0')
                            <td class="text-center fw-bold">{{ $balance->beginning_debit }}</td>
                            <td class="text-center fw-bold">{{ $balance->beginning_credit }}</td>
                            <td class="text-center fw-bold">{{ $balance->movement_debit }}</td>
                            <td class="text-center fw-bold">{{ $balance->movement_credit }}</td>
                        @endif
                        <td class="text-center fw-bold">{{ $balance->final_debit }}</td>
                        <td class="text-center fw-bold">{{ $balance->final_credit }}</td>
                    </tr>
                    @if($account->children->count())
                        @include('pages.accounting.reports.trial_balance_row', ['children' => $account->children])
                    @endif
                @endforeach
                <tr class="table-secondary">
                    <td colspan="2" class="text-center fw-bold">الإجمالي</td>
                    @if(request()->query('with_balances', '0') == '0')
                        <td class="text-center fw-bold">{{ $sum_beginning_debit }}</td>
                        <td class="text-center fw-bold">{{ $sum_beginning_credit }}</td>
                        <td class="text-center fw-bold">{{ $sum_movement_debit }}</td>
                        <td class="text-center fw-bold">{{ $sum_movement_credit }}</td>
                    @endif
                    <td class="text-center fw-bold">{{ $sum_final_debit }}</td>
                    <td class="text-center fw-bold">{{ $sum_final_credit }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#type').select2({
        width: '100%',
        placeholder: 'اختر النوع',
        allowClear: true
    });

    $('#type').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var level = selectedOption.data('level') || '';
        $('#type_level').val(level);
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