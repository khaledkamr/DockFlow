@extends('layouts.print')

@section('title', 'كشف حساب')

@section('content')

<h5 class="fw-bold text-center">كشف حساب من الفترة ({{ $from }}) للفترة ({{ $to }})</h5>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <h5 class="mb-4">الرصيد الافتتاحي: <strong>{{ $opening_balance ?? 0.00 }}</strong></h5>
    <div class="table-container">
        <table class="table table-bordered border-dark">
            <thead>
                <tr class="table-secondary border-dark fw-bold">
                    <th class="text-center">رقم الحساب</th>
                    <th class="text-center">إسم الحساب</th>
                    <th class="text-center">تاريخ</th>
                    <th class="text-center">رقم القيد</th>
                    <th class="text-center">نوع القيد</th>
                    <th class="text-center">البيان</th>
                    <th class="text-center">مدين</th>
                    <th class="text-center">دائن</th>
                    <th class="text-center">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = 0;
                @endphp
                @if($statement)
                    @foreach($statement as $line)
                        @php
                            if($line->debit > 0) {
                                $balance += $line->debit;
                            } else {
                                $balance -= $line->credit;
                            }
                        @endphp
                        <tr class="text-center">
                            <td>{{ $line->account->code }}</td>
                            <td>{{ $line->account->name }}</td>
                            <td>{{ $line->journal->date }}</td>
                            <td>{{ $line->journal_entry_id }}</td>
                            <td>{{ $line->journal->voucher->type ?? 'قيد يومي' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ $line->debit }}</td>
                            <td>{{ $line->credit }}</td>
                            <td>{{ $balance }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold">
                        <td colspan="6" class="text-center fs-6">
                            الإجماليـــــات
                        </td>
                        <td class="text-center">{{ $statement->sum(fn($line) => $line->debit) }}</td>
                        <td class="text-center">{{ $statement->sum(fn($line) => $line->credit) }}</td>
                        <td class="text-center">{{ $balance }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="status-danger fs-6">لا توجد حركات</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <h5 class="mt-4">الرصيد الختامي: <strong>{{ $balance }}</strong></h5>
</div>

@endsection