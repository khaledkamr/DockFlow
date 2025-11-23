@extends('layouts.app')

@section('title', 'تفاصيل القيد')

@section('content')
<h1 class="mb-4">تفاصيل القيد {{ $journal->code }}</h1>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h5 class="fw-bold">{{ $journal->voucher_id ? $journal->voucher->type : 'قيد يومي' }} - بتاريخ {{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}</h5>
        <div class="export-buttons d-flex gap-2 align-items-center">
            <button class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button>

            <form action="{{ route('print', 'journal_entry') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="journal_id" value="{{ $journal->id }}">
                <button type="submit" class="btn btn-outline-primary" target="top" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>

            <a href="{{ route('journal.edit', $journal) }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="تعديل">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>

            <div data-bs-toggle="tooltip" data-bs-placement="top" title="حذف">
                <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteJournalModal">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteJournalModal" tabindex="-1"
        aria-labelledby="deleteJournalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold"
                        id="deleteJournalModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center text-dark">
                    هل انت متأكد من حذف القيد؟
                </div>
                <div
                    class="modal-footer d-flex flex-column flex-sm-row justify-content-center">
                    <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                        data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('journal.delete', $journal) }}" method="POST"
                        class="order-1 order-sm-2 w-sm-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                    </form>
                </div>
            </div>
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

