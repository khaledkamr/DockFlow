@extends('layouts.admin')

@section('title', 'مطالبة فواتير')

@section('content')
<div class="container">
    <h1 class="mb-4">مطالبة فواتير</h1>

    <form method="GET" action="{{ route('invoices.claim') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <select name="customer_id" class="form-select border-primary" required>
                    <option value="">-- اختر العميل --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary fw-bold">عرض الفواتير</button>
            </div>
        </div>
    </form>

    @if(isset($invoices) && $invoices->count() > 0)
    <form method="POST" action="">
        @csrf
        <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr class="">
                        <th class="text-center bg-dark text-white" width="10%">
                            <button type="button" class="btn btn-sm btn-primary fw-bold">تحديد الكل</button>
                        </th>
                        <th class="text-center fw-bold bg-dark text-white">#</th>
                        <th class="text-center fw-bold bg-dark text-white">رقم الفاتورة</th>
                        <th class="text-center fw-bold bg-dark text-white">إسم العميل</th>
                        <th class="text-center fw-bold bg-dark text-white">التاريخ</th>
                        <th class="text-center fw-bold bg-dark text-white">المبلغ</th>
                        <th class="text-center fw-bold bg-dark text-white">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr class="text-center">
                        <td>
                            <input type="checkbox" name="invoices[]" value="{{ $invoice->id }}">
                        </td>
                        <td class="fw-bold">{{ $loop->iteration }}</td>
                        <td class="text-primary fw-bold">
                            <a href="{{ route('invoices.details', $invoice->code) }}" class="text-decoration-none">
                                {{ $invoice->code }}
                            </a>
                        </td>
                        <td class="fw-bold ">
                            <a href="{{ route('users.customer.profile', $invoice->customer->id) }}" class="text-decoration-none text-dark">
                                {{ $invoice->customer->name }}
                            </a>
                        </td>
                        <td>{{ $invoice->date }}</td>
                        <td class="fw-bold">{{ number_format($invoice->amount, 2) }}</td>
                        <td><span class="badge bg-danger">غير مدفوعة</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary fw-bold mt-4">إنشاء مطالبة</button>
    </form>
    @elseif(request('customer_id'))
        <div class="alert alert-info">لا توجد فواتير غير مدفوعة لهذا العميل.</div>
    @endif
</div>
@endsection
