@extends('layouts.print')

@section('title', 'تقرير الحاويات')

@section('content')
<h5 class="text-center fw-bold mb-3">تقرير الحاويات من فترة ({{ $from }}) الى فترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr class="text-center">
                <th>#</th>
                <th>كود الحاويــة</th>
                <th>صاحــب الحاويــة</th>
                <th>الفئـــة</th>
                <th>الموقــع</th>
                <th>الحالـــة</th>
                <th>تم الإستلام بواسطة</th>
                <th>تم التسليم بواسطة</th>
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
                @foreach ($containers as $index => $container)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center text-primary fw-bold">{{ $container->code }}</td>
                        <td class="text-center">{{ $container->customer->name }}</td>
                        <td class="text-center">{{ $container->containerType->name }}</td>
                        <td class="text-center">{{ $container->location ?? '-' }}</td>
                        <td class="text-center">
                            @if($container->status == 'متوفر')
                                <div class="status-available">{{ $container->status }}</div>
                            @elseif($container->status == 'مُسلم')
                                <div class="status-delivered">
                                    {{ $container->status }}
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            @elseif($container->status == 'متأخر')
                                <div class="status-danger">{{ $container->status }}</div>
                            @elseif($container->status == 'في الإنتظار')
                                <div class="status-waiting">{{ $container->status }}</div>
                            @endif
                        </td>
                        <td class="text-center {{ $container->received_by ? 'text-dark' : 'text-muted' }}">
                            {{ $container->received_by ?? 'لم يتم الإستلام بعد' }}
                        </td>
                        <td class="text-center {{ $container->delivered_by ? 'text-dark' : 'text-muted' }}">
                            {{ $container->delivered_by ?? 'لم يتم التسليم بعد' }}
                        </td>
                        <td class="text-center">{{ $container->date ?? '-' }}</td>
                        <td class="text-center">{{ $container->exit_date ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection