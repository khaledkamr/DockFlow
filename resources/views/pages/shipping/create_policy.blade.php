@extends('layouts.app')

@section('title', 'إضافة بوليصة شحن')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <h2 class="mb-4">إضافة بوليصة شحن</h2>

    <div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
        <form action="{{ route('shipping.policies.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-6 col-lg-3">
                    <label class="form-label">العميل</label>
                    <select class="form-select border-primary" id="customer_id" name="customer_id">
                        <option value="">اختر العميل...</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->code }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <label class="form-label">مكان التحميل</label>
                    <input type="text" class="form-control border-primary" id="from" name="from"
                        value="{{ old('from') }}">
                    @error('from')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <label class="form-label">مكان التفريغ</label>
                    <input type="text" class="form-control border-primary" id="to" name="to"
                        value="{{ old('to') }}">
                    @error('to')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <label class="form-label">تاريخ البوليصة</label>
                    <input type="date" class="form-control border-primary" id="date" name="date"
                        value="{{ old('date', Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6 col-md-6 col-lg-3">
                    <label class="form-label">نوع الناقل</label>
                    <select name="type" id="type" class="form-select border-primary">
                        <option value="ناقل داخلي" {{ old('type') == 'ناقل داخلي' ? 'selected' : '' }}>ناقل داخلي</option>
                        <option value="ناقل خارجي" {{ old('type') == 'ناقل خارجي' ? 'selected' : '' }}>ناقل خارجي</option>
                    </select>
                </div>
                <div class="col-6 col-md-6 col-lg-3 internal-field">
                    <label class="form-label">إســم السائق</label>
                    <select name="driver_id" id="driver_id" class="form-select border-primary">
                        <option value="">اختر السائق...</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" data-nid="{{ $driver->NID }}"
                                data-vehicle-plate="{{ $driver->vehicle ? $driver->vehicle->plate_number : '' }}"
                                data-vehicle-type="{{ $driver->vehicle ? $driver->vehicle->type : '' }}"
                                data-vehicle-id="{{ $driver->vehicle ? $driver->vehicle->id : '' }}"
                                {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('driver_name')
                        <div class="text-danger">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="col-6 col-md-6 col-lg-3 internal-field">
                        <label class="form-label">هوية السائق</label>
                        <input type="text" class="form-control border-primary" name="driver_NID" id="driver_NID"
                            value="{{ old('driver_NID') }}" readonly>
                    </div>
                    <div class="col-6 col-md-6 col-lg-3 internal-field">
                        <label class="form-label">لوحة السيارة</label>
                        <input type="text" class="form-control border-primary" name="plate_number" id="plate_number"
                            value="{{ old('plate_number') }}" readonly>
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id') }}">
                    </div>

                    <div class="col-6 col-md-6 col-lg-3 external-field" style="display: none">
                        <label class="form-label d-block">إســم المورد</label>
                        <select name="supplier_id" id="supplier_id" class="form-select border-primary" style="width: 100%;">
                            <option value="">اختر المورد...</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-6 col-lg-3 external-field" style="display: none">
                        <label class="form-label">إسم السائق</label>
                        <input type="text" class="form-control border-primary" name="driver_name" id="driver_name">
                    </div>
                    <div class="col-6 col-md-6 col-lg-3 external-field" style="display: none">
                        <label class="form-label">لوحة السيارة</label>
                        <input type="text" class="form-control border-primary" name="vehicle_plate" id="vehicle_plate">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-sm-6 col-md-4 col-lg external-field">
                        <label class="form-label">مصاريف المورد</label>
                        <input type="number" class="form-control border-primary" name="supplier_cost" id="supplier_cost"
                            value="{{ old('supplier_cost') ?? 0 }}">
                        @error('supplier_cost')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 col-lg internal-field">
                        <label class="form-label">مصاريف الديزل</label>
                        <input type="number" class="form-control border-primary" name="diesel_cost" id="diesel_cost"
                            value="{{ old('diesel_cost') ?? 0 }}">
                        @error('diesel_cost')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 col-lg internal-field">
                        <label class="form-label">عمولة السائق</label>
                        <input type="number" class="form-control border-primary" name="driver_wage" id="driver_wage"
                            value="{{ old('driver_wage') ?? 0 }}">
                        @error('driver_wage')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 col-lg">
                        <label class="form-label">مصاريف أخرى</label>
                        <input type="number" class="form-control border-primary" name="other_expenses" id="other_expenses"
                            value="{{ old('other_expenses') ?? 0 }}">
                        @error('other_expenses')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 col-lg">
                        <label class="form-label">سعر العميل</label>
                        <input type="number" class="form-control border-primary" id="client_cost" name="client_cost"
                            value="{{ old('client_cost') ?? 0 }}">
                        @error('client_cost')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Goods Section -->
                <div class="row mb-4" id="goods-section">
                    <div class="col-12">
                        <h5 class="mb-3">البضائع</h5>
                        <div class="table-container" id="tableContainer">
                            <table class="table table-hover" id="goods-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center text-nowrap" style="min-width: 200px">الوصف</th>
                                        <th class="text-center text-nowrap" style="min-width: 100px">الكمية</th>
                                        <th class="text-center text-nowrap" style="min-width: 100px">الوزن</th>
                                        <th class="text-center text-nowrap" style="min-width: 200px">ملاحظات</th>
                                        <th class="text-center text-nowrap" style="min-width: 50px">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="goods-tbody">
                                    <tr class="goods-row">
                                        <td>
                                            <input type="text" class="form-control" name="goods[0][description]" required>
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control"
                                                name="goods[0][quantity]" min="0">
                                        </td>
                                        <td>
                                            <input type="number" step="1" class="form-control"
                                                name="goods[0][weight]" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="goods[0][notes]">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm delete-row" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
                            <i class="fa-solid fa-arrows-left-right me-1"></i>
                            اسحب الجدول لليمين أو اليسار لرؤية المزيد
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 mt-3" id="add-goods-row">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">إضافة صف</span><span
                                    class="d-inline d-sm-none">إضافة</span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary fw-bold col-12 col-sm-2">
                    <i class="fas fa-save me-2"></i><span class="d-sm-inline">حفظ البوليصة</span><span
                </button>
            </form>
        </div>

        <script>
            const pickupInput = document.querySelector('#from');
            const dropoffInput = document.querySelector('#to');
            const clientPriceInput = document.querySelector('#client_cost');
            const customerId = document.querySelector('#customer_id');

            function checkService() {
                const pickup = pickupInput.value;
                const dropoff = dropoffInput.value;

                if (!pickup || !dropoff) return;

                fetch(
                        `{{ route('customer.check.service') }}?customer_id=${customerId.value}&pickup=${pickup}&dropoff=${dropoff}`)
                    .then(res => res.json())
                    .then(data => {
                        console.log("DATA:", data);
                        if (data.exists) {
                            console.log("Service exists. Price:", data.price);
                            clientPriceInput.value = data.price;
                        }
                    });
            }

            pickupInput.addEventListener('change', checkService);
            dropoffInput.addEventListener('change', checkService);

            $('#customer_id').select2({
                placeholder: "ابحث عن إسم العميل...",
                allowClear: true
            });

            $(document).ready(function() {
                function toggleFields() {
                    const selected = $('#type').val();
                    $('.internal-field, .external-field').hide();

                    if (selected === "ناقل داخلي") {
                        $('.internal-field').show();
                        $('#supplier_id').val(null).trigger('change');
                    } else if (selected === "ناقل خارجي") {
                        $('.external-field').show();
                        $('#driver_id').val(null).trigger('change');
                    }
                }
                $('#type').on('change', toggleFields);
                toggleFields();
            })

            $('#driver_id').select2({
                placeholder: "ابحث عن إسم السائق...",
                allowClear: true,
            });

            $('#driver_id').on('change', function() {
                let nid = $(this).find(':selected').data('nid');
                $('#driver_NID').val(nid || '');
                let vehiclePlate = $(this).find(':selected').data('vehicle-plate');
                $('#plate_number').val(vehiclePlate || '');
                let vehicleId = $(this).find(':selected').data('vehicle-id');
                $('#vehicle_id').val(vehicleId || '');
            });

            $('#supplier_id').select2({
                placeholder: "ابحث عن إسم المورد...",
                allowClear: true,
            });

            let goodsRowIndex = 1;

            // Add new row
            $('#add-goods-row').on('click', function() {
                const newRow = `
                    <tr class="goods-row">
                        <td>
                            <input type="text" class="form-control" name="goods[${goodsRowIndex}][description]" required>
                        </td>
                        <td>
                            <input type="number" step="1" class="form-control" name="goods[${goodsRowIndex}][quantity]" min="0">
                        </td>
                        <td>
                            <input type="number" step="1" class="form-control" name="goods[${goodsRowIndex}][weight]" min="0">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="goods[${goodsRowIndex}][notes]">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#goods-tbody').append(newRow);
                goodsRowIndex++;
                updateDeleteButtons();
            });

            // Delete row
            $(document).on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateDeleteButtons();
                reindexRows();
            });

            // Update delete buttons state
            function updateDeleteButtons() {
                const rowCount = $('#goods-tbody tr').length;
                if (rowCount === 1) {
                    $('.delete-row').prop('disabled', true);
                } else {
                    $('.delete-row').prop('disabled', false);
                }
            }

            // Reindex rows after deletion
            function reindexRows() {
                $('#goods-tbody tr').each(function(index) {
                    $(this).find('input').each(function() {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/\[\d+\]/, `[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
                goodsRowIndex = $('#goods-tbody tr').length;
            }

            // Initialize on page load
            $(document).ready(function() {
                updateDeleteButtons();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const tableContainer = document.getElementById('tableContainer');
                
                // Check if table needs scrolling
                function checkScroll() {
                    if (tableContainer.scrollWidth > tableContainer.clientWidth) {
                        tableContainer.classList.add('has-scroll');
                    } else {
                        tableContainer.classList.remove('has-scroll');
                    }
                }
                
                // Check on load and resize
                checkScroll();
                window.addEventListener('resize', checkScroll);
                
                // Remove scroll hint after first interaction
                const scrollHint = document.querySelector('.scroll-hint');
                if (scrollHint) {
                    tableContainer.addEventListener('scroll', function() {
                        scrollHint.style.display = 'none';
                    }, { once: true });
                }
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

            .container-card {
                cursor: pointer;
                transition: all 0.2s ease;
                border: 2px solid #dee2e6;
            }

            .container-card:hover {
                border-color: #0d6efd;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
