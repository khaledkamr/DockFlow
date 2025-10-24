@extends('layouts.app')

@section('title', 'تفاصيل القيض')

@section('content')
<h1 class="mb-4">تفاصيل القيد</h1>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h5 class="fw-bold">تقرير قيد - {{ $journal->voucher_id ? $journal->voucher->type : 'قيد يومي' }} - بتاريخ {{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}</h5>
        <div>
            <button class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button>

            <button class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button>

            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th class="text-center" width="20%">رقم الحساب</th>
                    <th class="text-center" width="25%">اسم الحساب</th>
                    <th class="text-center" width="10%">مديــن</th>
                    <th class="text-center" width="10%">دائــن</th>
                    <th class="text-center" width="35%">البيـــان</th>
                </tr>
            </thead>
            <tbody>
                @if($journal->lines->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="status-danger fs-6">هذا القيض فارغ!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($journal->lines as $line)
                        <tr class="text-center">
                            <td>{{ $line->account->code }}</td>
                            <td class="fw-bold">{{ $line->account->name }}</td>
                            <td>{{ $line->debit }}</td>
                            <td>{{ $line->credit }}</td>
                            <td>{{ $line->description ?? '-' }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-primary text-center fw-bold">
                        <td colspan="2" class="fs-6">إجمـــالـــي</td>
                        <td class="fs-6">{{ $journal->totalDebit }}</td>
                        <td class="fs-6">{{ $journal->totalCredit }}</td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

