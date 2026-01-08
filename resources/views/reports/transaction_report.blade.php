@extends('layouts.print')

@section('title', 'تقرير معاملات التخليص')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير معاملات التخليص من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم المعاملة</th>
                <th>العميل</th>
                <th>عدد الحاويات</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>الفاتورة</th>
                <th>المصروفات</th>
                <th>ايراد التخليص</th>
                <th>ايراد النقل</th>
                <th>ايراد عمال</th>
                <th>ايراد سابر</th>
            </tr>
        </thead>
        <tbody>
            @if ($transactions->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي معاملات!</div>
                    </td>
                </tr>
            @else
                @foreach ($transactions as  $transaction)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $transaction->code }}</td>
                        <td class="text-center">{{ $transaction->customer->name }}</td>
                        <td class="text-center fw-bold">{{ $transaction->containers->count() }}</td>
                        <td class="text-center">{{ $transaction->status }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($transaction->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $transaction->containers->first()->invoices->where('type', 'تخليص')->first()->code ?? '-' }}</td>
                        <td class="text-center">{{ $transaction->items->where('type', 'مصروف')->sum('amount') }}</td>
                        <td class="text-center">{{ $transaction->items->where('type', 'ايراد تخليص')->sum('amount') }}</td>
                        <td class="text-center">{{ $transaction->items->where('type', 'ايراد نقل')->sum('amount') }}</td>
                        <td class="text-center">{{ $transaction->items->where('type', 'ايراد عمال')->sum('amount') }}</td>
                        <td class="text-center">{{ $transaction->items->where('type', 'ايراد سابر')->sum('amount') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-end text-center fw-bold">الاجمالي</td>
                    <td class="text-center fw-bold">{{ $transactions->sum(function($transaction) { return $transaction->items->where('type', 'مصروف')->sum('amount'); }) }}</td>
                    <td class="text-center fw-bold">{{ $transactions->sum(function($transaction) { return $transaction->items->where('type', 'ايراد تخليص')->sum('amount'); }) }}</td>
                    <td class="text-center fw-bold">{{ $transactions->sum(function($transaction) { return $transaction->items->where('type', 'ايراد نقل')->sum('amount'); }) }}</td>
                    <td class="text-center fw-bold">{{ $transactions->sum(function($transaction) { return $transaction->items->where('type', 'ايراد عمال')->sum('amount'); }) }}</td>
                    <td class="text-center fw-bold">{{ $transactions->sum(function($transaction) { return $transaction->items->where('type', 'ايراد سابر')->sum('amount'); }) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@endsection