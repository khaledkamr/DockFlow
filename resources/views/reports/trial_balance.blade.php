@extends('layouts.print')

@section('title', 'ميزان المراجعة')

@section('content')

<h5 class="fw-bold text-center mt-3">
    تقرير ميزان المراجعة
</h5>

<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead>
            <tr>
                <th colspan="2" class="text-center">الحساب</th>
                @if($with_balances == '0')
                    <th colspan="2" class="text-center">رصيد اول المدة</th>
                    <th colspan="2" class="text-center">الحركة</th>
                @endif
                <th colspan="2" class="text-center">رصيد اخر المدة</th>
            </tr>
            <tr>
                <th class="text-center">الرقم</th>
                <th class="text-center">الاسم</th>
                @if($with_balances == '0')
                    <th class="text-center">مدين</th>
                    <th class="text-center">دائن</th>
                    <th class="text-center">مدين</th>
                    <th class="text-center">دائن</th>
                @endif
                <th class="text-center">مدين</th>
                <th class="text-center">دائن</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sum_beginning_debit = 0;
                $sum_beginning_credit = 0;
                $sum_movement_debit = 0;
                $sum_movement_credit = 0;
                $sum_final_debit = 0;
                $sum_final_credit = 0;
            @endphp
            @foreach($trialBalance as $account)
                @php
                    $balance = $account->calculateBalance($from, $to);
                    if('0' === request()->query('debit_movements') && $balance->final_debit > 0) {
                        continue;
                    }
                    if('0' === request()->query('credit_movements') && $balance->final_credit > 0) {
                        continue;
                    }
                    if('0' === request()->query('zero_balances') && $balance->final_debit == 0 && $balance->final_credit == 0) {
                        continue;
                    }

                    $sum_beginning_debit += $balance->beginning_debit;
                    $sum_beginning_credit += $balance->beginning_credit;
                    $sum_movement_debit += $balance->movement_debit;
                    $sum_movement_credit += $balance->movement_credit;
                    $sum_final_debit += $balance->final_debit;
                    $sum_final_credit += $balance->final_credit;
                @endphp
                <tr class="table-primary border-dark">
                    <td class="text-center">{{ $account->code }}</td>
                    <td class="fw-bold">{{ $account->name }} ({{ $account->level }})</td>
                    @if($with_balances == '0')
                        <td class="text-center fw-bold">{{ $balance->beginning_debit }}</td>
                        <td class="text-center fw-bold">{{ $balance->beginning_credit }}</td>
                        <td class="text-center fw-bold">{{ $balance->movement_debit }}</td>
                        <td class="text-center fw-bold">{{ $balance->movement_credit }}</td>
                    @endif
                    <td class="text-center fw-bold">{{ $balance->final_debit }}</td>
                    <td class="text-center fw-bold">{{ $balance->final_credit }}</td>
                </tr>
                @if($account->children->count())
                    @include('reports.trial_balance_row', ['children' => $account->children])
                @endif
            @endforeach
            <tr class="table-secondary border-dark">
                <td colspan="2" class="text-center fw-bold">الإجمالي</td>
                @if($with_balances == '0')
                    <td class="text-center fw-bold">{{ $sum_beginning_debit }}</td>
                    <td class="text-center fw-bold">{{ $sum_beginning_credit }}</td>
                    <td class="text-center fw-bold">{{ $sum_movement_debit }}</td>
                    <td class="text-center fw-bold">{{ $sum_movement_credit }}</td>
                @endif
                <td class="text-center fw-bold">{{ $sum_final_debit }}</td>
                <td class="text-center fw-bold">{{ $sum_final_credit }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection