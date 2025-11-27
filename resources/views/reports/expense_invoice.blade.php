@extends('layouts.print')

@section('title', "invoice-$invoice->code")

@section('content')
    <div class="mb-3 mt-3">
        <h5 class="fw-bold text-center mb-1 text-dark" style="font-size: 1.75rem;">فاتورة ضريبية</h5>
        <p class="text-center text-muted mb-0 small">TAX INVOICE</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="border border-dark rounded-3 p-3 bg-light h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات المورد</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-2 small">
                            <strong class="text-secondary">اسم المورد:</strong><br>{{ $invoice->supplier->name ?? '---' }}
                        </div>
                        <div class="small">
                            <strong class="text-secondary">رقم المورد:</strong><br>{{ $invoice->supplier->account->code ?? '---' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2 small">
                            <strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $invoice->supplier->vat_number ?? '---' }}
                        </div>
                        <div class="small">
                            <strong class="text-secondary">العنوان الوطني:</strong><br>{{ $invoice->supplier->national_address ?? '---' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border border-dark rounded-3 p-3 bg-light h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات الفاتورة</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-2 small">
                            <strong class="text-secondary">رقم الفاتورة:</strong><br>{{ $invoice->code ?? '---' }}
                        </div>
                        <div class="small">
                            <strong class="text-secondary">التاريخ:</strong><br>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2 small">
                            <strong class="text-secondary">رقم فاتورة المورد:</strong><br>{{ $invoice->supplier_invoice_number ?? '---' }}
                        </div>
                        <div class="small">
                            <strong class="text-secondary">طريقة الدفع:</strong><br>{{ $invoice->payment_method ?? '---' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-4">
        <table class="table table-bordered border-dark mb-0">
            <thead>
                <tr class="table-dark">
                    <th class="text-center fw-semibold">#</th>
                    <th class="text-center fw-semibold">البند</th>
                    <th class="text-center fw-semibold">البيان</th>
                    <th class="text-center fw-semibold">مركز التكلفة</th>
                    <th class="text-center fw-semibold">الكمية</th>
                    <th class="text-center fw-semibold">السعر</th>
                    <th class="text-center fw-semibold">المبلغ</th>
                    <th class="text-center fw-semibold">الضريبة</th>
                    <th class="text-center fw-semibold">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center small">{{ $index + 1 }}</td>
                        <td class="text-center small">{{ $item->account->name ?? '---' }}</td>
                        <td class="text-center small">{{ $item->description ?? '---' }}</td>
                        <td class="text-center small">{{ $item->costCenter->name ?? '---' }}</td>
                        <td class="text-center small">{{ $item->quantity }}</td>
                        <td class="text-center small">{{ number_format($item->price, 2) }}</td>
                        <td class="text-center small">{{ number_format($item->amount, 2) }}</td>
                        <td class="text-center small">{{ number_format($item->tax, 2) }}</td>
                        <td class="text-center small">{{ number_format($item->total_amount, 2) }} ر.س</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border border-2 border-dark rounded-3 p-4 bg-light mt-4">
        <div class="d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-secondary">الإجمالي قبل الضريبة</span>
            <span class="fw-bold text-dark">{{ number_format($invoice->amount_before_tax, 2) }} ر.س</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-secondary">الضريبة المضافة ({{ $invoice->tax_rate }}%)</span>
            <span class="fw-bold text-dark">{{ number_format($invoice->tax, 2) }} ر.س</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-secondary">الخصم
                ({{ $invoice->discount_rate ? $invoice->discount_rate . '%' : '0%' }})</span>
            <span class="fw-bold text-dark">- {{ number_format($invoice->discount ?? 0, 2) }} ر.س</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 mt-3 pt-3 summary-total">
            <span class="fw-bold fs-5 text-dark">الإجمالي بعد الضريبة</span>
            <span class="fw-bold fs-4 text-dark">{{ number_format($invoice->total_amount, 2) }} ر.س</span>
        </div>
        @if (isset($hatching_total) && $hatching_total)
            <div class="text-center mt-3 pt-3 border-top">
                <span class="text-muted fst-italic">{{ $hatching_total }}</span>
            </div>
        @endif
    </div>

@endsection
