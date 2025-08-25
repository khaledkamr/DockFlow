@extends('layouts.admin')

@section('title', 'إضافة حاوية جديدة')

@section('content')
<h2 class="mb-4">إضافة حاوية جديدة</h2>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm bg-white p-4">
    <form action="" method="GET" class="row mb-3">
        <div class="col-4">
            <h5 class="mb-3">بيانات العميــل</h5>
            <select class="form-select border-primary" id="user_id" name="customer_id" onchange="this.form.submit()">
                <option value="">اختر اسم العميل...</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request()->query('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <form action="{{ route('yard.containers.store') }}" method="POST" id="containerForm">
        @csrf
        <div class="row mb-4 bg-light p-3 rounded">
            <div class="col">
                <label for="customer_id" class="form-label">رقــم العميــل</label>
                <input type="text" class="form-control border-primary" id="customer_id" name="customer_id" value="{{ $client['id'] }}" readonly>
            </div>
            <div class="col">
                <label for="CR" class="form-label">رقم السجل الضريبي</label>
                <input type="text" class="form-control border-primary" id="CR" name="CR" value="{{ $client['CR'] }}" readonly>
            </div>
            <div class="col">
                <label for="phone" class="form-label">رقم الهاتــف</label>
                <input type="text" class="form-control border-primary" id="phone" name="phone" value="{{ $client['phone'] }}" readonly>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">بيانات الحاويات</h5>
                <button type="button" class="btn btn-primary btn-sm" id="addContainerBtn">
                    <i class="fas fa-plus me-1"></i> إضافة حاوية جديدة
                </button>
            </div>
            
            <div id="containersSection">
                <div class="container-row border rounded p-3 mb-3" data-row="0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary">الحاوية #<span class="container-number">1</span></h6>
                        <button type="button" class="btn btn-danger btn-sm remove-container" style="display: none;">
                            <i class="fas fa-trash-can"></i> 
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">كــود الحاويــة</label>
                            <input type="text" class="form-control border-primary" name="containers[0][code]" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">فئة الحاويــة</label>
                            <select class="form-select border-primary" name="containers[0][container_type_id]" required>
                                <option value="">اختر فئة الحاوية...</option>
                                @foreach ($containerTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالــة</label>
                            <select class="form-select border-primary" name="containers[0][status]" required>
                                <option value="في الإنتظار">في الإنتظار</option>
                                <option value="غير متوفر">غير متوفر</option>
                                <option value="متوفر">متوفر</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الموقــع</label>
                            <input type="text" class="form-control border-primary" name="containers[0][location]">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="submit" class="btn btn-primary fw-bold">
                حفظ جميع الحاويات
            </button>
            <span class="text-muted">إجمالي الحاويات: <span id="totalContainers">1</span></span>
        </div>
    </form>
</div>

<script>
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
.container-row {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.container-row:hover {
    background-color: #e9ecef;
}

.container-number {
    font-weight: bold;
}

.remove-container {
    transition: all 0.2s ease;
}

.remove-container:hover {
    transform: scale(1.05);
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
}
</style>

@endsection