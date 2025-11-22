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
                <th>نوع الناقل</th>
                <th>المورد</th>
                <th>السائق</th>
                <th>السيارة</th>
                <th>البيان</th>
                <th>مكان التحميل</th>
                <th>مكان التسليم</th>
                <th>الحالة</th>
                <th>المبلغ</th>
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
                @php
                    $index = 1;
                @endphp
                @foreach ($policies as  $policy)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td class="text-center text-primary fw-bold">{{ $policy->code }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($policy->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $policy->customer->name }}</td>
                        <td class="text-center">{{ $policy->type }}</td>
                        <td class="text-center">{{ $policy->supplier->name ?? '-' }}</td>
                        <td class="text-center">{{ $policy->supplier ? $policy->driver_name : $policy->driver->name ?? '-' }}</td>
                        <td class="text-center">{{ $policy->supplier ? $policy->vehicle_plate : $policy->vehicle->plate_number ?? '-' }}</td>
                        <td class="text-center">{{ $policy->goods->first()->description }}</td>
                        <td class="text-center">{{ $policy->from }}</td>
                        <td class="text-center">{{ $policy->to }}</td>
                        <td class="text-center">
                            @if($policy->is_received)
                                <div class="status-available">تم التسليم</div>
                            @else
                                <div class="status-delivered">تحت التسليم</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $policy->total_cost }}</td>
                        <td class="text-center">{{ $policy->invoices->where('type', 'شحن')->first()->code ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection