@extends('layouts.print')

@section('title', 'كشف حساب')

@section('content')

<h5 class="fw-bold text-center mt-3">
    كشف حساب للحساب: {{ $account->name }} ({{ $account->code }})
</h5>
<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="p-3">
    <h5 class="mb-4">الرصيد الافتتاحي: <strong>{{ $opening_balance ?? 0.00 }}</strong></h5>
    <div class="table-container">
        <table class="table table-bordered border-dark">
            <thead>
                <tr class="table-dark border-dark fw-bold">
                    <th class="text-center">التاريخ</th>
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
                            <td>{{ Carbon\Carbon::parse($line->journal->date)->format('Y/m/d') }}</td>
                            <td>{{ $line->journal->code }}</td>
                            <td>{{ $line->journal->voucher->type ?? 'قيد يومي' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ $line->debit }}</td>
                            <td>{{ $line->credit }}</td>
                            <td>{{ $balance }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold">
                        <td colspan="4" class="text-center fs-6">
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