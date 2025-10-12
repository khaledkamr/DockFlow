@extends('layouts.app')

@section('title', 'المعاملات')

@section('content')
    <h1 class="mb-4">المعاملات</h1>

    <div class="row mb-4">
        <div class="col">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن معاملة:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن معاملة بإسم العميل او بكود المعاملة او بالتاريخ... "
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('transactions.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-plus pe-1"></i>
                إضافة معاملة
            </a>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">كود المعاملة</th>
                    <th class="text-center bg-dark text-white">إسم العميل</th>
                    <th class="text-center bg-dark text-white">نوع المعاملة</th>
                    <th class="text-center bg-dark text-white">تاريخ المعاملة</th>
                    <th class="text-center bg-dark text-white">المبلغ</th>
                    <th class="text-center bg-dark text-white">تم بواسطة</th>
                    <th class="text-center bg-dark text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($transactions->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي معاملات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $transaction->code }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.customer.profile', $transaction->customer->id) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $transaction->customer->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $transaction->type }}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($transaction->date ?? $transaction->created_at)->format('Y/m/d') }}
                            </td>
                            <td class="text-center">
                                {{ number_format($transaction->amount ?? ($transaction->total ?? 0), 2) }}</td>
                            <td class="text-center">{{ $transaction->made_by->name ?? '-' }}</td>
                            <td class="action-icons text-center">
                                <a href="{{ route('transactions.details', $transaction) }}" class="btn btn-sm btn-primary">
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
