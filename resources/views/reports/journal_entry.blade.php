@extends('layouts.print')

@section('title', 'تقرير القيد' . '(' . $journal->code . ')')

@section('content')
<div class="text-center mt-4 mb-5">
    <h2 class="fw-bold">قيد محاسبي رقم {{ $journal->code }} - بتاريخ {{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}</h2>
</div>

<div class="table-container">
    <table class="table table-bordered border-dark table-striped">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الحساب</th>
                <th class="text-center bg-dark text-white">اسم السحاب</th>
                <th class="text-center bg-dark text-white">مدين</th>
                <th class="text-center bg-dark text-white">دائن</th>
                <th class="text-center bg-dark text-white">مركز التكلفة</th>
                <th class="text-center bg-dark text-white">البيان</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->lines as $line)
                <tr>
                    <td class="text-center">{{ $line->account->code }}</td>
                    <td class="text-center fw-bold">{{ $line->account->name }}</td>
                    <td class="text-center">{{ number_format($line->debit, 2) }}</td>
                    <td class="text-center">{{ number_format($line->credit, 2) }}</td>
                    <td class="text-center fw-bold">{{ $line->costCenter->name ?? '' }}</td>
                    <td class="text-center">{{ $line->description }}</td>
                </tr>
            @endforeach
            <tr class="table-primary border-dark text-center fw-bold">
                <td colspan="2" class="fs-6">إجمـــالـــي</td>
                <td class="fs-6">{{ $journal->totalDebit }}</td>
                <td class="fs-6">{{ $journal->totalCredit }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <div class="row">
        <div class="col-6">
            <p class="fw-bold">تم الإنشاء بواسطة: {{ $journal->made_by->name }}</p>
        </div>
        <div class="col-6">
            <p class="fw-bold">تم التعديل بواسطة: {{ $journal->modified_by->name ?? 'لم يتم التعديل' }}</p>
        </div>
    </div>
</div>

@endsection