@extends('layouts.print')

@section('title', 'كشف حساب')

@section('content')

<h5 class="fw-bold text-center mt-3">
    كشف حساب: {{ $account->name }} ({{ $account->code }})
</h5>
<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="p-3">
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
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center">الرصيد الافتتاحي</td>
                    <td class="text-center">{{ $opening_balance < 0 ? $opening_balance : '0.00' }}</td>
                    <td class="text-center">{{ $opening_balance > 0 ? $opening_balance : '0.00' }}</td>
                    <td class="text-center">{{ $opening_balance ?? '0.00' }}</td>
                </tr>
                @php
                    $balance = $opening_balance ?? 0;
                @endphp
                @if($statement)
                    @foreach($statement as $line)
                        @php
                            $balance += $line->debit - $line->credit;
                        @endphp
                        <tr class="text-center">
                            <td>{{ Carbon\Carbon::parse($line->journal->date)->format('Y/m/d') }}</td>
                            <td>{{ $line->journal->code }}</td>
                            <td>{{ $line->journal->voucher->type ?? 'قيد يومي' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                            <td>{{ number_format($line->credit, 2) }}</td>
                            <td>{{ number_format($balance, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold">
                        <td colspan="4" class="text-center fs-6">
                            الإجماليـــــات
                        </td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->debit), 2) }}</td>
                        <td class="text-center">{{ number_format($statement->sum(fn($line) => $line->credit), 2) }}</td>
                        <td class="text-center">{{ number_format($balance, 2) }}</td>
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
</div>

@endsection