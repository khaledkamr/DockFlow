@extends('layouts.app')

@section('title', 'تفاصيل العقد #' . $contract->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-primary">
                <i class="fas fa-file-contract me-2"></i>
                تفاصيل العقد #{{ $contract->id }}
            </h2>
            <div>
                <a href="{{ route('print.contract', $contract->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-print me-1"></i> طباعة
                </a>
                {{-- <a class="btn btn-success">
                    <i class="fas fa-download me-1"></i> تحميل PDF
                </a> --}}
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات العقد الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">رقم العقد</small>
                            <h6 class="fw-bold text-primary">#{{ $contract->id }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">تاريخ البداية</small>
                            <h6 class="fw-bold">{{ \Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">تاريخ الانتهاء</small>
                            <h6 class="fw-bold">{{ \Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <small class="text-muted">مدة العقد</small>
                        <h6 class="fw-bold text-dark">
                            {{ $months }} شهر و {{ $days }} يوم
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>
                            الطرف الأول
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">الاسم</label>
                                <div class="fw-bold">{{ $contract->company->name }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">السجل التجاري</label>
                                <div class="fw-bold">{{ $contract->company->CR }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم الضريبي</label>
                                <div class="fw-bold">{{ $contract->company->vatNumber }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">العنوان</label>
                                <div class="fw-bold">{{ $contract->company->national_address }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">رقم الهاتف</label>
                                <div class="fw-bold">{{ $contract->company->phone }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الإيميل</label>
                                <div class="fw-bold">{{ $contract->company->email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">ممثل الطرف الأول</label>
                                <div class="fw-bold">{{ $contract->company_representative }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الجنسية</label>
                                <div class="fw-bold">{{ $contract->company_representative_nationality }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم القومي</label>
                                <div class="fw-bold">{{ $contract->company_representative_NID }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            الطرف الثاني
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">الاسم</label>
                                <div class="fw-bold">{{ $contract->customer->name }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">السجل التجاري</label>
                                <div class="fw-bold">{{ $contract->customer->CR }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم الضريبي</label>
                                <div class="fw-bold">{{ $contract->customer->vatNumber }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">العنوان</label>
                                <div class="fw-bold">{{ $contract->customer->national_address }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">رقم الهاتف</label>
                                <div class="fw-bold">{{ $contract->customer->phone }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الإيميل</label>
                                <div class="fw-bold">{{ $contract->customer->email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">ممثل الطرف الثاني</label>
                                <div class="fw-bold">{{ $contract->customer_representative }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الجنسية</label>
                                <div class="fw-bold">{{ $contract->customer_representative_nationality }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم القومي</label>
                                <div class="fw-bold">{{ $contract->customer_representative_NID }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    الخدمات والأسعار
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($contract->services as $index => $service)
                        <div class="col-md-6">
                            <div class="service-card border border-primary rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="service-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <h6 class="mb-0 text-primary">الخدمة #{{ $index + 1 }}</h6>
                                </div>
                                <p class="text-muted mb-2">{{ $service->description }}</p>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">السعر</small>
                                        <div class="fw-bold text-success">
                                            {{ $service->pivot->price != 0 ? $service->pivot->price . ' ريال' : 'مجاناً' }} 
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">المدة</small>
                                        <div class="fw-bold text-dark">{{ $service->pivot->unit . ' ' . $service->pivot->unit_desc }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- File Attachments Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-paperclip me-2"></i>
                    مرفقات العقد
                </h5>
            </div>
            <div class="card-body">
                <!-- Upload Form -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form action="{{ route('contracts.add.attachment', $contract) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                            @csrf
                            <div class="flex-grow-1">
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,.xlsx,.xls">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i>
                                إرفاق ملف
                            </button>
                        </form>
                        <small class="text-muted mt-1 d-block">
                            يمكنك إرفاق الملفات التالية: PDF, Word, صور, Excel, نصوص
                        </small>
                    </div>
                </div>

                <!-- Attached Files List -->
                @if($contract->attachments && $contract->attachments->count() > 0)
                    <div class="row g-3">
                        @foreach($contract->attachments as $attachment)
                            <div class="col-md-6 col-lg-4">
                                <div class="attachment-card bg-light border rounded p-3 h-100 position-relative">
                                    <form action="{{ route('contracts.delete.attachment', $attachment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" 
                                            type="submit" title="حذف المرفق">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    </form>
                                    <div class="d-flex align-items-start">
                                        <div class="file-icon me-3">
                                            @php
                                                $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                                $iconClass = 'fas fa-file';
                                                $iconColor = 'text-secondary';
                                                
                                                switch(strtolower($extension)) {
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
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-truncate" title="{{ $attachment->file_name }}">
                                                {{ Str::limit($attachment->file_name, 25) }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $attachment->made_by->name ?? 'مستخدم محذوف' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $attachment->created_at->format('Y/m/d H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="fas fa-eye me-1"></i>
                                            عرض
                                        </a>
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                           download="{{ $attachment->file_name }}" 
                                           class="btn btn-sm btn-outline-success flex-grow-1">
                                            <i class="fas fa-download me-1"></i>
                                            تحميل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">لا توجد مرفقات لهذا العقد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.service-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.service-icon {
    font-size: 1.2rem;
}

.attachment-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background-color: #f8f9fa;
}

.attachment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background-color: #ffffff;
}

.file-icon {
    min-width: 50px;
    text-align: center;
}

@media (max-width: 768px) {
    .border-end {
        border-end: none !important;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    
    .border-end:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .attachment-card .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .attachment-card .d-flex.gap-2 .btn {
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection