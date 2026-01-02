@foreach ($children as $child)
    @php
        $balance = $child->calculateBalance($from, $to);
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
    <tr>
        <td class="text-center">{{ $child->code }}</td>
        <td class="fw-bold">
            @if($child->level == 5)
                <form action="" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="view" value="كشف حساب">
                    <input type="hidden" name="account" value="{{ $child->id }}">
                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">
                    <button type="submit" class="btn btn-link fw-bold p-0 m-0 align-baseline text-decoration-none">
                        {{ str_repeat(' - ', $child->level) }}
                        {{ $child->name }}
                        ({{ $child->level }})
                    </button>
                </form>
            @else
                {{ str_repeat(' - ', $child->level) }}
                {{ $child->name }}
                ({{ $child->level }})
            @endif
        </td>
        @if(request()->query('with_balances', '0') == '0')
            <td class="text-center">{{ $balance->beginning_debit }}</td>
            <td class="text-center">{{ $balance->beginning_credit }}</td>
            <td class="text-center">{{ $balance->movement_debit }}</td>
            <td class="text-center">{{ $balance->movement_credit }}</td>
        @endif
        <td class="text-center">{{ $balance->final_debit }}</td>
        <td class="text-center">{{ $balance->final_credit }}</td>
    </tr>
    @if($child->children->count())
        @include('pages.accounting.reports.trial_balance_row', ['children' => $child->children])
    @endif
@endforeach