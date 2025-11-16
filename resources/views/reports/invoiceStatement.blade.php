@extends('layouts.print')

@section('title', "مطالبة-$invoiceStatement->code")

@section('content')
<style>
    .table thead { background-color: #2c3e50; color: white; }
    .table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .summary-total { border-top: 2px solid #2c3e50; }
</style>

<div class="mb-3 mt-3">
    <h5 class="fw-bold text-center mb-1 text-dark" style="font-size: 1.75rem;">مطالبة فواتير</h5>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="border border-dark rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات العميل</h6>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">اسم العميل:</strong><br>{{ $invoiceStatement->customer->name ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">رقم العميل:</strong><br>{{ $invoiceStatement->customer->account->code ?? '---' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $invoiceStatement->customer->CR ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">العنوان الوطني:</strong><br>{{ $invoiceStatement->customer->national_address ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="border border-dark rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات المطالبة</h6>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">رقم المطالبة:</strong><br>{{ $invoiceStatement->code ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">التاريخ:</strong><br>{{ Carbon\Carbon::parse($invoiceStatement->date)->format('Y/m/d') ?? '---' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">الملاحظات:</strong><br>{{ $invoiceStatement->notes ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="my-4">
    <div class="table-responsive">
        <table class="table table-bordered border-dark mb-0">
            <thead>
                <tr class="table-primary">
                    <th class="text-center fw-semibold" style="white-space: nowrap;">#</th>
                    <th class="text-center fw-semibold" style="white-space: nowrap;">رقم الفاتورة</th>
                    <th class="text-center fw-semibold" style="white-space: nowrap;">نوع الفاتورة</th>
                    <th class="text-center fw-semibold" style="white-space: nowrap;">تاريخ الفاتورة</th>
                    <th class="text-center fw-semibold" style="white-space: nowrap;">قيمة الضريبة المضافة</th>
                    <th class="text-center fw-semibold" style="white-space: nowrap;">قيمة الفاتورة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoiceStatement->invoices as $index => $invoice)
                    <tr>
                        <td class="text-center small">{{ $index + 1 }}</td>
                        <td class="text-center small">{{ $invoice->code }}</td>
                        <td class="text-center small">{{ $invoice->type }}</td>
                        <td class="text-center small" style="white-space: nowrap;">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                        <td class="text-center small" style="white-space: nowrap;">{{ number_format($invoice->tax, 2) }}</td>
                        <td class="text-center small">{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="border border-2 border-dark rounded-3 p-4 bg-light mt-4">
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الإجمالي قبل الضريبة</span>
        <span class="fw-bold text-dark">{{ number_format($invoiceStatement->subtotal, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">إجمالي الضريبة المضافة</span>
        <span class="fw-bold text-dark">{{ number_format($invoiceStatement->tax, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2 mt-3 pt-3 summary-total">
        <span class="fw-bold fs-5 text-dark">الإجمالي بعد الضريبة</span>
        <span class="fw-bold fs-4 text-dark">{{ number_format($invoiceStatement->amount, 2) }} ر.س</span>
    </div>
    <div class="text-center mt-3 pt-3 border-top">
        <span class="text-muted fst-italic">{{ $hatching_total }}</span>
    </div>
</div>

@if($company->bankAccounts->isNotEmpty())
    <div class="border border-dark rounded-3 p-3 bg-light mt-4">
        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">الحسابات البنكية للشركة</h6>
        <div class="row">
            @foreach($company->bankAccounts as $bankAccount)
                <div class="col-6">
                    <div class="small"><strong class="text-secondary">{{ $bankAccount->bank }}: </strong>{{ $bankAccount->account_number }}</div>
                    <div class="small"><strong class="text-secondary">رقم الحساب الدولي (IBAN): </strong>{{ $bankAccount->iban ?? 'N/A' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection