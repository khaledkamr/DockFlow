@extends('layouts.app')

@section('title', 'إشعارات النقل')

@section('content')
    <h1 class="mb-4">إشعارات النقل</h1>

    <div class="row mb-4">
        <div class="col">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن إشعار:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن إشعار بإسم العميل او بكود الإشعار او بالتاريخ... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('transactions.transportOrders.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-plus pe-1"></i>
                إضافة إشعار نقل
            </a>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">كود الإشعار</th>
                    <th class="text-center bg-dark text-white">كود المعاملة</th>
                    <th class="text-center bg-dark text-white">إسم العميل</th>
                    <th class="text-center bg-dark text-white">تاريخ الإشعار</th>
                    <th class="text-center bg-dark text-white">تم بواسطة</th>
                    <th class="text-center bg-dark text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($transportOrders->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي إشعارات نقل!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($transportOrders as $order)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $order->code }}</td>
                            <td class="text-center text-primary fw-bold">{{ $order->transaction->code }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.customer.profile', $order->customer->id) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $order->customer->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($order->date ?? $order->created_at)->format('Y/m/d') }}
                            </td>
                            <td class="text-center">{{ $order->made_by->name ?? '-' }}</td>
                            <td class="action-icons text-center">
                                <a href="{{ route('transactions.transportOrders.details', $order) }}" class="btn btn-sm btn-primary">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

@endsection
