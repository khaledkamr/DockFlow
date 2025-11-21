@extends('layouts.app')

@section('title', 'إضافة بوليصة خدمات')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة بوليصة خدمات</h2>

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm mb-5">
    <form action="{{ route('policies.services.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="type" value="خدمات">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <input type="hidden" name="company_id" value="{{ $company->id }}">
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <label class="form-label">إســم العميــل</label>
                <select class="form-select border-primary" id="customer_name" name="customer_name">
                    <option value="">اختر اسم العميل...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" data-account="{{ $customer->account ? $customer->account->code : null }}"
                            data-contract="{{ $customer->contract ? $customer->contract->id : null }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="contract_id" name="contract_id">
            <input type="hidden" id="customer_id" name="customer_id">
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">رقــم العميــل</label>
                <input type="text" class="form-control border-primary" id="customer_account" name="customer_account" value="" readonly>
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">إســم السائق</label>
                <input type="text" name="driver_name" class="form-control border-primary">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-md-6 col-lg-3">
                <label class="form-label">رقــم هوية السائق</label>
                <input type="text" class="form-control border-primary" name="driver_NID">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-sm-6 col-lg-3">
                <label class="form-label">رقــم هاتف السائق</label>
                <input type="text" class="form-control border-primary" name="driver_phone">
                @error('driver_phone')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-sm-6 col-lg-3">
                <label class="form-label">نوع السيارة</label>
                <input type="text" class="form-control border-primary" name="driver_car">
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6 col-sm-6 col-lg-3">
                <label class="form-label">لوحة السيارة</label>
                <input type="text" class="form-control border-primary" name="car_code">
                @error('car_code')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-12 col-sm-6 col-sm-6 col-lg-3">
                <label class="form-label">البيان الضريبي</label>
                <input type="text" name="tax_statement" class="form-control border-primary">
                @error('tax_statement')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                <h5 class="mb-0">بيانات الحاويات</h5>
                <button type="button" class="btn btn-primary btn-sm" id="addContainerBtn">
                    <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">إضافة حاوية جديدة</span><span class="d-inline d-sm-none">إضافة حاوية</span>
                </button>
            </div>
            
            <div id="containersSection">
                <div class="container-row border border-primary rounded p-3 mb-3" data-row="0">
                    <div class="d-flex flex-row justify-content-between align-items-center gap-2 mb-2">
                        <h6 class="mb-0 text-primary">الحاوية #<span class="container-number">1</span></h6>
                        <button type="button" class="btn btn-danger btn-sm remove-container" style="display: none;">
                            <i class="fas fa-trash-can"></i><span class="d-none d-sm-inline ms-1">حذف</span>
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label">رقم الحاويــة</label>
                            <input type="text" class="form-control border-primary" name="containers[0][code]" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label">فئة الحاويــة</label>
                            <select class="form-select border-primary" name="containers[0][container_type_id]" required>
                                <option value="">اختر فئة الحاوية...</option>
                                @foreach ($containerTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label">الخدمة</label>
                            <select class="form-select border-primary" name="containers[0][service_id]" required>
                                <option value="">اختر الخدمة...</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label">السعر</label>
                            <input type="text" class="form-control border-primary" name="containers[0][price]">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary fw-bold">
                حفظ البوليصة
            </button>
            <span class="text-muted">إجمالي الحاويات: <span id="totalContainers">1</span></span>
        </div>
    </form>
</div>

<script>
    let currentContractServices = {};

    $('#customer_name').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true,
        tags: true,
    });

    $('#customer_name').on('change', function () {
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let account = $(this).find(':selected').data('account');
        $('#customer_account').val(account || '');

        currentContractServices = {};

        if (id) {
            fetch(`/customers/${id}/contract`)
                .then(res => res.json())
                .then(data => {
                    if (data.contract && data.contract.services.length > 0) {
                        data.contract.services.forEach(service => {
                            currentContractServices[service.id] = service.pivot.price;
                        });
                        console.log("خدمات العقد:", currentContractServices);
                    } else {
                        console.log("العميل دا ملوش عقد أو العقد مفيهوش خدمات");
                    }
                })
                .catch(err => console.error(err));
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name*="[service_id]"]')) {
            const serviceId = e.target.value;
            const row = e.target.closest('.container-row');
            const priceInput = row.querySelector('input[name*="[price]"]');
            
            if (currentContractServices[serviceId]) {
                priceInput.value = currentContractServices[serviceId];
                priceInput.readOnly = true;
            } else {
                priceInput.value = '';
                priceInput.readOnly = false;
            }
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
            
            // Update select names
            newRow.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
                select.name = select.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
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