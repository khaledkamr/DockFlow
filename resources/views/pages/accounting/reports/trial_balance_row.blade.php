@foreach ($children as $child)
    <tr>
        <td class="text-center">{{ $child->code }}</td>
        <td class="fw-bold">
            @if($child->level == 5)
                <form action="" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="view" value="كشف حساب">
                    <input type="hidden" name="account" value="{{ $child->id }}">
                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">
                    <button type="submit" class="btn btn-link fw-semibold p-0 m-0 align-baseline text-decoration-none">
                        @for($i = 0; $i < $child->level; $i++) - @endfor
                        {{ $child->name }}
                        ({{ $child->level }})
                    </button>
                </form>
            @else
                @for($i = 0; $i < $child->level; $i++) - @endfor
                {{ $child->name }}
                ({{ $child->level }})
            @endif
        </td>
        <td class="text-center">{{ $child->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->debit }}</td>
        <td class="text-center">{{ $child->calculateBalance(null, Carbon\Carbon::parse($from)->subDay())->credit }}</td>
        <td class="text-center">{{ $child->calculateBalance($from, $to)->debit }}</td>
        <td class="text-center">{{ $child->calculateBalance($from, $to)->credit }}</td>
        <td class="text-center">{{ $child->calculateBalance($from, $to)->balance['debit'] }}</td>
        <td class="text-center">{{ $child->calculateBalance($from, $to)->balance['credit'] }}</td>
    </tr>
    @if($child->children->count())
        @include('pages.accounting.reports.trial_balance_row', ['children' => $child->children])
    @endif
@endforeach