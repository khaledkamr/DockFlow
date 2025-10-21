@extends('layouts.app')

@section('title', 'إضافة إشعار نقل')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة إشعار نقل</h2>

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
    <form action="{{ route('transportOrders.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <input type="hidden" name="customer_id" id="customer_id">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">رقم المعاملة</label>
                <select class="form-select border-primary" id="transaction_id" name="transaction_id">
                    <option value="">اختر رقم المعاملة...</option>
                    @foreach ($transactions as $transaction)
                        <option value="{{ $transaction->id }}" data-containers="{{ $transaction->containers }}" data-customer="{{ $transaction->customer->id }}" 
                            data-contract="{{ $transaction->contract_id }}" data-invoices='@json($transaction->invoices)'
                            {{ old('transaction_id') == $transaction->id ? 'selected' : '' }}>
                            {{ $transaction->code }} - {{ $transaction->customer->name }}
                        </option>
                    @endforeach
                </select>
                @error('transaction_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">من</label>
                <input type="text" class="form-control border-primary" id="from" name="from" value="{{ old('from') }}">
                @error('from')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">إلى</label>
                <input type="text" class="form-control border-primary" id="to" name="to" value="{{ old('to') }}">
                @error('to')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">مدة النقل (بالأيام)</label>
                <input type="number" class="form-control border-primary" id="duration" name="duration" value="{{ old('duration') }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم السائق</label>
                <select name="driver_name" id="driver_name" class="form-select border-primary">
                    <option value="">اختر السائق...</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->name }}" data-nid="{{ $driver->NID }}" data-id="{{ $driver->id }}"
                            data-vehicle-plate="{{ $driver->vehicle ? $driver->vehicle->plate_number : '' }}"
                            data-vehicle-type="{{ $driver->vehicle ? $driver->vehicle->type : '' }}"
                            {{ old('driver_name') == $driver->name ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="driver_id" id="driver_id" value="{{ old('driver_id') }}">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">هوية السائق</label>
                <input type="text" class="form-control border-primary" name="driver_NID" id="driver_NID" value="{{ old('driver_NID') }}">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">لوحة السيارة</label>
                <input type="text" class="form-control border-primary" name="plate_number" id="plate_number" value="{{ old('plate_number') }}">
                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id') }}">
                @error('plate_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">نوع السيارة</label>
                <input type="text" class="form-control border-primary" name="vehicle_type" id="vehicle_type" value="{{ old('vehicle_type') }}">
                @error('vehicle_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <label class="form-label">مصاريف الديزل</label>
                <input type="number" class="form-control border-primary" name="diesel_cost" id="diesel_cost" value="{{ old('diesel_cost') ?? 0}}">
                @error('diesel_cost')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">عمولة السائق</label>
                <input type="number" class="form-control border-primary" name="driver_wage" id="driver_wage" value="{{ old('driver_wage') ?? 0 }}">
                @error('driver_wage')
                    <div class="text-danger">{{ $message }}</div>
                @enderror 
            </div>
            <div class="col">
                <label class="form-label">مصاريف أخرى</label>
                <input type="number" class="form-control border-primary" name="other_expenses" id="other_expenses" value="{{ old('other_expenses') ?? 0 }}">
                @error('other_expenses')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label class="form-label">ملاحظات</label>
                <input type="text" class="form-control border-primary" name="notes" id="notes" value="{{ old('notes') }}">
                @error('notes')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <!-- Container Selection Section -->
        <div class="row mb-4" id="container-section" >
            <div class="col-12">
                <h5 class="mb-3">اختيار الحاويات</h5>
                <div class="card border-primary bg-light p-3">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label">الحاويات المتاحة في المعاملة</label>
                            <div class="d-flex">
                                <div class="input-group input-group-sm w-auto">
                                    <input class="form-control border-primary" type="search" id="container-search" placeholder="إبحث عن حاوية بالكود..." aria-label="Search">
                                    <button class="btn btn-outline-primary" type="button" id="clear-search">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="containers-list" class="row">
                            <!-- Containers will be populated here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary fw-bold">حفظ الإشعار</button>
    </form>
</div>

<script>
    let currentContainers = [];

    $('#transaction_id').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
    });

    $('#transaction_id').on('change', function () {
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let customer = $(this).find(':selected').data('customer');
        $('#customer_id').val(customer || '');

        const containers = $(this).find(':selected').data('containers');
        
        // Show/hide container selection section
        if (containers && containers.length > 0) {
            currentContainers = containers;
            displayContainers(containers);
            $('#containers-list').show();
        } else if (containers) {
            currentContainers = containers;
            displayContainers(containers);
        } else {
            currentContainers = [];
            $('#containers-list').hide();
        }
        
        // Clear search when customer changes
        $('#container-search').val('');
    });

    $('#driver_name').select2({
        placeholder: "ابحث عن إسم السائق...",
        allowClear: true,
        tags: true,
    });

    $('#driver_name').on('change', function () {
        let nid = $(this).find(':selected').data('nid');
        $('#driver_NID').val(nid || '');
        let id = $(this).find(':selected').data('id');
        $('#driver_id').val(id || '');
        let vehiclePlate = $(this).find(':selected').data('vehicle-plate');
        $('#plate_number').val(vehiclePlate || '');
        let vehicleType = $(this).find(':selected').data('vehicle-type');
        $('#vehicle_type').val(vehicleType || '');
    });

    // Container search functionality
    $('#container-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        filterContainers(searchTerm);
    });

    $('#clear-search').on('click', function() {
        $('#container-search').val('');
        filterContainers('');
    });

    function filterContainers(searchTerm) {
        $('.container-card').each(function() {
            const containerCode = $(this).find('.fw-bold').text().toLowerCase();
            const containerId = $(this).find('.text-primary:last').text().toLowerCase();
            
            if (searchTerm === '' || containerCode.includes(searchTerm) || containerId.includes(searchTerm)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    }

    function displayContainers(containers) {
        const containersList = $('#containers-list');
        containersList.empty();

        // Filter containers that are available for receiving (status: 'متوفر')
        const availableContainers = containers.filter(container => {
            const hasTransportOrders = container.transport_orders && container.transport_orders.length > 0;
            return !hasTransportOrders;
        });

        if (availableContainers.length === 0) {
            containersList.html(`
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        لا توجد حاويات متاحة في هذه المعاملة.
                    </div>
                </div>
            `);
            return;
        }

        availableContainers.forEach(container => {
            const containerCard = `
                <div class="col-md-4 col-sm-6 mb-3 container-item">
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
            const isCurrentlyChecked = checkbox.prop('checked');

            if(!isCurrentlyChecked) {
                // شيّل أي checkboxes تانية متعلمة
                $('.container-checkbox').prop('checked', false);
                // شيّل الستايل من كل الكروت
                $('.container-card').removeClass('border-primary bg-primary-subtle');
            }

            checkbox.prop('checked', !isCurrentlyChecked);
            updateContainerCardStyle($(this), checkbox.prop('checked'));
        });

        // Style checkboxes when clicked directly
        $('.container-checkbox').on('change', function() {
            const isChecked = $(this).prop('checked');

            if (isChecked) {
                $('.container-checkbox').not(this).prop('checked', false);
                $('.container-card').not($(this).closest('.container-card')).removeClass('border-primary bg-primary-subtle');
            }
            
            updateContainerCardStyle($(this).closest('.container-card'), isChecked);
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
    #container-search {
        min-width: 250px;
    }
</style>

@endsection