@extends('layouts.print')

@section('title', 'تقرير الفواتير المبيعات')

@section('content')
    <h1 class="text-center fw-bold">تقرير فواتير المبيعات</h1>
    <h5 class="text-center fw-bold mb-4"> من فترة ({{ $from }}) الى فترة
        ({{ Carbon\Carbon::parse($to)->format('Y-m-d') }})
    </h5>

    <div class="table-container">
        <table class="table table-bordered border-dark">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>رقم الفاتورة</th>
                    <th>نوع الفاتورة</th>
                    <th>إسم العميل</th>
                    <th>التاريخ</th>
                    <th>موعد السداد</th>
                    <th>المبلغ</th>
                    <th>الضريبة المضافة</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>المبلغ المسدد</th>
                    <th>المبلغ المتبقي</th>
                </tr>
            </thead>
            <tbody>
                @if ($invoices->isEmpty())
                    <tr>
                        <td colspan="12" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي فواتير!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center fw-bold">{{ $invoice->code }}</td>
                            <td class="text-center">{{ $invoice->type }}</td>
                            <td class="text-center">{{ $invoice->customer->name }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                            <td class="text-center">{{ $invoice->paymentDueDate }}</td>
                            <td class="text-center">{{ $invoice->amount_before_tax }}</td>
                            <td class="text-center">{{ $invoice->tax }}</td>
                            <td class="text-center">{{ $invoice->total_amount }}</td>
                            <td class="text-center">{{ $invoice->status }}</td>
                            <td class="text-center">{{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="text-center">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

@endsection
