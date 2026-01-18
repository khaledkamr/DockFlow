@extends('layouts.print')

@section('title', 'تقرير اشعارات النقل')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير اشعارات النقل من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم الاشعار</th>
                <th>رقم المعاملة</th>
                <th>التاريخ</th>
                <th>العميل</th>
                <th>المورد</th>
                <th>السائق</th>
                <th>السيارة</th>
                <th>البيان</th>
                <th>مكان التحميل</th>
                <th>مكان التسليم</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @if ($transportOrders->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي إشعارات نقل!</div>
                    </td>
                </tr>
            @else
                @foreach ($transportOrders as  $transportOrder)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $transportOrder->code }}</td>
                        <td class="text-center fw-bold">{{ $transportOrder->transaction->code }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($transportOrder->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $transportOrder->customer->name }}</td>
                        <td class="text-center">{{ $transportOrder->supplier->name ?? '-' }}</td>
                        <td class="text-center">{{ $transportOrder->supplier ? $transportOrder->driver_name : $transportOrder->driver->name ?? '-' }}</td>
                        <td class="text-center">{{ $transportOrder->supplier ? $transportOrder->vehicle_plate : $transportOrder->vehicle->plate_number ?? '-' }}</td>
                        <td class="text-center">{{ $transportOrder->containers->first()->code ?? '-' }}</td>
                        <td class="text-center">{{ $transportOrder->from }}</td>
                        <td class="text-center">{{ $transportOrder->to }}</td>
                        <td class="text-center">{{ $transportOrder->total_cost }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection