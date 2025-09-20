@extends('layouts.admin')

@section('title', 'إضافة عقد')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة عقد جديد</h2>

@if (session('success'))
    @push('scripts')
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endpush
@endif

@if (session('errors'))
    @push('scripts')
        <script>
            showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
        </script>
    @endpush
@endif

<div class="card border-0 shadow-sm bg-white p-4">
    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf
        <input type="hidden" name="start_date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="end_date" value="{{ Carbon\Carbon::now()->addMonths(3) }}">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        
        <h5 class="mb-3">بيانات الشركة</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">إسم الشركة</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->name }}" readonly>
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->CR }}" readonly>
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->TIN }}" readonly>
                </div>
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->national_address }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="company_representative" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="company_representative" value="{{ old('company_representative') }}">
                </div>
                <div class="col">
                    <label for="company_representative_nationality" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="company_representative_nationality" value="{{ old('company_representative_nationality') }}">
                </div>
                <div class="col">
                    <label for="company_representative_NID" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="company_representative_NID" value="{{ old('company_representative_NID') }}">
                </div>
                <div class="col">
                    <label for="company_representative_role" class="form-label">بصفته</label>
                    <input type="text" class="form-control border-primary" name="company_representative_role" value="{{ old('company_representative_role') }}">
                </div>
            </div>
        </div>
        
        <h5 class="mb-3">بيانات العميل</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label class="mb-2">اسم الشركة</label>
                    <select id="customer_name" class="form-select border-primary" style="width:100%;">
                        <option value="">-- اختر الحساب --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" data-cr="{{ $customer->CR }}" data-tin="{{ $customer->TIN }}" data-add="{{ $customer->national_address }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="customer_id" id="customer_id" value="">
                <div class="col">
                    <label class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" id="customer_CR" value="">
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" id="customer_TIN" value="">
                </div>
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" id="customer_national_address" value="">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="customer_representative" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="customer_representative" value="{{ old('customer_representative') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_nationality" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_nationality" value="{{ old('customer_representative_nationality') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_NID" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_NID" value="{{ old('customer_representative_NID') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_role" class="form-label">بصفته</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_role" value="{{ old('customer_representative_role') }}">
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">الخدمات والأسعار</h5>
            <button type="button" class="btn btn-primary" id="add-service-btn">
                <i class="fas fa-plus me-2"></i>إضافة خدمة
            </button>
        </div>
        
        <div id="services-container">
            @foreach ($services as $index => $service)
                <div class="mb-4 bg-light p-3 rounded service-item" data-service-id="{{ $service->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-primary">خدمة #{{ $index + 1 }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">الوصف</label>
                            <input type="text" class="form-control border-primary" value="{{ $service->description }}" readonly>
                            <input type="hidden" name="services[{{ $service->id }}][service_id]" value="{{ $service->id }}">
                        </div>
                        <div class="col-2">
                            <label class="form-label">السعر</label>
                            <input type="number" step="0.01" class="form-control border-primary" name="services[{{ $service->id }}][price]">
                        </div>
                        <div class="col-2">
                            <label class="form-label">الكمية</label>
                            <input type="number" class="form-control border-primary" name="services[{{ $service->id }}][unit]">
                        </div>
                        <div class="col-2">
                            <label class="form-label">الوحدة</label>
                            <input type="text" class="form-control border-primary" name="services[{{ $service->id }}][unit_desc]">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Service Selection Modal -->
        <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="serviceModalLabel">اختيار الخدمة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="service-select" class="form-label">الخدمة المطلوبة</label>
                            <select id="service-select" class="form-select border-primary">
                                <option value="">اختر الخدمة...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-description="{{ $service->description }}">
                                        {{ $service->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="service-price" class="form-label">السعر</label>
                                <input type="number" step="0.01" class="form-control border-primary" id="service-price" placeholder="0.00">
                            </div>
                            <div class="col">
                                <label for="service-unit" class="form-label">الكمية</label>
                                <input type="number" class="form-control border-primary" id="service-unit" placeholder="1" value="1">
                            </div>
                            <div class="col-md-5">
                                <label for="service-unit-desc" class="form-label">وحدة القياس</label>
                                <input type="text" class="form-control border-primary" id="service-unit-desc" placeholder="شهر، يوم، حاوية...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-primary fw-bold" id="confirm-service">إضافة الخدمة</button>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary fw-bold" id="submit-btn">
            حفظ العقد
        </button>
    </form>
</div>

<script>
    let serviceCounter = 0;

    $('#customer_name').select2({
        placeholder: "ابحث عن الشركة...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let cr = $(this).find(':selected').data('cr');
        $('#customer_CR').val(cr || '');
        let tin = $(this).find(':selected').data('tin');
        $('#customer_TIN').val(tin || '');
        let add = $(this).find(':selected').data('add');
        $('#customer_national_address').val(add || '');
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
    });

    // Add service button click
    $('#add-service-btn').on('click', function() {
        $('#service-select').val('');
        $('#service-price').val('');
        $('#service-unit').val('1');
        $('#service-unit-desc').val('');
        $('#serviceModal').modal('show');
    });

    // Confirm service addition
    $('#confirm-service').on('click', function() {
        const serviceId = $('#service-select').val();
        const serviceDescription = $('#service-select option:selected').data('description');
        const price = $('#service-price').val();
        const unit = $('#service-unit').val();
        const unitDesc = $('#service-unit-desc').val();

        if (!serviceId || !price || !unit) {
            showToast('الرجاء ملء جميع الحقول المطلوبة', 'danger');
            return;
        }

        // Check if service already exists
        if ($(`input[name="services[${serviceId}][service_id]"]`).length > 0) {
            showToast('هذه الخدمة مضافة مسبقاً', 'danger');
            return;
        }

        addServiceToContract(serviceId, serviceDescription, price, unit, unitDesc);
        $('#serviceModal').modal('hide');
        checkSubmitButton();
    });

    function addServiceToContract(serviceId, description, price, unit, unitDesc) {
        serviceCounter++;
        
        const serviceHtml = `
            <div class="mb-4 bg-light p-3 rounded service-item" data-service-id="${serviceId}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">خدمة #${serviceCounter}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">الوصف</label>
                        <input type="text" class="form-control border-primary" value="${description}" readonly>
                        <input type="hidden" name="services[${serviceId}][service_id]" value="${serviceId}">
                    </div>
                    <div class="col-2">
                        <label class="form-label">السعر</label>
                        <input type="number" step="0.01" class="form-control border-primary" name="services[${serviceId}][price]" value="${price}" readonly>
                    </div>
                    <div class="col-2">
                        <label class="form-label">الكمية</label>
                        <input type="number" class="form-control border-primary" name="services[${serviceId}][unit]" value="${unit}" readonly>
                    </div>
                    <div class="col-2">
                        <label class="form-label">الوحدة</label>
                        <input type="text" class="form-control border-primary" name="services[${serviceId}][unit_desc]" value="${unitDesc}" readonly>
                    </div>
                </div>
            </div>
        `;
        
        $('#services-container').append(serviceHtml);
    }

    // Remove service
    $(document).on('click', '.remove-service', function() {
        $(this).closest('.service-item').remove();
        checkSubmitButton();
        updateServiceNumbers();
    });

    function updateServiceNumbers() {
        $('#services-container .service-item').each(function(index) {
            $(this).find('h6').text(`خدمة #${index + 1}`);
        });
        serviceCounter = $('#services-container .service-item').length;
    }

    function checkSubmitButton() {
        const hasServices = $('#services-container .service-item').length > 0;
        const hasCustomer = $('#customer_id').val() !== '';
        
        $('#submit-btn').prop('disabled', !hasServices || !hasCustomer);
        
        if (!hasServices) {
            $('#submit-btn').text('الرجاء إضافة خدمة واحدة على الأقل');
        } else if (!hasCustomer) {
            $('#submit-btn').text('الرجاء اختيار العميل');
        } else {
            $('#submit-btn').text('حفظ العقد');
        }
    }

    // Check on customer selection
    $('#customer_name').on('change', function() {
        checkSubmitButton();
    });

    // Initial check
    $(document).ready(function() {
        checkSubmitButton();
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 38px;       
        border-radius: 8px; 
        border: 1px solid #0d6efd;
        padding: 5px;
    }
    .select2-container .select2-selection__rendered {
        line-height: 30px; 
    }
    
    .service-item {
        border-left: 4px solid #0d6efd;
        border-right: 4px solid #0d6efd;
    }
    
    .remove-service:hover {
        background-color: #dc3545;
        color: white;
    }
    
    #submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .modal-body .row {
        margin-top: 15px;
    }
</style>

@endsection