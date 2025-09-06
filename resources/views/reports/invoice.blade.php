@extends('layouts.print')

@section('title', 'فاتورة ضريبية')

@section('content') 
<h5 class="fw-bold text-center mb-4">فاتورة ضريبية</h5>

<div class="d-flex justify-content-between mb-4">
    <div class="border rounded p-3 w-50 me-2">
        <h6 class="fw-bold mb-3">بيانات العميل</h6>
        <div class="d-flex justify-content-between">
            <div class="d-flex flex-column">
                <p><strong>اسم العميل:</strong> {{ $invoice->customer->name ?? '---' }}</p>
                <p><strong>رقم العميل:</strong> {{ $invoice->customer->account->code ?? '---' }}</p>
            </div>
            <div class="d-flex flex-column">
                <p><strong>الرقم الضريبي:</strong> {{ $invoice->customer->CR ?? '---' }}</p>
                <p><strong>العنوان الوطني:</strong> {{ $invoice->customer->national_address ?? '---' }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between border rounded p-3 w-50 ms-2">
        <div>
            <h6 class="fw-bold mb-3">بيانات الفاتورة</h6>
            <p><strong>رقم الفاتورة:</strong> {{ $invoice->code ?? '---' }}</p>
            <p><strong>التاريخ:</strong> {{ $invoice->date ?? now()->format('Y-m-d') }}</p>
        </div>
        
        <div>
            {!! $qrCode !!}
            {{-- <img src="{{ asset('img/qrcode.png') }}" alt="QR Code" width="120"> --}}
        </div>
    </div>
</div>

<div class="table-container">
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">رقم الإتفاقية</th>
                <th class="text-center">رقم الحاوية</th>
                <th class="text-center">نوع الحاوية</th>
                <th class="text-center">تاريخ الدخول</th>
                <th class="text-center">تاريخ الخروج</th>
                <th class="text-center">أيام التخزين</th>
                <th class="text-center">سعر التخزين</th>
                <th class="text-center">أيام التأخير</th>
                <th class="text-center">غرامة التأخير</th>
                <th class="text-center">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->policy->containers as $index => $container)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $invoice->policy->code }}</td>
                    <td class="text-center">{{ $container->code }}</td>
                    <td class="text-center">{{ $container->containerType->name }}</td>
                    <td class="text-center">{{ $container->date }}</td>
                    <td class="text-center">{{ $container->exit_date }}</td>
                    <td class="text-center">{{ $container->period }}</td>
                    <td class="text-center">{{ $container->storage_price }}</td>
                    <td class="text-center">{{ $container->late_days }}</td>
                    <td class="text-center">{{ $container->late_fee }}</td>
                    <td class="text-center">{{ $container->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-start gap-5 border p-3 rounded-3">
    <div class="d-flex flex-column me-5">
        <h4>الإجمالي قبل الضريبه</h4>
        <h4>الخصم</h4>
        <h4>الضريبة المضافة (15%)</h4>
        <hr>
        <h3>الإجمالي بعد الضريبة</h3>
    </div>
    <div class="d-flex flex-column text-end ms-5">
        <h4>{{ $invoice->subtotal }}</h4>
        <h4>{{ $invoice->discount }}</h4>
        <h4>{{ $invoice->tax }}</h4>
        <hr>
        <h3 class="fw-bold">{{ $invoice->total }}</h3>
    </div>
    <h3 class="align-self-end fw-bold ms-5">{{ $hatching_total }}</h3>
</div>
@endsection
