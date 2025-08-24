@extends('layouts.admin')

@section('title', 'إضافة إتفاقية إستلام')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة إتفاقية إستلام</h2>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>حدث خطأ في العمليه الرجاء مراجعة البيانات!</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
    <form action="{{ route('policies.receive.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        <input type="hidden" name="type" value="إستلام">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم الشركة</label>
                <input type="text" name="company_name" class="form-control border-primary" value="{{ $company->name }}">
            </div>
            <div class="col">
                <label class="form-label">رقــم الشركة</label>
                <input type="text" class="form-control border-primary" id="company_id" name="company_id" value="{{ $company->id }}" readonly>
                @error('company_id')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">إســم العميــل</label>
                <select class="form-select border-primary" id="customer_name">
                    <option value="">اختر اسم العميل...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" 
                            data-contract="{{ $customer->contract ? $customer->contract->id : null }}"
                            data-containers="{{ $customer->containers }}" >
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="contract_id" name="contract_id">
            <div class="col">
                <label class="form-label">رقــم العميــل</label>
                <input type="text" class="form-control border-primary" id="customer_id" name="customer_id" value="" readonly>
                @error('customer_id')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم السائق</label>
                <input type="text" name="driver_name" class="form-control border-primary">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">رقــم هوية السائق</label>
                <input type="text" class="form-control border-primary" name="driver_NID">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">نوع السيارة</label>
                <input type="text" class="form-control border-primary" name="driver_car">
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">لوحة السيارة</label>
                <input type="text" class="form-control border-primary" name="car_code">
                @error('car_code')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        
        <!-- Container Selection Section -->
        <div class="row mb-4" id="container-section" >
            <div class="col-12">
                <h5 class="mb-3">اختيار الحاويات</h5>
                <div class="card border-primary bg-light p-3">
                    <div class="mb-3">
                        <label class="form-label">الحاويات المتاحة للإستلام</label>
                        <div id="containers-list" class="row">
                            <!-- Containers will be populated here dynamically -->
                        </div>
                    </div>
                    <div class="mt-3 d-flex flex-column">
                        <small class="text-danger">* لا يمكن للعميل سحب اخر حاوية اذا عليه فواتير لم يدفعها بعد</small>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary fw-bold">حفظ الإتفاقية</button>
    </form>
</div>

<script>
    $('#customer_name').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let containers = $(this).find(':selected').data('containers');
        
        // Show/hide container selection section
        if (containers && containers.length > 0) {
            displayContainers(containers);
            $('#containers-list').show();
        } else if (containers) {
            displayContainers(containers);
        } else {
            $('#containers-list').hide();
        }
    });

    function displayContainers(containers) {
        const containersList = $('#containers-list');
        containersList.empty();

        // Filter containers that are available for receiving (status: 'متوفر' or 'في الإنتظار')
        const availableContainers = containers.filter(container => 
            container.status === 'متوفر'
        );

        if (availableContainers.length === 0) {
            containersList.html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        لا توجد حاويات متاحة للإستلام لهذا العميل
                    </div>
                </div>
            `);
            return;
        }

        availableContainers.forEach(container => {
            const containerCard = `
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card container-card" data-container-id="${container.id}">
                        <div class="card-body p-3">
                            <div class="form-check">
                                <input class="form-check-input container-checkbox" type="checkbox" 
                                       name="selected_containers[]" value="${container.id}" 
                                       id="container_${container.id}">
                                <label class="form-check-label w-100" for="container_${container.id}">
                                    <div class="container-info">
                                        <div class="d-flex justify-content-between"> 
                                            <div class="fw-bold text-primary">${container.code}</div>
                                            <div class="text-primary">#${container.id}</div>
                                        </div>
                                        <div class="small text-muted">الحالة: ${container.status}</div>
                                        ${container.location ? `<div class="small text-muted">الموقع: ${container.location}</div>` : ''}
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            containersList.append(containerCard);
        });

        // Add click event to container cards
        $('.container-card').on('click', function() {
            const checkbox = $(this).find('.container-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
            updateContainerCardStyle($(this), checkbox.prop('checked'));
        });

        // Style checkboxes when clicked directly
        $('.container-checkbox').on('change', function() {
            updateContainerCardStyle($(this).closest('.container-card'), $(this).prop('checked'));
        });
    }

    function updateContainerCardStyle(card, isSelected) {
        if (isSelected) {
            card.addClass('border-primary bg-primary-subtle');
        } else {
            card.removeClass('border-primary bg-primary-subtle');
        }
    }
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

    .container-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid #dee2e6;
    }

    .container-card:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .container-card.border-primary {
        border-color: #0d6efd !important;
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .container-info {
        margin-left: 10px;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-label {
        cursor: pointer;
    }
</style>

@endsection