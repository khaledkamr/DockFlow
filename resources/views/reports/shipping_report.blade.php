@extends('layouts.print')

@section('title', 'تقرير بوالص الشحن')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير بوالص الشحن من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم البوليصة</th>
                <th>التاريخ</th>
                <th>العميل</th>
                <th>المورد</th>
                @if(request('net_profit') == '0')
                    <th>السائق</th>
                    <th>السيارة</th>
                    <th>البيان</th>
                @endif
                <th>مكان التحميل</th>
                <th>مكان التسليم</th>
                <th>الحالة</th>
                @if(request('net_profit') == '0')
                    <th>المبلغ</th>
                @elseif(request('net_profit') == '1')
                    <th>تكلفة المورد</th>
                    <th>سعر العميل</th>
                    <th>صافي الربح</th>
                @endif
                <th>الفاتورة</th>
            </tr>
        </thead>
        <tbody>
            @if ($policies->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي بوالص!</div>
                    </td>
                </tr>
            @else
                @foreach ($policies as  $policy)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $policy->code }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($policy->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $policy->customer->name }}</td>
                        <td class="text-center">{{ $policy->supplier->name ?? '-' }}</td>
                        @if(request('net_profit') == '0')
                            <td class="text-center">{{ $policy->supplier ? $policy->driver_name : $policy->driver->name ?? '-' }}</td>
                            <td class="text-center">{{ $policy->supplier ? $policy->vehicle_plate : $policy->vehicle->plate_number ?? '-' }}</td>
                            <td class="text-center">{{ $policy->goods->first()->description }}</td>
                        @endif
                        <td class="text-center">{{ $policy->from }}</td>
                        <td class="text-center">{{ $policy->to }}</td>
                        <td class="text-center">
                            @if($policy->is_received)
                                <div class="status-available">تم التسليم</div>
                            @else
                                <div class="status-delivered">تحت التسليم</div>
                            @endif
                        </td>
                        @if(request('net_profit') == '0')
                            <td class="text-center">{{ $policy->total_cost }}</td>
                        @elseif(request('net_profit') == '1')
                            <td class="text-center">{{ $policy->supplier_cost }}</td>
                            <td class="text-center">{{ $policy->total_cost }}</td>
                            <td class="text-center">{{ $policy->total_cost - $policy->supplier_cost }}</td>
                        @endif
                        <td class="text-center fw-bold">{{ $policy->invoices->where('type', 'شحن')->first()->code ?? '-' }}</td>
                    </tr>
                @endforeach

                @if(request('net_profit') == '1')
                    <tr class="table-primary table-bordered border-dark fw-bold">
                        <td colspan="7"></td>
                        <td class="text-center">الإجمالي</td>
                        <td class="text-center">
                            {{ $policies->sum('supplier_cost') }}
                        </td>
                        <td class="text-center">
                            {{ $policies->sum('total_cost') }}
                        </td>
                        <td class="text-center">
                            {{ $policies->sum('total_cost') - $policies->sum('supplier_cost') }}
                        </td>
                        <td></td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>
</div>

@endsection