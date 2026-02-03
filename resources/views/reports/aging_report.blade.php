@extends('layouts.print')

@section('title', 'تقرير أعمار الذمم')

@section('content')
<h5 class="fw-bold text-center mt-3">
    تقرير أعمار الذمم - {{ $selectedCustomer ? $selectedCustomer->name : 'جميع العملاء' }}
</h5>
<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="p-3">
    @if(request('report_type') == 'single' && request('customer_id'))
        <div class="table-container">
            @if ($selectedCustomer)
                <table class="table table-bordered border-dark">
                    <thead>
                        <tr>
                            <th class="text-center">اسم العميل</th>
                            <th class="text-center">رقم الفاتورة</th>
                            <th class="text-center">نوع الفاتورة</th>
                            <th class="text-center">تاريخ الفاتورة</th>
                            <th class="text-center">موعد السداد</th>
                            <th class="text-center">أيام التأخير</th>
                            <th class="text-center">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unpaidInvoices as $invoice)
                            <tr>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $selectedCustomer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $selectedCustomer->name }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">{{ $invoice->code }}</td>
                                <td class="text-center">{{ $invoice->type }}</td>
                                <td class="text-center text-nowrap">{{ \Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                <td class="text-center text-nowrap">
                                    {{ $invoice->payment_due_date ?  $invoice->payment_due_date->format('Y/m/d') : '' }}
                                </td>
                                <td class="text-center fw-bold text-nowrap">
                                    {{ (int) $invoice->late_days }} يوم
                                </td>
                                <td class="text-center text-nowrap">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">لا توجد فواتير غير مسددة</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold table-primary border-dark">
                            <td class="text-center text-nowrap" colspan="6">الإجمالي</td>
                            <td class="text-center text-nowrap">
                                {{ number_format($unpaidInvoices->sum('total_amount'), 2) }} ر.س
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="alert alert-warning text-center">لم يتم العثور على العميل المحدد</div>
            @endif
        </div>
    @else
        <div class="table-container">
            <table class="table table-bordered border-dark">
                <thead>
                    <tr class="table-dark border-dark fw-bold">
                        <th class="text-center">اسم العميل</th>
                        <th class="text-center">حالي (0 يوم)</th>
                        <th class="text-center">1-30 يوم</th>
                        <th class="text-center">31-60 يوم</th>
                        <th class="text-center">+90 يوم</th>
                        <th class="text-center">إجمالي الرصيد</th>
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
                                لا توجد بيانات للعرض. يرجى تحديد فترة صحيحة.
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-primary border-dark">
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
    @endif
</div>

@endsection