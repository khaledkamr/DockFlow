@extends('layouts.print')

@section('title', "invoice-$invoice->code")

@section('content')
<style>
    .table thead { background-color: #2c3e50; color: white; }
    .table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .summary-total { border-top: 2px solid #2c3e50; }
</style>

<div class="mb-3 mt-3">
    <h5 class="fw-bold text-center mb-1 text-dark" style="font-size: 1.75rem;">فاتورة ضريبية</h5>
</div>

<!-- Invoice and Customer Details -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="border border-dark rounded-3 p-3 bg-light h-100">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات العميل</h6>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">اسم العميل:</strong><br>{{ $invoice->customer->name ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">رقم العميل:</strong><br>{{ $invoice->customer->account->code ?? '---' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-2 small"><strong class="text-secondary">الرقم الضريبي:</strong><br>{{ $invoice->customer->vatNumber ?? '---' }}</p>
                    <p class="mb-2 small"><strong class="text-secondary">العنوان الوطني:</strong><br>{{ $invoice->customer->national_address ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="border border-dark rounded-3 p-3 bg-light h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات الفاتورة</h6>
                    <div class="row">
                        <div class="col-7">
                            <p class="mb-2 small"><strong class="text-secondary">رقم الفاتورة:</strong><br>{{ $invoice->code ?? '---' }}</p>
                            <p class="mb-2 small"><strong class="text-secondary">التاريخ:</strong><br>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') ?? '---' }}</p>
                        </div>
                    </div>
                </div>
                <div class="border rounded p-2 bg-white">
                    {!! $qrCode !!}
                </div>
            </div>
        </div>
    </div>
</div>

@if($invoice->type == 'تخزين' || $invoice->type == 'تخزين و شحن')
    <div class="my-4">
        <div class="table-responsive">
            <table class="table table-bordered border-dark mb-0">
                <thead>
                    <tr class="table-dark">
                        <th class="text-center fw-semibold" style="white-space: nowrap;">#</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">رقم البوليصة</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">رقم الحاوية</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">تاريخ الدخول</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">تاريخ الخروج</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">أيام التخزين</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">سعر التخزين</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">أيام التأخير</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">غرامة التأخير</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">خدمات</th>
                        <th class="text-center fw-semibold" style="white-space: nowrap;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->containers as $container)
                        <tr>
                            <td class="text-center small">{{ $loop->iteration }}</td>
                            <td class="text-center small">{{ $container->policies->where('type', 'تسليم')->first()->code }}</td>
                            <td class="text-center small">{{ $container->code }}</td>
                            <td class="text-center small" style="white-space: nowrap;">{{ Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                            <td class="text-center small" style="white-space: nowrap;">{{ Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                            <td class="text-center small">{{ $container->storage_days }}</td>
                            <td class="text-center small">{{ number_format($container->storage_price, 2) }}</td>
                            <td class="text-center small">{{ $container->late_days }}</td>
                            <td class="text-center small">{{ number_format($container->late_fee, 2) }}</td>
                            <td class="text-center small">{{ number_format($container->total_services, 2) }}</td>
                            <td class="text-center small fw-semibold">{{ number_format($container->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if($invoice->type == 'شحن' || $invoice->type == 'تخزين و شحن')
    <div class="my-4">
        <div class="table-responsive">
            <table class="table table-bordered border-dark mb-0">
                <thead>
                    <tr class="table-dark">
                        <th class="text-center fw-semibold">#</th>
                        <th class="text-center fw-semibold">رقم البوليصة</th>
                        <th class="text-center fw-semibold">تاريخ البوليصة</th>
                        <th class="text-center fw-semibold">البيان</th>
                        <th class="text-center fw-semibold">اسم السائق</th>
                        <th class="text-center fw-semibold">رقم اللوحة</th>
                        <th class="text-center fw-semibold">مكان التحميل</th>
                        <th class="text-center fw-semibold">مكان التسليم</th>
                        <th class="text-center fw-semibold">رسوم فسح</th>
                        <th class="text-center fw-semibold">غرامة تأخير</th>
                        <th class="text-center fw-semibold">المبلغ</th>
                        <th class="text-center fw-semibold">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->shippingPolicies as $policy)
                        <tr>
                            <td class="text-center small">{{ $loop->iteration }}</td>
                            <td class="text-center small">{{ $policy->code }}</td>
                            <td class="text-center small" style="white-space: nowrap;">{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                            <td class="text-center small">{{ $policy->goods->first()->description ?? '---' }}</td>
                            @if($policy->type == 'ناقل داخلي')
                                <td class="text-center small">{{ $policy->driver->name }}</td>
                                <td class="text-center small">{{ $policy->vehicle->plate_number }}</td>
                            @elseif ($policy->type == 'ناقل خارجي')
                                <td class="text-center small">{{ $policy->driver_name }}</td>
                                <td class="text-center small">{{ $policy->vehicle_plate }}</td>
                            @endif
                            <td class="text-center small">{{ $policy->from }}</td>
                            <td class="text-center small">{{ $policy->to }}</td>
                            <td class="text-center small">{{ number_format($policy->clearance_fee, 2) }}</td>
                            <td class="text-center small">{{ number_format($policy->late_fee, 2) }}</td>
                            <td class="text-center small">{{ number_format($policy->client_cost, 2) }}</td>
                            <td class="text-center small">{{ number_format($policy->total_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if($invoice->type == 'خدمات')
    <div class="my-4">
        <table class="table table-bordered border-dark mb-0">
            <thead>
                <tr class="table-dark">
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
                @foreach ($invoice->containers as $container)
                    <tr>
                        <td class="text-center small">{{ $loop->iteration }}</td>
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
@endif

@if($invoice->type == 'تخليص')
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="border border-dark rounded-3 p-3 bg-light h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">بيانات المعاملة</h6>
                <div class="row">
                    <div class="col">
                        <div class="small"><strong class="text-secondary">رقم المعاملة:</strong><br>{{ $invoice->containers->first()->transactions->first()->code ?? '---' }}</div>
                    </div>
                    <div class="col">
                        <div class="small"><strong class="text-secondary">رقم البوليصة:</strong><br>{{ $invoice->containers->first()->transactions->first()->policy_number ?? '---' }}</div>
                    </div>
                    <div class="col">
                        <div class="small"><strong class="text-secondary">رقم البيان الجمركي:</strong><br>{{ $invoice->containers->first()->transactions->first()->customs_declaration ?? '---' }}</div>
                    </div>
                    <div class="col">
                        <div class="small"><strong class="text-secondary">تاريخ البيان الجمركي:</strong><br>{{ $invoice->containers->first()->transactions->first()->customs_declaration_date ?? '---' }}</div>
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
                    <th class="text-center fw-semibold">رقم الحاوية</th>
                    <th class="text-center fw-semibold">فئة الحاوية</th>
                    <th class="text-center fw-semibold">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->containers as $index => $container)
                    <tr>
                        <td class="text-center small">{{ $index + 1 }}</td>
                        <td class="text-center small">{{ $container->code }}</td>
                        <td class="text-center small">{{ $container->containerType->name }}</td>
                        <td class="text-center small">{{ $container->notes ?? '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="my-4">
        <table class="table table-bordered border-dark mb-0">
            <thead>
                <tr class="table-dark">
                    <th class="text-center fw-semibold">#</th>
                    <th class="text-center fw-semibold">البند</th>
                    <th class="text-center fw-semibold">المبلغ</th>
                    <th class="text-center fw-semibold">الضريبة</th>
                    <th class="text-center fw-semibold">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->clearanceInvoiceItems->sortBy('number') as $item)
                    <tr>
                        <td class="text-center small">{{ $item->number }}</td>
                        <td class="text-center small">{{ $item->description }}</td>
                        <td class="text-center small">{{ number_format($item->amount, 2) }}</td>
                        <td class="text-center small">{{ number_format($item->tax, 2) }}</td>
                        <td class="text-center small">{{ number_format($item->total, 2) }} ر.س</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="border border-dark border-2 border-dark rounded-3 p-4 bg-light mt-4">
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الإجمالي قبل الضريبة</span>
        <span class="fw-bold text-dark">{{ number_format($invoice->amount_before_tax, 2) }} ر.س</span>
    </div>
    <div class="d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold text-secondary">الضريبة المضافة ({{ $invoice->tax_rate }}%)</span>
        <span class="fw-bold text-dark">{{ number_format($invoice->tax, 2) }} ر.س</span>
    </div>
    @if($invoice->discount > 0 || $invoice->discount_amount > 0)
        <div class="d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-secondary">الخصم ({{ $invoice->discount ? $invoice->discount . '%' : '0%' }})</span>
            <span class="fw-bold text-dark">- {{ number_format($invoice->discount_amount, 2) }} ر.س</span>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center py-2 mt-3 pt-3 summary-total">
        <span class="fw-bold fs-5 text-dark">الإجمالي بعد الضريبة</span>
        <span class="fw-bold fs-4 text-dark">{{ number_format($invoice->total_amount, 2) }} ر.س</span>
    </div>
    <div class="text-center mt-3 pt-3 border-top">
        <span class="text-dark fst-italic">{{ $hatching_total }}</span>
    </div>
</div>

@if(auth()->user()->company->bankAccounts->isNotEmpty())
    <div class="border border-dark rounded-3 p-3 bg-light mt-4">
        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-2">الحسابات البنكية للشركة</h6>
        <div class="row">
            @foreach(auth()->user()->company->bankAccounts as $bankAccount)
                <div class="col-6">
                    <div class="small"><strong class="text-secondary">{{ $bankAccount->bank }}: </strong>{{ $bankAccount->account_number }}</div>
                    <div class="small"><strong class="text-secondary">رقم الحساب الدولي (IBAN): </strong>{{ $bankAccount->iban ?? 'N/A' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection