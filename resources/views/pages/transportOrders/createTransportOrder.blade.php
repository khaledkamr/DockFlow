@extends('layouts.app')

@section('title', 'إضافة إشعار نقل')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة إشعار نقل</h2>

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
    <form action="{{ route('policies.receive.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="type" value="تسليم">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">رقم المعاملة</label>
                <select class="form-select border-primary" id="transaction_id" name="transaction_id">
                    <option value="">اختر رقم المعاملة...</option>
                    @foreach ($transactions as $transaction)
                        <option value="{{ $transaction->id }}" data-containers="{{ $transaction->containers }}">
                            {{ $transaction->code }} - {{ $transaction->customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label class="form-label">من</label>
                <input type="text" class="form-control border-primary" id="from" name="from" value="" readonly>
            </div>
            <div class="col">
                <label class="form-label">إلى</label>
                <input type="text" class="form-control border-primary" id="to" name="to" value="">
            </div>
            <div class="col">
                <label class="form-label">مدة النقل (بالأيام)</label>
                <input type="number" class="form-control border-primary" id="duration" name="duration" value="">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم السائق</label>
                <select name="driver_id" id="driver_id" class="form-select border-primary">
                    <option value="">اختر السائق...</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" data-nid="{{ $driver->NID }}">
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">هوية السائق</label>
                <input type="text" class="form-control border-primary" name="driver_NID" id="driver_NID">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">نوع السيارة</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select border-primary">
                    <option value="">اختر نوع السيارة...</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-plate="{{ $vehicle->plate_number }}">
                            {{ $vehicle->type }}
                        </option>
                    @endforeach
                </select>
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">لوحة السيارة</label>
                <input type="text" class="form-control border-primary" name="plate_number" id="plate_number">
                @error('plate_number')
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label">الحاويات المتاحة في المعاملة</label>
                            <div class="d-flex">
                                <div class="input-group input-group-sm w-auto">
                                    <input class="form-control border-primary" type="search" id="container-search" placeholder="إبحث عن حاوية بالكود..." aria-label="Search">
                                    <button class="btn btn-outline-primary" type="button" id="clear-search">
                                        <i class="fas fa-times"></i>
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
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let containers = $(this).find(':selected').data('containers');
        let invoices = $(this).find(':selected').data('invoices');
        
        // Check for unpaid invoices
        hasUnpaidInvoices = false;
        if (invoices && Array.isArray(invoices)) {
            hasUnpaidInvoices = invoices.some(invoice => invoice.payment === 'لم يتم الدفع');
        }
        
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

    $('#driver_id').select2({
        placeholder: "ابحث عن إسم السائق...",
        allowClear: true
    });

    $('#driver_id').on('change', function () {
        let nid = $(this).find(':selected').data('nid');
        $('#driver_NID').val(nid || '');
    });

    $('#vehicle_id').select2({
        placeholder: "اختر نوع السيارة...",
        allowClear: true
    });

    $('#vehicle_id').on('change', function () {
        let plate = $(this).find(':selected').data('plate');
        $('#plate_number').val(plate || '');
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
        const availableContainers = containers.filter(container => 
            container.status === 'متوفر'
        );

        if (availableContainers.length === 0) {
            containersList.html(`
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        لا توجد حاويات متاحة للتسليم لهذا العميل
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