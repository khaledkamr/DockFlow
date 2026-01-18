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
            {{ $child->name }}
            ({{ $child->level }})
        </td>
        @if($with_balances == '0')
            <td class="text-center">{{ $balance->beginning_debit }}</td>
            <td class="text-center">{{ $balance->beginning_credit }}</td>
            <td class="text-center">{{ $balance->movement_debit }}</td>
            <td class="text-center">{{ $balance->movement_credit }}</td>
        @endif
        <td class="text-center">{{ $balance->final_debit }}</td>
        <td class="text-center">{{ $balance->final_credit }}</td>
    </tr>
    @if($child->children->count())
        @include('reports.trial_balance_row', ['children' => $child->children])
    @endif
@endforeach