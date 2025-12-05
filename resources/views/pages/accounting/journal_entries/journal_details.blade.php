@extends('layouts.app')

@section('title', 'تفاصيل القيد')

@section('content')
<h1 class="mb-4">تفاصيل القيد {{ $journal->code }}</h1>

<div class="bg-white p-3 rounded-3 shadow-sm border-0 mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h5 class="fw-bold">
            {{ $journal->voucher_id ? $journal->voucher->type : 'قيد يومي' }} - بتاريخ {{ Carbon\Carbon::parse($journal->date)->format('Y/m/d') }}
        </h5>
        <div class="export-buttons d-flex gap-2 align-items-center">
            @if($journal->type == 'قيد يومي')
                <a href="{{ route('journal.duplicate', $journal) }}" target="_blank" class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تكرار القيد">
                    <i class="fa-solid fa-copy"></i>
                </a>
            @endif

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

    <div class="modal fade" id="deleteJournalModal" tabindex="-1" aria-labelledby="deleteJournalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white fw-bold"
                        id="deleteJournalModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
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

    <div class="row g-3 mt-4 pt-2 border-top">
        <div class="col-12">
            <div class="card border-primary border-2 p-2 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <i class="fa-solid fa-paperclip text-primary"></i>
                            </div>
                            <h6 class="card-title text-primary mb-0 fw-bold">المرفقات</h6>
                        </div>
                        <form action="{{ route('journal.add.attachment', $journal) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="file" name="attachment" class="form-control form-control-sm border-primary" style="width: auto;" required>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> إضافة
                            </button>
                        </form>
                    </div>
                    
                    @if($journal->attachments && $journal->attachments->count() > 0)
                        <div class="row g-3">
                            @foreach($journal->attachments as $attachment)
                                <div class="col-12 col-lg-6">
                                    <div class="alert alert-primary border-2 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                                    $iconClass = 'fas fa-file';
                                                    $iconColor = 'text-secondary';

                                                    switch (strtolower($extension)) {
                                                        case 'pdf':
                                                            $iconClass = 'fas fa-file-pdf';
                                                            $iconColor = 'text-danger';
                                                            break;
                                                        case 'doc':
                                                        case 'docx':
                                                            $iconClass = 'fas fa-file-word';
                                                            $iconColor = 'text-primary';
                                                            break;
                                                        case 'xls':
                                                        case 'xlsx':
                                                            $iconClass = 'fas fa-file-excel';
                                                            $iconColor = 'text-success';
                                                            break;
                                                        case 'jpg':
                                                        case 'jpeg':
                                                        case 'png':
                                                        case 'gif':
                                                            $iconClass = 'fas fa-file-image';
                                                            $iconColor = 'text-info';
                                                            break;
                                                        case 'txt':
                                                            $iconClass = 'fas fa-file-alt';
                                                            $iconColor = 'text-dark';
                                                            break;
                                                    }
                                                @endphp
                                                <i class="{{ $iconClass }} {{ $iconColor }}" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 alert-heading">{{ $attachment->file_name }}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    أرفق بواسطة {{ $attachment->made_by ? $attachment->made_by->name : 'غير محدد' }} في 
                                                    {{ $attachment->created_at->format('Y/m/d') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                download="{{ $attachment->file_name }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger"
                                                type="button" data-bs-toggle="modal" data-bs-target="#deleteAttachmentModal{{ $attachment->id }}"
                                                title="حذف المرفق">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Attachment Modal -->
                                <div class="modal fade" id="deleteAttachmentModal{{ $attachment->id }}" tabindex="-1"
                                    aria-labelledby="deleteAttachmentModalLabel{{ $attachment->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title text-white fw-bold" id="deleteAttachmentModalLabel{{ $attachment->id }}">
                                                    تأكيد حذف المرفق
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-dark">
                                                <p class="mb-3">هل أنت متأكد من حذف هذا المرفق؟</p>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>{{ $attachment->file_name }}</strong>
                                                    <br>
                                                    <small>لن تتمكن من استرداد هذا الملف بعد حذفه</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                                <form action="{{ route('contracts.delete.attachment', $attachment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger fw-bold order-1 order-sm-1">
                                                        حذف المرفق
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2" data-bs-dismiss="modal">
                                                    إلغاء
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fa-solid fa-paperclip fs-1 mb-2 text-muted"></i>
                            <p class="mb-0">لا توجد مرفقات لهذا القيد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

