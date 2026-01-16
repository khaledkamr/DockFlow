@extends('layouts.app')

@section('title', 'إضافة بوليصة تخزين')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة بوليصة تخزين</h2>

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm mb-5">
    <form action="{{ route('policies.storage.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="type" value="تخزين">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <label class="form-label">إســم العميــل <span class="text-danger">*</span></label>
                <select class="form-select border-primary" id="customer_name">
                    <option value="">اختر اسم العميل...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-id="{{ $customer->id }}"  data-account="{{ $customer->account ? $customer->account->code : null }}"
                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->account ? $customer->account->code : '' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="contract_id" name="contract_id">
            <input type="hidden" id="customer_id" name="customer_id">
            
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">إســم السائق <span class="text-danger">*</span></label>
                <input type="text" name="driver_name" class="form-control border-primary" value="{{  old('driver_name') }}">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">رقــم هوية السائق <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="driver_NID" value="{{ old('driver_NID') }}">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">رقم السائق</label>
                <input type="text" class="form-control border-primary" name="driver_number" value="{{ old('driver_number') }}">
                @error('driver_number')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">نوع السيارة <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="driver_car" value="{{ old('driver_car') }}">
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">لوحة السيارة <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="car_code" value="{{ old('car_code') }}">
                @error('car_code')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">الرقم المرجعي</label>
                <input type="text" class="form-control border-primary" id="reference_number" name="reference_number" value="{{ old('reference_number') }}">
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <label class="form-label">البيان الضريبي</label>
                <input type="text" name="tax_statement" class="form-control border-primary" value="{{ old('tax_statement') }}">
                @error('tax_statement')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>

        <div class="row g-3 mb-3">
            <h5 class="mb-2">سعر التخزين</h5>
            <div class="col-4 col-md-4">
                <label class="form-label">سعر التخزين <span class="text-danger">*</span></label>
                <input type="number" id="storage_price" name="storage_price" class="form-control border-primary" min="0" step="any" required>
                @error('storage_price')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-4 col-md-4">
                <label class="form-label">مدة التخزين <span class="text-danger">*</span></label>
                <input type="number" id="storage_duration" name="storage_duration" class="form-control border-primary" min="0" step="any">
                @error('storage_duration')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-4 col-md-4">
                <label class="form-label d-none d-sm-block">غرامة التأخير (لليوم) <span class="text-danger">*</span></label>
                <label class="form-label d-block d-sm-none">غرامة التأخير<span class="text-danger">*</span></label>
                <input type="number" id="late_fee" name="late_fee" class="form-control border-primary" min="0" step="any" required>
                @error('late_fee')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">بيانات الحاويات</h5>
                {{-- <button type="button" class="btn btn-primary btn-sm" id="addContainerBtn">
                    <i class="fas fa-plus me-1"></i> إضافة حاوية جديدة
                </button> --}}
            </div>
            
            <div id="containersSection">
                <div class="container-row border border-primary rounded p-3 mb-3" data-row="0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary">الحاوية #<span class="container-number">1</span></h6>
                        <button type="button" class="btn btn-danger btn-sm remove-container" style="display: none;">
                            <i class="fas fa-trash-can"></i> 
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label">نوع الإضافة <span class="text-danger">*</span></label>
                            <select class="form-select border-primary container-type-select" data-row="0">
                                <option value="new" selected>حاوية جديدة</option>
                                <option value="existing">حاوية موجودة</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label">رقم الحاويــة <span class="text-danger">*</span></label>
                            <input type="hidden" name="containers[0][id]">
                            
                            <!-- New Container Input (Default) -->
                            <input type="text" class="form-control border-primary container-code-input" name="containers[0][code]" placeholder="أدخل رقم الحاوية" required>
                            
                            <!-- Existing Container Select (Hidden by default) -->
                            <select name="containers[0][code_select]" class="form-select border-primary container-code-select" style="display: none;">
                                <option value="">اختر رقم الحاوية...</option>
                                @foreach ($containers as $container)
                                    <option value="{{ $container->code }}" data-id="{{ $container->id }}">
                                        {{ $container->code }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3">
                            <label class="form-label">فئة الحاويــة <span class="text-danger">*</span></label>
                            <select class="form-select border-primary" name="containers[0][container_type_id]" id="container_type_id" required>
                                <option value="">اختر فئة الحاوية...</option>
                                @foreach ($containerTypes as $type)
                                    <option value="{{ $type->id }}" data-type="{{ $type->name }}">
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-1">
                            <label class="form-label">الموقــع</label>
                            <input type="text" class="form-control border-primary" name="containers[0][location]">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label">ملاحظات</label>
                            <input type="text" class="form-control border-primary" name="containers[0][notes]">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary fw-bold">
                <span class="d-inline">حفظ البوليصة</span>
            </button>
            <span class="text-muted">إجمالي الحاويات: <span id="totalContainers">1</span></span>
        </div>
    </form>
</div>

<script>
    // Initialize select2 for existing container selects
    function initializeSelect2ForContainer(selectElement) {
        $(selectElement).select2({
            placeholder: "ابحث عن رقم الحاوية...",
            allowClear: true
        });

        $(selectElement).on('change', function () {
            let id = $(this).find(':selected').data('id');
            $(this).closest('.container-row').find('input[name^="containers"][name$="[id]"]').val(id || '');
        });
    }

    // Handle container type toggle (new/existing)
    $(document).on('change', '.container-type-select', function() {
        const row = $(this).closest('.container-row');
        const selectedType = $(this).val();
        const codeInput = row.find('.container-code-input');
        const codeSelect = row.find('.container-code-select');
        const hiddenId = row.find('input[name^="containers"][name$="[id]"]');

        console.log('Selected Type:', selectedType);
        
        if (selectedType === 'new') {
            // Show text input for new container
            codeInput.css('display', 'block').prop('required', true).prop('disabled', false);
            codeSelect.css('display', 'none').prop('required', false).prop('disabled', true);
            
            // Destroy select2 instance if exists
            if (codeSelect.hasClass('select2-hidden-accessible')) {
                codeSelect.select2('destroy');
            }
            hiddenId.val(''); // Clear container ID
            
            // Update name attribute
            const rowIndex = row.attr('data-row');
            codeInput.attr('name', `containers[${rowIndex}][code]`);
            codeSelect.attr('name', `containers[${rowIndex}][code_select]`);
        } else {
            // Show select for existing container
            codeInput.css('display', 'none').prop('required', false).prop('disabled', true);
            codeSelect.css('display', 'block').prop('required', true).prop('disabled', false);
            
            // Update name attribute
            const rowIndex = row.attr('data-row');
            codeInput.attr('name', `containers[${rowIndex}][code_input]`);
            codeSelect.attr('name', `containers[${rowIndex}][code]`);
            
            // Initialize select2 if not already initialized
            if (!codeSelect.hasClass('select2-hidden-accessible')) {
                initializeSelect2ForContainer(codeSelect);
            }
        }
    });

    $('#customer_name').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');

        let contract_storage_price = 0;
        let contract_storage_duration = 0;
        let contract_late_fee = 0;

        let storage_price_val = $('#storage_price').val();
        let storage_duration_val = $('#storage_duration').val();
        let late_fee_val = $('#late_fee').val();

        if (id) {
            fetch(`/customers/${id}/contract`)
                .then(res => res.json())
                .then(data => {
                    $('#storage_price').val(data.storage_price || storage_price_val);
                    $('#storage_duration').val(data.storage_duration || storage_duration_val);
                    $('#late_fee').val(data.late_fee || late_fee_val);
                })
                .catch(err => console.error(err));
        }
    });

    $('#container_type_id').select2({
        placeholder: "اختر فئة الحاوية...",
        allowClear: true
    });

    $('#container_type_id').on('change', function () {
        let type_name = $(this).find(':selected').data('type');
        let customer_id = $('#customer_id').val();
        let late_fee_val = $('#late_fee').val();

        if(customer_id && type_name) {
            fetch(`/customers/${customer_id}/contract`)
                .then(res => res.json())
                .then(data => {
                    if(type_name == 'حاوية 20') {
                        $('#late_fee').val(data.late_fee_20ft || late_fee_val);
                    } else if(type_name == 'حاوية 40') {
                        $('#late_fee').val(data.late_fee_40ft || late_fee_val);
                    }
                })
                .catch(err => console.error(err));
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        let containerCount = 1;
        const addBtn = document.getElementById('addContainerBtn');
        const containersSection = document.getElementById('containersSection');
        const totalContainersSpan = document.getElementById('totalContainers');

        // Add new container row
        addBtn.addEventListener('click', function() {
            const newRow = createContainerRow(containerCount);
            containersSection.appendChild(newRow);
            containerCount++;
            updateContainerNumbers();
            updateRemoveButtons();
            updateTotalCount();
        });

        // Remove container row
        containersSection.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-container') || e.target.closest('.remove-container')) {
                const row = e.target.closest('.container-row');
                row.remove();
                updateContainerNumbers();
                updateRemoveButtons();
                updateTotalCount();
            }
        });

        function createContainerRow(index) {
            const template = document.querySelector('.container-row');
            const newRow = template.cloneNode(true);
            
            // Update data-row attribute
            newRow.setAttribute('data-row', index);
            
            // Clear input values
            newRow.querySelectorAll('input').forEach(input => {
                input.value = '';
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            // Update select names and data-row attributes
            newRow.querySelectorAll('select').forEach(select => {
                if (select.classList.contains('container-type-select')) {
                    select.value = 'new'; // Reset to default (new container)
                    select.setAttribute('data-row', index);
                } else {
                    select.selectedIndex = 0;
                }
                select.name = select.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            // Reset to new container mode (default)
            const codeInput = newRow.querySelector('.container-code-input');
            const codeSelect = newRow.querySelector('.container-code-select');
            
            if (codeInput && codeSelect) {
                codeInput.style.display = 'block';
                codeInput.required = true;
                codeInput.disabled = false;
                codeInput.setAttribute('name', `containers[${index}][code]`);
                
                codeSelect.style.display = 'none';
                codeSelect.required = false;
                codeSelect.disabled = true;
                codeSelect.setAttribute('name', `containers[${index}][code_select]`);
                
                // Destroy select2 instance if exists
                if ($(codeSelect).hasClass('select2-hidden-accessible')) {
                    $(codeSelect).select2('destroy');
                }
            }
            
            // Show remove button
            const removeBtn = newRow.querySelector('.remove-container');
            removeBtn.style.display = 'inline-block';
            
            return newRow;
        }

        function updateContainerNumbers() {
            const rows = containersSection.querySelectorAll('.container-row');
            rows.forEach((row, index) => {
                const numberSpan = row.querySelector('.container-number');
                numberSpan.textContent = index + 1;
                
                // Update input and select names
                row.querySelectorAll('input, select').forEach(element => {
                    const name = element.name;
                    element.name = name.replace(/\[\d+\]/, `[${index}]`);
                });
            });
        }

        function updateRemoveButtons() {
            const rows = containersSection.querySelectorAll('.container-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-container');
                if (rows.length > 1) {
                    removeBtn.style.display = 'inline-block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }

        function updateTotalCount() {
            const count = containersSection.querySelectorAll('.container-row').length;
            totalContainersSpan.textContent = count;
        }

        // Form validation
        document.getElementById('containerForm').addEventListener('submit', function(e) {
            let isValid = true;
            const rows = containersSection.querySelectorAll('.container-row');
            
            rows.forEach(row => {
                const inputs = row.querySelectorAll('input[required], select[required]');
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('يرجى ملء جميع الحقول المطلوبة');
            }
        });

        // Remove validation errors on input
        containersSection.addEventListener('input', function(e) {
            if (e.target.value.trim()) {
                e.target.classList.remove('is-invalid');
            }
        });

        containersSection.addEventListener('change', function(e) {
            if (e.target.value.trim()) {
                e.target.classList.remove('is-invalid');
            }
        });
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
</style>

@endsection