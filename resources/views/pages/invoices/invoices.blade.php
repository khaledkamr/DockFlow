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
                    <option value="آجل" {{ request()->query('paymentMethod') === 'آجل' ? 'selected' : '' }}>
                        آجل</option>
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
                <select name="isPaid" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('isPaid') === 'all' || !request()->query('isPaid') ? 'selected' : '' }}>
                        جميع الفواتير</option>
                    <option value="تم الدفع" {{ request()->query('isPaid') === 'تم الدفع' ? 'selected' : '' }}>
                        تم الدفع</option>
                    <option value="لم يتم الدفع" {{ request()->query('isPaid') === 'لم يتم الدفع' ? 'selected' : '' }}>
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
                <th class="text-center bg-dark text-white">المبلغ</th>
                <th class="text-center bg-dark text-white">طريقــة الدفـــع</th>
                <th class="text-center bg-dark text-white">تاريــخ الفــاتورة</th>
                <th class="text-center bg-dark text-white">عملية الدفع</th>
                <th class="text-center bg-dark text-white">تم بواسطة</th>
                <th class="text-center bg-dark text-white">الإجرائات</th>
            </tr>
        </thead>
        <tbody>
            @if ($invoices->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
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
                            @elseif($invoice->type == 'تخزين')
                                <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @elseif($invoice->type == 'تخليص')
                                <a href="{{ route('invoices.clearance.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @elseif($invoice->type == 'شحن')
                                <a href="{{ route('invoices.shipping.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $invoice->customer) }}"
                                class="text-dark text-decoration-none">
                                {{ $invoice->customer->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $invoice->type ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $invoice->total_amount }} <i data-lucide="saudi-riyal"></i></td>
                        <td class="text-center">{{ $invoice->payment_method }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                        @if($invoice->isPaid == 'تم الدفع')
                            <td class="text-center"><span class="badge status-delivered">تم الدفع</span></td>
                        @else
                            <td class="text-center"><span class="badge status-danger">لم يتم الدفع</span></td>
                        @endif
                        <td class="text-center">
                            <a href="{{ route('admin.user.profile', $invoice->made_by) }}" class="text-dark text-decoration-none">
                                {{ $invoice->made_by->name ?? '-' }}
                            </a>
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
                            @elseif($invoice->type == 'شحن')
                                <a href="{{ route('invoices.shipping.details', $invoice) }}" class="btn btn-sm btn-primary">
                                    عرض
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection