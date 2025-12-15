@extends('layouts.app')

@section('title', 'إضافة عقد')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <h2 class="mb-4">إضافة عقد جديد</h2>

    <div class="card border-0 shadow-sm bg-white p-3 p-md-4">
        <form action="{{ route('contracts.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

            <h5 class="mb-3">بيانات الشركة</h5>
            <div class="mb-4 bg-light p-3 rounded">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">إسم الشركة</label>
                        <input type="text" class="form-control border-primary" value="{{ $company->name }}" readonly>
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">الرقم الضريبي</label>
                        <input type="text" class="form-control border-primary" value="{{ $company->vatNumber }}"
                            readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">السجل التجاري</label>
                        <input type="text" class="form-control border-primary" value="{{ $company->CR }}" readonly>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">العنوان الوطني</label>
                        <input type="text" class="form-control border-primary" value="{{ $company->national_address }}"
                            readonly>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="company_representative" class="form-label">إسم الممثل</label>
                        <input type="text" class="form-control border-primary" name="company_representative"
                            value="{{ old('company_representative') }}">
                        @error('company_representative')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="company_representative_nationality" class="form-label">الجنسية</label>
                        <input type="text" class="form-control border-primary" name="company_representative_nationality"
                            value="{{ old('company_representative_nationality') }}">
                        @error('company_representative_nationality')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="company_representative_NID" class="form-label">الهوية الوطنية</label>
                        <input type="text" class="form-control border-primary" name="company_representative_NID"
                            value="{{ old('company_representative_NID') }}">
                        @error('company_representative_NID')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="company_representative_role" class="form-label">بصفته</label>
                        <input type="text" class="form-control border-primary" name="company_representative_role"
                            value="{{ old('company_representative_role') }}">
                        @error('company_representative_role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <h5 class="mb-3">بيانات العميل</h5>
            <div class="mb-4 bg-light p-3 rounded">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="mb-2">اسم الشركة</label>
                        <select id="customer_name" class="form-select border-primary" style="width:100%;">
                            <option value="">-- اختر الحساب --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-id="{{ $customer->id }}"
                                    data-cr="{{ $customer->CR }}" data-vatnumber="{{ $customer->vatNumber }}"
                                    data-add="{{ $customer->national_address }}">
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <input type="hidden" name="customer_id" id="customer_id" value="">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">الرقم الضريبي</label>
                        <input type="text" class="form-control border-primary" id="customer_vatNumber" value="">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">السجل التجاري</label>
                        <input type="text" class="form-control border-primary" id="customer_CR" value="">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">العنوان الوطني</label>
                        <input type="text" class="form-control border-primary" id="customer_national_address"
                            value="">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="customer_representative" class="form-label">إسم الممثل</label>
                        <input type="text" class="form-control border-primary" name="customer_representative"
                            value="{{ old('customer_representative') }}">
                        @error('customer_representative')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="customer_representative_nationality" class="form-label">الجنسية</label>
                        <input type="text" class="form-control border-primary"
                            name="customer_representative_nationality"
                            value="{{ old('customer_representative_nationality') }}">
                        @error('customer_representative_nationality')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="customer_representative_NID" class="form-label">الهوية الوطنية</label>
                        <input type="text" class="form-control border-primary" name="customer_representative_NID"
                            value="{{ old('customer_representative_NID') }}">
                        @error('customer_representative_NID')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="customer_representative_role" class="form-label">بصفته</label>
                        <input type="text" class="form-control border-primary" name="customer_representative_role"
                            value="{{ old('customer_representative_role') }}">
                        @error('customer_representative_role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <h5 class="mb-3">مدة العقد</h5>
            <div class="mb-4 bg-light p-3 rounded">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">تاريخ بداية العقد</label>
                        <input type="date" name="start_date" class="form-control border-primary"
                            value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                        @error('start_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">تاريخ نهاية العقد</label>
                        <input type="date" name="end_date" class="form-control border-primary"
                            value="{{ Carbon\Carbon::now()->addMonths(3)->format('Y-m-d') }}">
                        @error('end_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">فترة السماح للدفع</label>
                        <div class="input-group mb-3">
                            <input type="number" min="1" step="1" class="form-control border-primary"
                                name="payment_grace_period" value="{{ old('payment_grace_period', 7) }}">
                            <select name="payment_grace_period_unit" class="form-select border-primary"
                                style="max-width: 140px;">
                                <option value="">وحدة القياس</option>
                                <option value="يوم" {{ old('payment_grace_period_unit') == 'يوم' ? 'selected' : '' }}>
                                    يوم</option>
                                <option value="أيام" {{ old('payment_grace_period_unit') == 'أيام' ? 'selected' : '' }}>
                                    أيام</option>
                            </select>
                        </div>
                        @error('payment_grace_period')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('payment_grace_period_unit')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                <h5 class="mb-0">الخدمات والأسعار</h5>
                <button type="button" class="btn btn-primary" id="add-service-btn">
                    <i class="fas fa-plus me-2"></i>إضافة خدمة
                </button>
            </div>

            <div id="services-container">

            </div>

            <!-- Service Selection Modal -->
            <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white fw-bold" id="serviceModalLabel">اختيار الخدمة</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="service-select" class="form-label">الخدمة المطلوبة</label>
                                <select id="service-select" class="form-select border-primary">
                                    <option value="">اختر الخدمة...</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            data-description="{{ $service->description }}">
                                            {{ $service->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="service-price" class="form-label">السعر</label>
                                    <input type="number" min="1" step="1"
                                        class="form-control border-primary" id="service-price" placeholder="0.00">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="service-unit" class="form-label">الكمية</label>
                                    <input type="number" class="form-control border-primary" id="service-unit"
                                        placeholder="1" value="1">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="service-unit-desc" class="form-label">وحدة القياس</label>
                                    <input type="text" class="form-control border-primary" id="service-unit-desc"
                                        placeholder="شهر، يوم، حاوية...">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
                            <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="button" class="btn btn-primary fw-bold order-1 order-sm-2"
                                id="confirm-service">إضافة الخدمة</button>
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

        $('#service-select').select2({
            dropdownParent: $('#serviceModal'),
            placeholder: "اختر الخدمة...",
            width: '100%'
        });

        $('#customer_name').select2({
            placeholder: "ابحث عن الشركة...",
            allowClear: true
        });

        $('#customer_name').on('change', function() {
            let cr = $(this).find(':selected').data('cr');
            $('#customer_CR').val(cr || '');
            let vatNumber = $(this).find(':selected').data('vatnumber');
            $('#customer_vatNumber').val(vatNumber || '');
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

            if (!serviceId || !price || !unit || !unitDesc) {
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
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                    <h6 class="mb-0 text-primary">خدمة #${serviceCounter}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">الوصف</label>
                        <input type="text" class="form-control border-primary" value="${description}" required readonly>
                        <input type="hidden" name="services[${serviceId}][service_id]" value="${serviceId}">
                    </div>
                    <div class="col-12 col-sm-4 col-md-2">
                        <label class="form-label">السعر</label>
                        <input type="number" step="0.01" class="form-control border-primary" name="services[${serviceId}][price]" value="${price}" required>
                    </div>
                    <div class="col-12 col-sm-4 col-md-2">
                        <label class="form-label">الكمية</label>
                        <input type="number" class="form-control border-primary" name="services[${serviceId}][unit]" value="${unit}" required>
                    </div>
                    <div class="col-12 col-sm-4 col-md-2">
                        <label class="form-label">الوحدة</label>
                        <input type="text" class="form-control border-primary" name="services[${serviceId}][unit_desc]" value="${unitDesc}" required>
                    </div>
                </div>
            </div>
        `;

            $('#services-container').append(serviceHtml);
        }


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
