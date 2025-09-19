@extends('layouts.print')

@section('title', 'كشف حساب')

@section('content')

<h5 class="fw-bold text-center">كشف حساب من الفترة ({{ $from }}) للفترة ({{ $to }})</h5>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <h5>الرصيد الافتتاحي: <strong>{{ $opening_balance ?? 0.00 }}</strong></h5>
    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr class="table-secondary">
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
                @forelse($statement as $line)
                @php
                    if($line->debit > 0) {
                        $balance += $line->debit;
                    } else {
                        $balance -= $line->credit;
                    }
                @endphp
                    <tr class="text-center">
                        <td>{{ $line->account->name }}</td>
                        <td>{{ $line->journal->date }}</td>
                        <td>{{ $line->journal_entry_id }}</td>
                        <td>{{ $line->journal->voucher->type ?? 'قيد يومي' }}</td>
                        <td>{{ $line->description }}</td>
                        <td>{{ $line->debit }}</td>
                        <td>{{ $line->credit }}</td>
                        <td>{{ $balance }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="status-danger fs-6">لا توجد حركات</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <h5 class="mt-4">الرصيد الختامي: <strong>{{ $balance }}</strong></h5>
</div>

@endsection