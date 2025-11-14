@extends('layouts.app')

@section('title', 'تفاصيل العقد')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-dark">
                <i class="fas fa-file-contract me-2"></i>
                تفاصيل العقد 
                @if($contract->end_date < \Carbon\Carbon::now())
                    <span class="badge status-danger ms-2">منتهي</span>
                @else
                    <span class="badge status-delivered ms-2">ساري</span>
                @endif
            </h2>
            <div>
                <a href="{{ route('print.contract', $contract->id) }}" class="btn btn-primary me-2" target="_blank">
                    <i class="fas fa-print me-1"></i> طباعة العقد
                </a>
            </div>
        </div>

        <!-- معلومات العقد الأساسية -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات العقد الأساسية
                </h5>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#editDurationModal">
                    تعديل المدة
                </button>
            </div>
            <div class="card-body">
                <div class="row">
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
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">مدة العقد</small>
                            <h6 class="fw-bold text-dark">
                                {{ $months }} شهر و {{ $days }} يوم
                            </h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <small class="text-muted">مدة السماح للدفع</small>
                            <h6 class="fw-bold">{{ $contract->payment_grace_period . ' ' . $contract->payment_grace_period_unit }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- تعديل مدة العقد Modal -->
        <div class="modal fade" id="editDurationModal" tabindex="-1" aria-labelledby="editDurationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editDurationModalLabel">تعديل مدة العقد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contracts.update', $contract) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">تاريخ البداية</label>
                                    <input type="date" class="form-control border-primary" name="start_date" value="{{ $contract->start_date }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">تاريخ الإنتهاء</label>
                                    <input type="date" class="form-control border-primary" name="end_date" value="{{ $contract->end_date }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">فترة السماح للدفع</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min="1" step="1" class="form-control border-primary" name="payment_grace_period" value="{{ $contract->payment_grace_period }}" required>
                                        <select name="payment_grace_period_unit" class="form-select border-primary" style="flex: 0 0 140px; width: 140px;" required>
                                            <option value="يوم" {{ $contract->payment_grace_period_unit == 'يوم' ? 'selected' : '' }}>يوم</option>
                                            <option value="أيام" {{ $contract->payment_grace_period_unit == 'ايام' ? 'selected' : '' }}>أيام</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- معلومات الطرفين -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>
                            الطرف الأول
                        </h5>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#editRepresentativeOneModal">
                            تعديل الممثل
                        </button>
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
                    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            الطرف الثاني
                        </h5>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#editRepresentativeTwoModal">
                            تعديل الممثل
                        </button>
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

        <!-- تعديل بيانات الطرف الاول Modal -->
        <div class="modal fade" id="editRepresentativeOneModal" tabindex="-1" aria-labelledby="editRepresentativeOneModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editRepresentativeOneModalLabel">تعديل بيانات ممثل الطرف الاول</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contracts.update', $contract) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="row mb-3">
                                <div class="col-12 mb-3">
                                    <label class="form-label">ممثل الطرف الاول</label>
                                    <input type="text" class="form-control border-primary" name="company_representative" value="{{ $contract->company_representative }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">الجنسية</label>
                                    <input type="text" class="form-control border-primary" name="company_representative_nationality" value="{{ $contract->company_representative_nationality }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">الرقم القومي</label>
                                    <input type="text" class="form-control border-primary" name="company_representative_NID" value="{{ $contract->company_representative_NID }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- تعديل بيانات الطرف الاول Modal -->
        <div class="modal fade" id="editRepresentativeTwoModal" tabindex="-1" aria-labelledby="editRepresentativeTwoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editRepresentativeTwoModalLabel">تعديل بيانات ممثل الطرف الاول</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contracts.update', $contract) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="row mb-3">
                                <div class="col-12 mb-3">
                                    <label class="form-label">ممثل الطرف الثاني</label>
                                    <input type="text" class="form-control border-primary" name="customer_representative" value="{{ $contract->customer_representative }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">الجنسية</label>
                                    <input type="text" class="form-control border-primary" name="customer_representative_nationality" value="{{ $contract->customer_representative_nationality }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">الرقم القومي</label>
                                    <input type="text" class="form-control border-primary" name="customer_representative_NID" value="{{ $contract->customer_representative_NID }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- الخدمات والأسعار -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    الخدمات والأسعار
                </h5>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#editServicesModal">
                    تعديل الخدمات
                </button>
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
                                    <h6 class="mb-0 text-primary">{{ $service->description }}</h6>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">السعر</small>
                                        <div class="fw-bold text-dark">
                                            {{ $service->pivot->price }} <i data-lucide="saudi-riyal"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">المدة/الكمية</small>
                                        <div class="fw-bold text-dark">{{ $service->pivot->unit . ' ' . $service->pivot->unit_desc }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- تعديل خدمات العقد Modal -->
        <div class="modal fade" id="editServicesModal" tabindex="-1" aria-labelledby="editServicesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editServicesModalLabel">تعديل خدمات العقد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contracts.update', $contract) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div id="services-container">
                                @foreach($contract->services as $index => $service)
                                    <div class="service-item border border-primary rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-primary">الخدمة {{ $index + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="services[{{ $index }}][service_id]" value="{{ $service->id }}">
                                        
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label">وصف الخدمة</label>
                                                <input type="text" class="form-control border-primary" name="services[{{ $index }}][description]" 
                                                       value="{{ $service->description }}" required>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">السعر</label>
                                                <input type="number" step="1" min="0" class="form-control border-primary" name="services[{{ $index }}][price]" 
                                                       value="{{ $service->pivot->price }}" required>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">المدة/الكمية</label>
                                                <input type="number" step="1" min="1" value="1" class="form-control border-primary" name="services[{{ $index }}][unit]" 
                                                       value="{{ $service->pivot->unit }}" required>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">وصف الوحدة</label>
                                                <input type="text" class="form-control border-primary" name="services[{{ $index }}][unit_desc]" 
                                                       value="{{ $service->pivot->unit_desc }}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-service">
                                    <i class="fas fa-plus me-1"></i>
                                    إضافة خدمة جديدة
                                </button>
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                let serviceIndex = {{ $contract->services->count() }};
                                
                                // Add new service
                                document.getElementById('add-service').addEventListener('click', function() {
                                    const container = document.getElementById('services-container');
                                    const newService = `
                                        <div class="service-item border border-primary rounded p-3 mb-3" data-index="${serviceIndex}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-primary">خدمة جديدة ${serviceIndex + 1}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="form-label">وصف الخدمة</label>
                                                    <select class="form-select border-primary" name="services[${serviceIndex}][service_id]" required>
                                                        <option value="">اختر الخدمة</option>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->description }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-2">
                                                    <label class="form-label">السعر</label>
                                                    <input type="number" step="1" min="0" class="form-control border-primary" 
                                                           name="services[${serviceIndex}][price]" required>
                                                </div>
                                                <div class="col-2">
                                                    <label class="form-label">المدة/الكمية</label>
                                                    <input type="number" step="1" min="1" value="1" class="form-control border-primary" 
                                                           name="services[${serviceIndex}][unit]" required>
                                                </div>
                                                <div class="col-2">
                                                    <label class="form-label">وصف الوحدة</label>
                                                    <input type="text" class="form-control border-primary" 
                                                           name="services[${serviceIndex}][unit_desc]" required>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', newService);
                                    serviceIndex++;
                                });
                                
                                // Remove service
                                document.addEventListener('click', function(e) {
                                    if (e.target.closest('.remove-service')) {
                                        const serviceItem = e.target.closest('.service-item');
                                        if (document.querySelectorAll('.service-item').length > 1) {
                                            serviceItem.remove();
                                        } else {
                                            alert('يجب أن يحتوي العقد على خدمة واحدة على الأقل');
                                        }
                                    }
                                });
                            });
                            </script>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- مرفقات العقد -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-paperclip me-2"></i>
                    مرفقات العقد
                </h5>
            </div>
            <div class="card-body">
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
                            يمكنك إرفاق الملفات التالية: PDF, صور
                        </small>
                    </div>
                </div>

                <!-- Attached Files List -->
                @if($contract->attachments && $contract->attachments->count() > 0)
                    <div class="row g-3">
                        @foreach($contract->attachments as $attachment)
                            <div class="col-md-6 col-lg-4">
                                <div class="attachment-card bg-light border rounded p-3 h-100 position-relative">
                                    <form action="{{ route('contracts.delete.attachment', $attachment) }}" method="POST" class="d-inline">
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