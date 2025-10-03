@foreach ($children as $child)
    <tr>
        @php
            $balance = $child->calculateBalance(request()->query('from'), request()->query('to'))->balance;
        @endphp
        <td class="text-center">{{ $child->code }}</td>
        <td class="fw-bold">
            @if($child->level == 5)
                <form action="" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="view" value="كشف حساب">
                    <input type="hidden" name="account" value="{{ $child->id }}">
                    <input type="hidden" name="from" value="{{ request()->query('from') }}">
                    <input type="hidden" name="to" value="{{ request()->query('to') }}">
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
        <td class="text-center">{{ $child->calculateBalance(null, Carbon\Carbon::parse(request()->query('from'))->subDay())->debit }}</td>
        <td class="text-center">{{ $child->calculateBalance(null, Carbon\Carbon::parse(request()->query('from'))->subDay())->credit }}</td>
        <td class="text-center">{{ $child->calculateBalance(request()->query('from'), request()->query('to'))->debit }}</td>
        <td class="text-center">{{ $child->calculateBalance(request()->query('from'), request()->query('to'))->credit }}</td>
        <td class="text-center">{{ $balance > 0 ? $balance : '0.00' }}</td>
        <td class="text-center">{{ $balance < 0 ? abs($balance) : '0.00' }}</td>
    </tr>
    @if($child->children->count())
        @include('pages.accounting.reports.trial_balance_row', ['children' => $child->children])
    @endif
@endforeach