@extends('layouts.admin')

@section('title', 'الفواتير')

@section('content')
<style>
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-waiting {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-available {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

<h1 class="mb-4">الفواتير</h1>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحــث عن فاتـــورة:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن فاتورة بإسم العميل او بتاريخ الفاتورة... "
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span>بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="paymentMethodFilter" class="form-label text-dark fw-bold">تصفية حسب طريقــة الدفــع:</label>
            <div class="d-flex">
                <select id="paymentMethodFilter" name="paymentMethod" class="form-select border-primary" onchange="this.form.submit()">
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
</div>

<div class="table-container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الفاتــورة</th>
                <th class="text-center bg-dark text-white">رقــم العقــد</th>
                <th class="text-center bg-dark text-white">العميــل</th>
                <th class="text-center bg-dark text-white">سعــر الإيجــار</th>
                <th class="text-center bg-dark text-white">غرامــة التأخيــر</th>
                <th class="text-center bg-dark text-white">الضريبـــة المضافــة</th>
                <th class="text-center bg-dark text-white">إجمالــي المبلـــغ</th>
                <th class="text-center bg-dark text-white">طريقــة الدفـــع</th>
                <th class="text-center bg-dark text-white">تاريــخ الفــاتورة</th>
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
                        <td class="text-center">{{ $invoice->id }}</td>
                        <td class="text-center">{{ $invoice->contract_id }}</td>
                        <td class="text-center">{{ $invoice->user->name }}</td>
                        <td class="text-center">{{ $invoice->base_price }}</td>
                        <td class="text-center">{{ $invoice->late_fee_total }}</td>
                        <td class="text-center">{{ $invoice->tax_total }}</td>
                        <td class="text-center">{{ $invoice->grand_total }}</td>
                        <td class="text-center">{{ $invoice->payment_method }}</td>
                        <td class="text-center">{{ $invoice->date }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection