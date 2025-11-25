@extends('layouts.app')

@section('title', 'تفاصيل القيد')

@section('content')
<h1 class="mb-4">تفاصيل القيد {{ $journal->code }}</h1>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h5 class="fw-bold">{{ $journal->voucher_id ? $journal->voucher->type : 'قيد يومي' }} - بتاريخ {{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}</h5>
        <div class="export-buttons d-flex gap-2 align-items-center">
            {{-- <button class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button> --}}

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

    <div class="row g-3 mt-4 pt-2 border-top">
        <div class="col-md-6">
            <div class="card border-primary border-2 p-2 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <i class="fa-solid fa-user-plus text-primary"></i>
                        </div>
                        <h6 class="card-title text-primary mb-0 fw-bold">معلومات الإنشاء</h6>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1">تم الإنشاء بواسطة</small>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    {{ $journal->made_by ? $journal->made_by->name : 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-dark fw-semibold">
                                    {{ $journal->created_at ? $journal->created_at->timezone(auth()->user()->timezone)->format('Y/m/d') : 'غير محدد' }}
                                </span>
                                @if($journal->created_at)
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $journal->created_at->timezone(auth()->user()->timezone)->format('h:i A') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-info border-2 p-2 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <i class="fa-solid fa-pen-to-square text-info"></i>
                        </div>
                        <h6 class="card-title text-info mb-0 fw-bold">آخر تعديل</h6>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1">تم التعديل بواسطة</small>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                    {{ $journal->modified_by ? $journal->modified_by->name : 'لا يوجد تعديل' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">تاريخ التعديل</small>
                            <div class="d-flex align-items-center gap-2">
                                @if($journal->updated_at && $journal->updated_at != $journal->created_at)
                                <span class="text-dark fw-semibold">
                                    {{ $journal->updated_at->timezone(auth()->user()->timezone)->format('Y/m/d') }}
                                </span>
                                <span class="badge bg-info rounded-pill">
                                    {{ $journal->updated_at->timezone(auth()->user()->timezone)->format('h:i A') }}
                                </span>
                                @else
                                <span class="text-muted fst-italic">لا يوجد تعديل</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

