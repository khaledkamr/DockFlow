@extends('layouts.print')

@section('title', 'تقرير الحاويات')

@section('content')
<h5 class="text-center fw-bold mb-4 mt-4">تقرير الحاويات من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark">
        <thead class="table-dark">
            <tr class="text-center">
                <th>#</th>
                <th>كود الحاويــة</th>
                <th>صاحــب الحاويــة</th>
                <th>الفئـــة</th>
                <th>الموقــع</th>
                <th>الحالـــة</th>
                <th>تاريخ الدخول</th>
                <th>تاريخ الخروج</th>
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
                @php
                    $index = 1;
                @endphp
                @foreach ($containers as $container)
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td class="text-center fw-bold">{{ $container->code }}</td>
                        <td class="text-center">{{ $container->customer->name }}</td>
                        <td class="text-center">{{ $container->containerType->name }}</td>
                        <td class="text-center">{{ $container->location ?? '-' }}</td>
                        <td class="text-center">
                            @if($container->status == 'في الساحة')
                                <div class="status-available">{{ $container->status }}</div>
                            @elseif($container->status == 'تم التسليم')
                                <div class="status-delivered">{{ $container->status }}</div>
                            @elseif($container->status == 'متأخر')
                                <div class="status-danger">{{ $container->status }}</div>
                            @elseif($container->status == 'خدمات')
                                <div class="status-waiting">{{ $container->status }}</div>
                            @elseif($container->status == 'في الميناء')
                                <div class="status-info">{{ $container->status }}</div>
                            @elseif($container->status == 'قيد النقل')
                                <div class="status-purple">{{ $container->status }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ Carbon\Carbon::parse($container->date)->format('Y/m/d') ?? '-' }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection