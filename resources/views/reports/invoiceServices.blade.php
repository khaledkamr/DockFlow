@extends('layouts.print')

@section('title', 'فاتورة ضريبية')

@section('content')
<style>
    .table thead { background-color: #2c3e50; color: white; }
    .table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .summary-total { border-top: 2px solid #2c3e50; }
</style>

<div class="border-bottom border-3 border-dark pb-3 mb-4">
    <h5 class="fw-bold text-center mb-1 text-dark" style="font-size: 1.75rem;">فاتورة ضريبية</h5>
    <p class="text-center text-muted mb-0 small">TAX INVOICE</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="border rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات العميل</h6>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">اسم العميل:</strong><br>{{ $invoice->customer->name ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">رقم العميل:</strong><br>{{ $invoice->customer->account->code ?? '---' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $invoice->customer->CR ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">العنوان الوطني:</strong><br>{{ $invoice->customer->national_address ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="border rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات الفاتورة</h6>
            <div class="row">
                <div class="col-7">
                    <p class="mb-2 small"><strong class="text-secondary">رقم الفاتورة:</strong><br>{{ $invoice->code ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">التاريخ:</strong><br>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}</p>
                </div>
                <div class="col-5 d-flex align-items-center justify-content-center">
                    <div class="border rounded p-2 bg-white">
                        {!! $qrCode !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="my-4">
    <table class="table table-bordered mb-0">
        <thead>
            <tr class="table-primary">
                <th class="text-center fw-semibold">#</th>
                <th class="text-center fw-semibold">رقم الإتفاقية</th>
                <th class="text-center fw-semibold">رقم الحاوية</th>
                <th class="text-center fw-semibold">تاريخ الدخول</th>
                <th class="text-center fw-semibold">تاريخ الخروج</th>
                <th class="text-center fw-semibold">الخدمة</th>
                <th class="text-center fw-semibold">سعر الخدمة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->containers as $index => $container)
                <tr>
                    <td class="text-center small">{{ $index + 1 }}</td>
                    <td class="text-center small">{{ $container->policies->where('type', 'خدمات')->first()->code }}</td>
                    <td class="text-center small">{{ $container->code }}</td>
                    <td class="text-center small">{{ Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                    <td class="text-center small">{{ Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                    <td class="text-center small">{{ $container->services->first()->description }}</td>
                    <td class="text-center small">{{ number_format($container->services->first()->pivot->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="border border-2 border-dark rounded-3 p-4 bg-light mt-4">
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الإجمالي قبل الضريبة</span>
        <span class="fw-bold text-dark">{{ number_format($invoice->subtotal, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الضريبة المضافة (15%)</span>
        <span class="fw-bold text-dark">{{ number_format($invoice->tax, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الخصم ({{ $invoice->discount ? $invoice->discount . '%' : '0%' }})</span>
        <span class="fw-bold text-dark">- {{ number_format($discountValue, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2 mt-3 pt-3 summary-total">
        <span class="fw-bold fs-5 text-dark">الإجمالي بعد الضريبة</span>
        <span class="fw-bold fs-4 text-dark">{{ number_format($invoice->total, 2) }} ر.س</span>
    </div>
    <div class="text-center mt-3 pt-3 border-top">
        <span class="text-muted fst-italic">{{ $hatching_total }}</span>
    </div>
</div>
@endsection