@extends('layouts.print')

@section('title', 'تقرير الحاويات')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير الحاويات من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>رقم الحاويــة</th>
                <th>العميل</th>
                <th>النوع</th>
                <th>الموقــع</th>
                <th>الحالـــة</th>
                <th>تاريخ الدخول</th>
                <th>تاريخ الخروج</th>
                <th>الفاتورة</th>
            </tr>
        </thead>
        <tbody>
            @if ($containers->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي حاويات!</div>
                    </td>
                </tr>
            @else
                @foreach ($containers as $container)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $container->code }}</td>
                        <td class="text-center">{{ $container->customer->name }}</td>
                        <td class="text-center">{{ $container->containerType->name }}</td>
                        <td class="text-center">{{ $container->location ?? '-' }}</td>
                        <td class="text-center">
                            {{ $container->status }}
                        </td>
                        <td class="text-center">{{ $container->date ? Carbon\Carbon::parse($container->date)->format('Y/m/d') : '-' }}</td>
                        <td class="text-center">{{ $container->exit_date ? Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : '-' }}</td>
                        <td class="text-center">
                            @if ($container->invoices->isNotEmpty())
                                @foreach ($container->invoices as $invoice)
                                    <span class="fw-bold">{{ $invoice->code }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection