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
                <th>رقم البوليصة</th>
                <th>العميل</th>
                <th>عدد الحاويات</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>تم بواسطة</th>
                <th>الفاتورة</th>
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
                @php
                    $index = 1;
                @endphp
                @foreach ($transactions as  $transaction)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td class="text-center text-primary fw-bold">{{ $transaction->code }}</td>
                        <td class="text-center">{{ $transaction->policy_number ?? '-' }}</td>
                        <td class="text-center">{{ $transaction->customer->name }}</td>
                        <td class="text-center fw-bold">{{ $transaction->containers->count() }}</td>
                        <td class="text-center">{{ $transaction->status }}</td>
                        <td class="text-center">{{ (Carbon\Carbon::parse($transaction->date)->format('Y/m/d')) }}</td>
                        <td class="text-center">{{ $transaction->made_by->name }}</td>
                        <td class="text-center">{{ $transaction->containers->first()->invoices->where('type', 'تخليص')->first()->code ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection