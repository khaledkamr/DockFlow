@extends('layouts.app')

@section('title', 'إضافة بوليصة إستلام')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة بوليصة تسليم</h2>

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
    <form action="{{ route('policies.receive.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
        <input type="hidden" name="type" value="تسليم">
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم العميــل <span class="text-danger">*</span></label>
                <select class="form-select border-primary" id="customer_name">
                    <option value="">اختر اسم العميل...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" data-account="{{ $customer->account ? $customer->account->code : null }}"
                            data-contract="{{ $customer->contract ? $customer->contract->id : null }}"
                            data-containers="{{ $customer->containers }}"
                            data-invoices="{{ $customer->invoices }}" >
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="contract_id" name="contract_id">
            <input type="hidden" id="customer_id" name="customer_id">
            <div class="col">
                <label class="form-label">رقــم العميــل</label>
                <input type="text" class="form-control border-primary" id="customer_account" name="customer_account" value="" readonly>
            </div>
            <div class="col">
                <label class="form-label">إســم السائق <span class="text-danger">*</span></label>
                <input type="text" name="driver_name" class="form-control border-primary">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">رقــم هوية السائق <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="driver_NID">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">رقــم السائق</label>
                <input type="text" class="form-control border-primary" name="driver_number">
                @error('driver_number')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">نوع السيارة <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="driver_car">
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">لوحة السيارة <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-primary" name="car_code">
                @error('car_code')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col"></div>
        </div>
        
        <!-- Container Selection Section -->
        <div class="row mb-4" id="container-section" >
            <div class="col-12">
                <h5 class="mb-3">اختيار الحاويات</h5>
                <div class="card border-primary bg-light p-3">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label">الحاويات المتاحة للتسليم</label>
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

        <button type="submit" class="btn btn-primary fw-bold">حفظ البوليصة</button>
    </form>
</div>

<script>
    let currentContainers = [];
    let hasUnpaidInvoices = false;
    let maxSelectableContainers = 0;

    $('#customer_name').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let account = $(this).find(':selected').data('account');
        $('#customer_account').val(account || '');

        let containers = $(this).find(':selected').data('containers');
        let invoices = $(this).find(':selected').data('invoices');
        
        // Check for unpaid invoices
        hasUnpaidInvoices = false;
        if (invoices && Array.isArray(invoices)) {
            hasUnpaidInvoices = invoices.some(invoice => invoice.isPaid === 'لم يتم الدفع');
        }
        
        // Show/hide container selection section
        if (containers && containers.length > 0) {
            currentContainers = containers;
            displayContainers(containers);
            $('#containers-list').show();
            
            // Calculate max selectable containers
            console.log(containers);
            const availableContainers = containers.filter(container => (container.status === 'متأخر' || container.status === 'في الساحة'));
            console.log(availableContainers);
            maxSelectableContainers = hasUnpaidInvoices ? Math.max(0, availableContainers.length - 1) : availableContainers.length;
            
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

        // Filter containers that are available for receiving (status: 'في الساحة')
        const availableContainers = containers.filter(container => 
            container.status === 'في الساحة' || container.status === 'متأخر'
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

        // Add click event to container cards
        $('.container-card').on('click', function() {
            const checkbox = $(this).find('.container-checkbox');
            const isCurrentlyChecked = checkbox.prop('checked');
            
            if (!isCurrentlyChecked) {
                // Trying to check a container
                const currentSelectedCount = $('.container-checkbox:checked').length;

                console.log(currentSelectedCount, maxSelectableContainers, hasUnpaidInvoices);
                
                if (hasUnpaidInvoices && currentSelectedCount >= maxSelectableContainers) {
                    showToast('العميل لديه فواتير غير مدفوعة، لا يمكن سحب جميع الحاويات', 'danger');
                    return;
                }
            }
            
            checkbox.prop('checked', !isCurrentlyChecked);
            updateContainerCardStyle($(this), checkbox.prop('checked'));
        });

        // Style checkboxes when clicked directly
        $('.container-checkbox').on('change', function() {
            const isChecked = $(this).prop('checked');
            
            if (isChecked) {
                // Trying to check a container
                const currentSelectedCount = $('.container-checkbox:checked').length;
                
                if (hasUnpaidInvoices && currentSelectedCount > maxSelectableContainers) {
                    $(this).prop('checked', false);
                    showToast('العميل لديه فواتير غير مدفوعة، لا يمكن سحب جميع الحاويات', 'danger');
                    return;
                }
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