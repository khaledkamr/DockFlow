@extends('layouts.print')

@section('title', 'تقرير الفواتير')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير الفواتير من فترة ({{ $from }}) الى فترة ({{ Carbon\Carbon::parse($to)->format('Y-m-d') }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم الفاتورة</th>
                <th>التاريخ</th>
                <th>العميل</th>
                <th>نوع الفاتورة</th>
                <th>طريقة الدفع</th>
                <th>المبلغ</th>
                <th>الضريبة المضافة</th>
                <th>الإجمالي</th>
                <th>الدفع</th>
            </tr>
        </thead>
        <tbody>
            @if ($invoices->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي فواتير!</div>
                    </td>
                </tr>
            @else
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $invoice->code }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($invoice->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $invoice->customer->name }}</td>
                        <td class="text-center">{{ $invoice->type }}</td>
                        <td class="text-center">{{ $invoice->payment_method }}</td>
                        <td class="text-center">{{ $invoice->amount_before_tax }}</td>
                        <td class="text-center">{{ $invoice->tax }}</td>
                        <td class="text-center">{{ $invoice->total_amount }}</td>
                        @if ($invoice->isPaid == 'تم الدفع')
                            <td class="text-center">مدفوعة</td>
                        @else
                            <td class="text-center">غير مدفوعة</td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection