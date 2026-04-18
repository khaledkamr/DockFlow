@extends('layouts.print')

@section('title', 'تقرير بوالص التخزين')

@section('content')
<h1 class="text-center fw-bold">تقرير بوالص التخزين</h1>
<h5 class="text-center fw-bold mb-4">من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم البوليصة</th>
                <th>النوع</th>
                <th>بوليصة التسليم</th>
                <th>إسم العميل</th>
                <th>الحاوية</th>
                <th>الرقم المرجعي</th>
                <th>تاريخ الدخول</th>
                <th>إسم السائق</th>
                <th>لوحة السيارة</th>
                <th>تاريخ الخروج</th>
                <th>إسم السائق</th>
                <th>لوحة السيارة</th>
                <th>أيام التخزين</th>
                <th>أيام التأخير</th>
                <th>السعر الأساسي</th>
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
                        <td class="text-center">{{ $policy->reference_number ?? '-' }}</td>
                        <td class="text-center">
                            @if($policy->containers->first())
                                {{ Carbon\Carbon::parse($policy->containers->first()->date)->format('Y/m/d') ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $policy->driver_name }}
                        </td>
                        <td class="text-center">
                            {{ $policy->car_code }}
                        </td>
                        <td class="text-center">
                            @if($policy->containers->first() && $policy->containers->first()->exit_date)
                                {{ Carbon\Carbon::parse($policy->containers->first()->exit_date)->format('Y/m/d') ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first())
                                {{ $policy->containers->first()->policies->where('type', 'تسليم')->first()->driver_name }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first())
                                {{ $policy->containers->first()->policies->where('type', 'تسليم')->first()->car_code }}
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
                        <td class="text-center text-nowrap">
                            @if($policy->type == 'تخزين')
                                @if($policy->containers->first())
                                    {{ $policy->containers->first()->storage_days > $policy->storage_duration && $policy->storage_duration ? 
                                        $policy->containers->first()->storage_days - $policy->storage_duration : 0 }}
                                @else
                                    0
                                @endif
                            @elseif($policy->type == 'خدمات')
                                0
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            @if($policy->type == 'تخزين')
                                {{ number_format($policy->storage_price, 2) }}
                            @elseif($policy->type == 'خدمات')
                                {{ $policy->containers->first() ? number_format($policy->containers->first()->services->first()->pivot->price, 2) : '-' }}
                            @endif
                        </td>
                        <td class="text-center fw-bold">
                            @if($policy->containers->first())
                                @if ($policy->containers->first()->invoices()->where('type', 'LIKE', '%تخزين%')->first())
                                    {{ $policy->containers->first()->invoices()->where('type', 'LIKE', '%تخزين%')->first()->code }}
                                @elseif($policy->containers->first()->invoices->where('type', 'تخليص')->first())
                                    {{ $policy->containers->first()->invoices->where('type', 'تخليص')->first()->code }}
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