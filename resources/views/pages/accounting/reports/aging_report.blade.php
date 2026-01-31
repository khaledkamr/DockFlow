<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="أعمار الذمم">
    
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
            <form action="{{ route('export.excel', 'aging_report') }}" method="GET">
                <input type="hidden" name="customer" value="{{ request()->query('customer') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>
            
            <form action="{{ route('print', 'aging_report') }}" method="POST" target="_blank">
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
                    <th class="bg-dark text-center text-white text-nowrap">اسم العميل</th>
                    <th class="bg-dark text-center text-white text-nowrap">حالي (0 يوم)</th>
                    <th class="bg-dark text-center text-white text-nowrap">1-30 يوم</th>
                    <th class="bg-dark text-center text-white text-nowrap">31-60 يوم</th>
                    <th class="bg-dark text-center text-white text-nowrap">+90 يوم</th>
                    <th class="bg-dark text-center text-white text-nowrap">إجمالي الرصيد</th>
                </tr>
            </thead>
            <tbody>
                @if(request()->query('from') && request()->query('to'))
                    @foreach($customers as $customer)
                        <tr>
                            <td class="text-center fw-bold">
                                <a href="{{ route('users.customer.profile', $customer) }}" class="text-decoration-none text-dark">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="text-center text-nowrap">
                                {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 0, 0), 2) }}
                                ر.س 
                                ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 0, 0) }})
                            </td>
                            <td class="text-center text-nowrap">
                                {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 1, 30), 2) }}
                                ر.س 
                                ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 1, 30) }})
                            </td>
                            <td class="text-center text-nowrap">
                                {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 31, 60), 2) }}
                                ر.س 
                                ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 31, 60) }})
                            </td>
                            <td class="text-center text-nowrap">
                                {{ number_format($customer->agingBalance(request()->query('from'), request()->query('to'), 61, null), 2) }}
                                ر.س 
                                ({{ $customer->agingBalanceCount(request()->query('from'), request()->query('to'), 61, null) }})
                            </td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($customer->totalAgingBalance(request()->query('from'), request()->query('to')), 2) }}
                                ر.س 
                                ({{ $customer->totalAgingBalanceCount(request()->query('from'), request()->query('to')) }})
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="fw-bold table-secondary">
                    <td class="text-center text-nowrap">الإجمالي</td>
                    <td class="text-center text-nowrap">
                        {{ number_format($customers->sum(function($customer) {
                            return $customer->agingBalance(request()->query('from'), request()->query('to'), 0, 0);
                        }), 2) }}
                    </td>
                    <td class="text-center text-nowrap">
                        {{ number_format($customers->sum(function($customer) {
                            return $customer->agingBalance(request()->query('from'), request()->query('to'), 1, 30);
                        }), 2) }}
                    </td>
                    <td class="text-center text-nowrap">
                        {{ number_format($customers->sum(function($customer) {
                            return $customer->agingBalance(request()->query('from'), request()->query('to'), 31, 60);
                        }), 2) }}
                    </td>
                    <td class="text-center text-nowrap">
                        {{ number_format($customers->sum(function($customer) {
                            return $customer->agingBalance(request()->query('from'), request()->query('to'), 61, null);
                        }), 2) }}
                    </td>
                    <td class="text-center text-nowrap">
                        {{ number_format($customers->sum(function($customer) {
                            return $customer->totalAgingBalance(request()->query('from'), request()->query('to'));
                        }), 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#customer_id').select2({
            placeholder: "اختر العميل",
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

