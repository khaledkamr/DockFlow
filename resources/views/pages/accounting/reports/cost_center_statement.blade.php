<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="كشف مركز تكلفة">
    <div class="col-md-6">
        <label class="form-label">مركز التكلفة</label>
        <select name="cost_center" id="cost_center_id" class="form-select border-primary" required>
            <option value="">اختر مركز التكلفة</option>
            @foreach($costCenters as $costCenter)
                <option value="{{ $costCenter->id }}" {{ request('cost_center') == $costCenter->id ? 'selected' : '' }}>
                    {{ $costCenter->name }}
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
            <form action="{{ route('export.excel', 'cost_center_statement') }}" method="GET">
                <input type="hidden" name="cost_center" value="{{ request()->query('cost_center') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>
            
            <form action="{{ route('print.cost.center.statement') }}" method="GET" target="_blank">
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
                    <th class="bg-dark text-center text-white text-nowrap">مركز التكلفة</th>
                    <th class="bg-dark text-center text-white text-nowrap">تاريخ</th>
                    <th class="bg-dark text-center text-white text-nowrap">رقم القيد</th>
                    <th class="bg-dark text-center text-white text-nowrap">رقم الحساب</th>
                    <th class="bg-dark text-center text-white text-nowrap">اسم الحساب</th>
                    <th class="bg-dark text-center text-white text-nowrap">البيان</th>
                    <th class="bg-dark text-center text-white text-nowrap">المصروف</th>
                </tr>
            </thead>
            <tbody>
                @if($statement->count() > 0)
                    @foreach($statement as $line)
                        <tr class="text-center">
                            <td>{{ $line->costCenter->name ?? '-' }}</td>
                            <td>{{ Carbon\Carbon::parse($line->journal->date)->format('Y/m/d') }}</td>
                            <td class="fw-bold">
                                <a href="{{ route('journal.details', $line->journal) }}" class="text-decoration-none text-dark">
                                    {{ $line->journal->code }}
                                </a>
                            </td>
                            <td>{{ $line->account->code ?? '-' }}</td>
                            <td>{{ $line->account->name ?? '-' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-primary fw-bold">
                        <td colspan="6" class="text-center fs-6">إجمالي المصروف</td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->debit), 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لا توجد مصاريف</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#cost_center_id').select2({
            placeholder: "اختر مركز التكلفة",
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
