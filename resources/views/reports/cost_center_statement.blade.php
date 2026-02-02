@extends('layouts.print')

@section('title', 'كشف مركز تكلفة')

@section('content') 
<h5 class="fw-bold text-center mt-3">
    كشف مركز تكلفة {{ $cost_center->name ?? '' }}
</h5>
<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="p-3">
    <div class="table-container">
        <table class="table table-bordered border-dark">
            <thead>
                <tr class="table-dark border-dark fw-bold">
                    <th class="text-center">مركز التكلفة</th>
                    <th class="text-center">تاريخ</th>
                    <th class="text-center">رقم القيد</th>
                    <th class="text-center">رقم الحساب</th>
                    <th class="text-center">اسم الحساب</th>
                    <th class="text-center">البيان</th>
                    <th class="text-center">المصروف</th>
                </tr>
            </thead>
            <tbody>
                @if($statement->count() > 0)
                    @foreach($statement as $line)
                        <tr class="text-center">
                            <td>{{ $line->costCenter->name ?? '-' }}</td>
                            <td>{{ Carbon\Carbon::parse($line->journal->date)->format('Y/m/d') }}</td>
                            <td>{{ $line->journal->code }}</td>
                            <td>{{ $line->account->code ?? '-' }}</td>
                            <td>{{ $line->account->name ?? '-' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-primary border-dark fw-bold">
                        <td colspan="6" class="text-center fs-6">إجمالي المصروف</td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->debit), 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لا توجد مصاريف</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection