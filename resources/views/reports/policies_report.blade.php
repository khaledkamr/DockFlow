@extends('layouts.print')

@section('title', 'تقرير بوالص التخزين')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير بوالص التخزين من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم البوليصة</th>
                <th>النوع</th>
                <th>بوليصة التسليم</th>
                <th>العميل</th>
                <th>الحاوية</th>
                <th>تاريخ الدخول</th>
                <th>تاريخ الخروج</th>
                <th>أيام التخزين</th>
                <th>الفاتورة</th>
            </tr>
        </thead>
        <tbody>
            @if ($policies->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي بوالص!</div>
                    </td>
                </tr>
            @else
                @foreach ($policies as $policy)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $policy->code }}</td>
                        <td class="text-center">{{ $policy->type }}</td>
                        <td class="text-center fw-bold">
                            @if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first())
                                {{ $policy->containers->first()->policies->where('type', 'تسليم')->first()->code }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ $policy->customer->name }}</td>
                        <td class="text-center">
                            @if($policy->containers->first())
                                {{ $policy->containers->first()->code }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($policy->containers->first())
                                {{ Carbon\Carbon::parse($policy->containers->first()->date)->format('Y/m/d') ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($policy->containers->first() && $policy->containers->first()->exit_date)
                                {{ Carbon\Carbon::parse($policy->containers->first()->exit_date)->format('Y/m/d') ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($policy->type == 'تخزين')
                                {{ $policy->containers->first() ? $policy->containers->first()->storage_days : '-' }}
                            @elseif($policy->type == 'خدمات')
                                0
                            @endif
                        </td>
                        <td class="text-center fw-bold">
                            @if($policy->containers->first())
                                @if ($policy->containers->first()->invoices->where('type', 'تخزين')->first())
                                    {{ $policy->containers->first()->invoices->where('type', 'تخزين')->first()->code }}
                                @elseif($policy->containers->first()->invoices->where('type', 'خدمات')->first())
                                    {{ $policy->containers->first()->invoices->where('type', 'خدمات')->first()->code }}
                                @else
                                    -
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection