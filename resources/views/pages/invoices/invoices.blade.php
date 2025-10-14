@extends('layouts.app')

@section('title', 'الفواتير')

@section('content')
<h1 class="mb-4">الفواتير</h1>

<div class="row mb-4">
    <div class="col-md-5">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحــث عن فاتـــورة:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن فاتورة بالرقم او بإسم العميل او بتاريخ الفاتورة... "
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span>بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        <form method="GET" action="" class="d-flex flex-column">
            <label class="form-label text-dark fw-bold">تصفية حسب طريقــة الدفــع:</label>
            <div class="d-flex">
                <select name="paymentMethod" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('paymentMethod') === 'all' || !request()->query('paymentMethod') ? 'selected' : '' }}>
                        جميع الطرق</option>
                    <option value="كريدت" {{ request()->query('paymentMethod') === 'كريدت' ? 'selected' : '' }}>
                        كريدت</option>
                    <option value="تحويل بنكي" {{ request()->query('paymentMethod') === 'تحويل بنكي' ? 'selected' : '' }}>
                        تحويل بنكي</option>
                    <option value="كاش" {{ request()->query('paymentMethod') === 'كاش' ? 'selected' : '' }}>
                        كاش</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
    <div class="col-md-3">
        <form method="GET" action="" class="d-flex flex-column">
            <label class="form-label text-dark fw-bold">تصفية حسب الدفــع:</label>
            <div class="d-flex">
                <select name="payment" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('payment') === 'all' || !request()->query('payment') ? 'selected' : '' }}>
                        جميع الفواتير</option>
                    <option value="تم الدفع" {{ request()->query('payment') === 'كريدت' ? 'selected' : '' }}>
                        تم الدفع</option>
                    <option value="لم يتم الدفع" {{ request()->query('payment') === 'كاش' ? 'selected' : '' }}>
                        لم يتم الدفع</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الفاتــورة</th>
                <th class="text-center bg-dark text-white">العميــل</th>
                <th class="text-center bg-dark text-white">نوع الفاتورة</th>
                <th class="text-center bg-dark text-white">تم بواسطة</th>
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">طريقــة الدفـــع</th>
                <th class="text-center bg-dark text-white">تاريــخ الفــاتورة</th>
                <th class="text-center bg-dark text-white">عملية الدفع</th>
                <th class="text-center bg-dark text-white">الإجرائات</th>
            </tr>
        </thead>
        <tbody>
            @if ($invoices->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي فواتيـــر!</div>
                    </td>
                </tr>
            @else
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="text-center text-primary fw-bold">
                            @if($invoice->type == 'خدمات')
                                <a href="{{ route('invoices.services.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @else
                                <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @endif
                            
                        </td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $invoice->customer->id) }}"
                                class="text-dark text-decoration-none">
                                {{ $invoice->customer->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $invoice->type ?? '-' }}</td>
                        <td class="text-center">{{ $invoice->made_by->name ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $invoice->amount }}</td>
                        <td class="text-center">{{ $invoice->payment_method }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                        <td class="text-center fw-bold {{ $invoice->payment == 'تم الدفع' ? 'text-success' : 'text-danger' }}">
                            {{ $invoice->payment }}
                        </td>
                        <td class="d-flex justify-content-center align-items-center gap-2 text-center">
                            @if($invoice->type == 'خدمات')
                                <a href="{{ route('invoices.services.details', $invoice) }}" class="btn btn-sm btn-primary">
                                    عرض
                                </a>
                            @elseif($invoice->type == 'تخزين')
                                <a href="{{ route('invoices.details', $invoice) }}" class="btn btn-sm btn-primary">
                                    عرض
                                </a>
                            @elseif($invoice->type == 'تخليص')
                                <a href="{{ route('invoices.clearance.details', $invoice) }}" class="btn btn-sm btn-primary">
                                    عرض
                                </a>
                            @endif
                        </td>
                    </tr>
                    <div class="modal fade" id="updateInvoice{{ $invoice->id }}" tabindex="-1" aria-labelledby="updateInvoiceLabel{{ $invoice->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="updateInvoiceLabel{{ $invoice->id }}">تحديث بيانات الفاتورة</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="amount" class="form-label">المبلغ</label>
                                                <input type="text" class="form-control border-primary" name="amount" value="{{ $invoice->amount }}" readonly>
                                            </div>
                                            <div class="col">
                                                <label for="payment" class="form-label">عملية الدفع</label>
                                                <select name="payment" class="form-select border-primary" required>
                                                    <option value="" selected disabled>اختر عملية الدفع</option>
                                                    <option value="تم الدفع">تم الدفع</option>
                                                    <option value="لم يتم الدفع">لم يتم الدفع</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ الفاتورة</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection