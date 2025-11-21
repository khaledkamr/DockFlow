@extends('layouts.app')

@section('title', 'إنشاء فاتورة شحن')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <h1 class="mb-4">إنشاء فاتورة شحن</h1>

    <form method="GET" action="" class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <select name="customer_id" id="customer_id" class="form-select border-primary" required>
                    <option value="">-- اختر العميل --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->code }} - {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <button type="submit" class="btn btn-primary fw-bold w-100"
                    onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                    عرض البوالص <i class="fa-solid fa-file-invoice ms-1"></i>
                </button>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="input-group">
                    <input id="search-input" class="form-control border-primary" type="search"
                        placeholder="إبحث عن بوليصة بالكود..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="button" id="clear-search">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if (isset($shippingPolicies) && $shippingPolicies->count() > 0)
        <form method="POST" action="{{ route('invoices.shipping.store') }}" class="mb-5">
            @csrf
            <input type="hidden" name="type" value="شحن">
            <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">

            <!-- Search results info -->
            <div class="alert alert-secondary mb-3" id="search-info" style="display: none;">
                <i class="fas fa-search me-1"></i>
                <span id="search-results-text"></span>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clear-search-btn">
                    <i class="fas fa-times me-1"></i> إلغاء البحث
                </button>
            </div>

            <!-- Selected policies counter -->
            <div class="alert alert-info mb-3" id="selection-counter" style="display: none;">
                <i class="fas fa-info-circle"></i>
                تم تحديد <span id="selected-count">0</span> بوليصة من أصل <span
                    id="visible-count">{{ $shippingPolicies->count() }}</span> بوليصة
                <span class="ms-3">
                    <i class="fas fa-dollar-sign text-success"></i>
                    إجمالي التكلفة: <strong id="total-cost">0.00</strong> ر.س
                </span>
            </div>

            <div class="table-container" id="tableContainer">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center bg-dark text-white text-nowrap" width="10%">
                                <button type="button" id="select-all-btn" class="btn btn-sm btn-primary fw-bold">
                                    <i class="fas fa-check-square me-1"></i>
                                    <span class="d-none d-sm-inline">تحديد الكل</span>
                                </button>
                            </th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">#</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">رقم البوليصة</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">نوع الناقل</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">من</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">إلى</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">التاريخ</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">المبلغ</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">الحالة</th>
                        </tr>
                    </thead>
                    <tbody id="policies-table-body">
                        @foreach ($shippingPolicies as $policy)
                            <tr class="text-center policy-row" data-policy-id="{{ $policy->id }}"
                                data-policy-code="{{ strtolower($policy->code) }}"
                                data-policy-cost="{{ $policy->total_cost }}" data-from="{{ strtolower($policy->from) }}"
                                data-to="{{ strtolower($policy->to) }}">
                                <td class="checkbox-cell" style="cursor: pointer;">
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" name="shipping_policy_ids[]" value="{{ $policy->id }}"
                                            class="form-check-input policy-checkbox" style="transform: scale(1.2);">
                                    </div>
                                </td>
                                <td class="fw-bold row-number">{{ $loop->iteration }}</td>
                                <td class="text-primary fw-bold">
                                    <a href="" class="text-decoration-none policy-code">
                                        {{ $policy->code }}
                                    </a>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $policy->type == 'ناقل داخلي' ? 'status-available' : 'status-danger' }}">
                                        {{ $policy->type }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-danger text-nowrap"></i> {{ $policy->from }}
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-danger text-nowrap"></i> {{ $policy->to }}
                                </td>
                                <td>{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                                <td class="fw-bold text-success">{{ number_format($policy->total_cost, 2) }} <i
                                        data-lucide="saudi-riyal"></i></td>
                                <td>
                                    <span class="badge status-delivered">
                                        تم الاستلام <i class="fa-solid fa-check"></i>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- No results message -->
            <div class="alert alert-danger text-center" id="no-results" style="display: none;">
                <i class="fas fa-search me-2"></i>
                لم يتم العثور على بوليصات تطابق البحث <span id="search-term"></span>
            </div>

            <!-- Payment Details Section -->
            <div class="card border-dark mb-3 mt-4" id="payment-details">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        تفاصيل الدفع
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4 col-sm-4 col-lg-3">
                            <label for="payment_method" class="form-label fw-bold">
                                <i class="fas fa-money-bill me-1"></i>
                                طريقة الدفع
                            </label>
                            <select name="payment_method" id="payment_method" class="form-select border-primary"
                                required>
                                <option value="آجل">آجل</option>
                                <option value="كاش">كاش</option>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                            </select>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-3">
                            <label for="tax_rate" class="form-label fw-bold">
                                <i class="fas fa-receipt me-1"></i>
                                الضريبة المضافة
                            </label>
                            <select name="tax_rate" id="tax_rate" class="form-select border-primary" required>
                                <option value="15">خاضع للضريبة (15%)</option>
                                <option value="0">غير خاضع للضريبة</option>
                            </select>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-3">
                            <label for="discount" class="form-label fw-bold">
                                <i class="fas fa-percent me-1"></i>
                                نسبة الخصم (%)
                            </label>
                            <input type="number" name="discount" id="discount" class="form-control border-primary"
                                min="0" max="100" step="1" value="0" placeholder="0.00"
                                required>
                        </div>
                        <div class="col-12 col-sm-12 col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold" id="create-invoice-btn"
                                disabled>
                                <i class="fas fa-plus me-1"></i>
                                إنشاء فاتورة
                            </button>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center g-3">
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="p-2">
                                                <small class="text-muted d-block">إجمالي التكلفة</small>
                                                <div class="d-flex justify-content-center align-items-center mb-1">
                                                    <h4 class="text-primary mb-0" id="summary-total">0.00</h4>
                                                    <i data-lucide="saudi-riyal" class="text-primary ms-1"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="p-2">
                                                <small class="text-muted d-block">الضريبة المضافة</small>
                                                <div class="d-flex justify-content-center align-items-center mb-1">
                                                    <h4 class="text-primary mb-0" id="summary-tax">0.00</h4>
                                                    <i data-lucide="saudi-riyal" class="text-primary ms-1"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="p-2">
                                                <small class="text-muted d-block">قيمة الخصم</small>
                                                <div
                                                    class="d-flex justify-content-center align-items
                                            const summaryTax = document.getElementById('summary-tax');-center mb-1">
                                                    <h4 class="text-danger mb-0" id="summary-discount">0.00</h4>
                                                    <i data-lucide="saudi-riyal" class="text-danger ms-1"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="p-2">
                                                <small class="text-muted d-block">المبلغ النهائي</small>
                                                <div class="d-flex justify-content-center align-items-center mb-1">
                                                    <h4 class="text-success mb-0" id="summary-final">0.00</h4>
                                                    <i data-lucide="saudi-riyal" class="text-success ms-1"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-outline-danger fw-bold" id="clear-selection-btn"
                    style="display: none;">
                    <i class="fas fa-times me-1"></i>
                    <span class="d-none d-sm-inline">إلغاء التحديد</span><span class="d-inline d-sm-none">إلغاء</span>
                </button>
            </div>
        </form>
    @elseif(request('customer_id'))
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد بوالص شحن مُستلمة غير مفوترة لهذا العميل.
        </div>
    @endif

    @push('scripts')
        <script>
            $("#customer_id").select2({
                placeholder: "ابحث عن العميل...",
                allowClear: true,
                width: '100%'
            });

            document.addEventListener('DOMContentLoaded', function() {
                const selectAllBtn = document.getElementById('select-all-btn');
                const clearSelectionBtn = document.getElementById('clear-selection-btn');
                const createInvoiceBtn = document.getElementById('create-invoice-btn');
                const selectionCounter = document.getElementById('selection-counter');
                const selectedCountSpan = document.getElementById('selected-count');
                const visibleCountSpan = document.getElementById('visible-count');
                const totalCostSpan = document.getElementById('total-cost');
                const searchInput = document.getElementById('search-input');
                const clearSearchBtn = document.getElementById('clear-search');
                const clearSearchBtnAlt = document.getElementById('clear-search-btn');
                const searchInfo = document.getElementById('search-info');
                const searchResultsText = document.getElementById('search-results-text');
                const noResults = document.getElementById('no-results');
                const searchTerm = document.getElementById('search-term');
                const taxRateSelect = document.getElementById('tax_rate');
                const discountInput = document.getElementById('discount');
                const summaryTotal = document.getElementById('summary-total');
                const summaryTax = document.getElementById('summary-tax');
                const summaryDiscount = document.getElementById('summary-discount');
                const summaryFinal = document.getElementById('summary-final');

                const rows = document.querySelectorAll('.policy-row');
                const checkboxCells = document.querySelectorAll('.checkbox-cell');

                let allSelected = false;
                let currentSearchTerm = '';

                // Get visible checkboxes and rows
                function getVisibleElements() {
                    const visibleRows = document.querySelectorAll('.policy-row:not([style*="display: none"])');
                    const visibleCheckboxes = [];
                    visibleRows.forEach(row => {
                        const checkbox = row.querySelector('.policy-checkbox');
                        if (checkbox) visibleCheckboxes.push(checkbox);
                    });
                    return {
                        visibleRows,
                        visibleCheckboxes
                    };
                }

                // Calculate total cost
                function calculateTotalCost() {
                    let total = 0;
                    const selectedCheckboxes = document.querySelectorAll('.policy-checkbox:checked');
                    selectedCheckboxes.forEach(checkbox => {
                        const row = checkbox.closest('.policy-row');
                        const cost = parseFloat(row.getAttribute('data-policy-cost')) || 0;
                        total += cost;
                    });
                    return total;
                }

                // Update summary
                function updateSummary() {
                    const total = calculateTotalCost();
                    const discountPercent = parseFloat(discountInput.value) || 0;
                    const discountAmount = (total * discountPercent) / 100;
                    const finalAmount = total - discountAmount;
                    const taxRate = parseFloat(taxRateSelect.value) || 0;
                    const taxAmount = (finalAmount * taxRate) / 100;
                    const finalWithTax = finalAmount + taxAmount;

                    if (summaryTotal) summaryTotal.textContent = total.toFixed(2);
                    if (summaryTax) summaryTax.textContent = taxAmount.toFixed(2);
                    if (summaryDiscount) summaryDiscount.textContent = discountAmount.toFixed(2);
                    if (summaryFinal) summaryFinal.textContent = finalWithTax.toFixed(2);
                }

                // Update row numbers for visible rows
                function updateRowNumbers() {
                    const visibleRows = document.querySelectorAll('.policy-row:not([style*="display: none"])');
                    visibleRows.forEach((row, index) => {
                        const numberCell = row.querySelector('.row-number');
                        if (numberCell) {
                            numberCell.textContent = index + 1;
                        }
                    });
                }

                // Highlight search term
                function highlightSearchTerm(text, term) {
                    if (!term) return text;
                    const regex = new RegExp(`(${term})`, 'gi');
                    return text.replace(regex, '<mark class="bg-warning">$1</mark>');
                }

                // Search functionality
                function performSearch(searchTerm) {
                    currentSearchTerm = searchTerm.toLowerCase().trim();
                    let visibleCount = 0;
                    let hasResults = false;

                    rows.forEach(row => {
                        const policyCode = row.getAttribute('data-policy-code') || '';
                        const from = row.getAttribute('data-from') || '';
                        const to = row.getAttribute('data-to') || '';
                        const codeCell = row.querySelector('.policy-code');

                        if (currentSearchTerm === '' ||
                            policyCode.includes(currentSearchTerm) ||
                            from.includes(currentSearchTerm) ||
                            to.includes(currentSearchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                            hasResults = true;

                            // Highlight search term
                            if (codeCell && currentSearchTerm) {
                                const originalCode = codeCell.textContent;
                                codeCell.innerHTML = highlightSearchTerm(originalCode, searchTerm);
                            }
                        } else {
                            row.style.display = 'none';
                            // Uncheck hidden checkboxes
                            const checkbox = row.querySelector('.policy-checkbox');
                            if (checkbox) checkbox.checked = false;

                            // Remove highlighting
                            if (codeCell) {
                                codeCell.innerHTML = codeCell.textContent;
                            }
                        }
                    });

                    // Update UI based on search results
                    if (currentSearchTerm) {
                        searchInfo.style.display = 'block';
                        searchResultsText.textContent = `عُثر على ${visibleCount} بوليصة من أصل ${rows.length} بوليصة`;
                        if (searchTerm) searchTerm.innerHTML = `"${searchTerm}"`;

                        if (!hasResults) {
                            noResults.style.display = 'block';
                            document.querySelector('.table-container').style.display = 'none';
                        } else {
                            noResults.style.display = 'none';
                            document.querySelector('.table-container').style.display = 'block';
                        }
                    } else {
                        searchInfo.style.display = 'none';
                        noResults.style.display = 'none';
                        document.querySelector('.table-container').style.display = 'block';

                        // Remove all highlighting
                        rows.forEach(row => {
                            const codeCell = row.querySelector('.policy-code');
                            if (codeCell) {
                                codeCell.innerHTML = codeCell.textContent;
                            }
                        });
                    }

                    updateRowNumbers();
                    updateUI();
                }

                // Function to update UI based on selection
                function updateUI() {
                    const {
                        visibleRows,
                        visibleCheckboxes
                    } = getVisibleElements();
                    const selectedCheckboxes = visibleCheckboxes.filter(cb => cb.checked);
                    const selectedCount = selectedCheckboxes.length;
                    const totalVisibleCount = visibleCheckboxes.length;
                    const totalCost = calculateTotalCost();

                    // Update counters
                    if (selectedCountSpan) selectedCountSpan.textContent = selectedCount;
                    if (visibleCountSpan) visibleCountSpan.textContent = totalVisibleCount;
                    if (totalCostSpan) totalCostSpan.textContent = totalCost.toFixed(2);

                    // Show/hide elements based on selection
                    if (selectedCount > 0) {
                        if (selectionCounter) selectionCounter.style.display = 'block';
                        createInvoiceBtn.disabled = false;
                        clearSelectionBtn.style.display = 'inline-block';
                    } else {
                        if (selectionCounter) selectionCounter.style.display = 'none';
                        createInvoiceBtn.disabled = true;
                        clearSelectionBtn.style.display = 'none';
                    }

                    // Update select all button
                    if (selectedCount === totalVisibleCount && totalVisibleCount > 0) {
                        selectAllBtn.innerHTML = 'إلغاء الكل';
                        selectAllBtn.classList.remove('btn-primary');
                        selectAllBtn.classList.add('btn-danger');
                        allSelected = true;
                    } else {
                        selectAllBtn.innerHTML = 'تحديد الكل';
                        selectAllBtn.classList.remove('btn-danger');
                        selectAllBtn.classList.add('btn-primary');
                        allSelected = false;
                    }

                    // Update row styling
                    visibleRows.forEach(row => {
                        const checkbox = row.querySelector('.policy-checkbox');
                        if (checkbox && checkbox.checked) {
                            row.classList.add('table-primary');
                            row.style.transform = 'scale(1.01)';
                            row.style.boxShadow = '0 2px 4px rgba(0,123,255,0.3)';
                        } else {
                            row.classList.remove('table-primary');
                            row.style.transform = 'scale(1)';
                            row.style.boxShadow = 'none';
                        }
                    });

                    // Update summary
                    updateSummary();
                }

                // Search input event listener
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        performSearch(this.value);
                    });

                    searchInput.addEventListener('keyup', function(e) {
                        if (e.key === 'Escape') {
                            this.value = '';
                            performSearch('');
                        }
                    });
                }

                // Clear search functionality
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        if (searchInput.value) {
                            searchInput.value = '';
                            performSearch('');
                            searchInput.focus();
                        }
                    });
                }

                if (clearSearchBtnAlt) {
                    clearSearchBtnAlt.addEventListener('click', function() {
                        if (searchInput) {
                            searchInput.value = '';
                            performSearch('');
                            searchInput.focus();
                        }
                    });
                }

                // Discount input change
                if (discountInput) {
                    discountInput.addEventListener('input', updateSummary);
                }
                if (taxRateSelect) {
                    taxRateSelect.addEventListener('change', updateSummary);
                }

                // Select/Deselect all functionality
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const {
                            visibleCheckboxes
                        } = getVisibleElements();

                        if (allSelected) {
                            visibleCheckboxes.forEach(checkbox => {
                                checkbox.checked = false;
                            });
                        } else {
                            visibleCheckboxes.forEach(checkbox => {
                                checkbox.checked = true;
                            });
                        }

                        updateUI();

                        // Add animation effect
                        const {
                            visibleRows
                        } = getVisibleElements();
                        visibleRows.forEach((row, index) => {
                            setTimeout(() => {
                                row.style.transition = 'all 0.2s ease';
                                row.classList.add('animate__animated', 'animate__pulse');
                                setTimeout(() => {
                                    row.classList.remove('animate__animated',
                                        'animate__pulse');
                                }, 200);
                            }, index * 50);
                        });
                    });
                }

                // Clear selection functionality
                if (clearSelectionBtn) {
                    clearSelectionBtn.addEventListener('click', function() {
                        const checkboxes = document.querySelectorAll('.policy-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        updateUI();
                    });
                }

                // Click on checkbox cell to toggle checkbox
                checkboxCells.forEach(cell => {
                    cell.addEventListener('click', function(e) {
                        if (e.target.type !== 'checkbox') {
                            const checkbox = this.querySelector('.policy-checkbox');
                            if (checkbox) {
                                checkbox.checked = !checkbox.checked;
                                updateUI();

                                // Add click animation
                                const row = this.closest('.policy-row');
                                if (row) {
                                    row.style.transition = 'all 0.2s ease';
                                    row.classList.add('animate__animated', 'animate__pulse');
                                    setTimeout(() => {
                                        row.classList.remove('animate__animated',
                                            'animate__pulse');
                                    }, 200);
                                }
                            }
                        }
                    });
                });

                // Individual checkbox change
                rows.forEach(row => {
                    const checkbox = row.querySelector('.policy-checkbox');
                    if (checkbox) {
                        checkbox.addEventListener('change', function() {
                            updateUI();
                        });
                    }
                });

                // Add hover effects
                rows.forEach(row => {
                    row.addEventListener('mouseenter', function() {
                        if (!this.classList.contains('table-primary')) {
                            this.style.backgroundColor = '#f8f9fa';
                        }
                    });

                    row.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('table-primary')) {
                            this.style.backgroundColor = '';
                        }
                    });
                });

                // Initial UI update
                updateUI();
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .policy-row {
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .policy-row.table-primary {
                background-color: rgba(13, 110, 253, 0.1) !important;
                border-left: 4px solid #0d6efd;
            }

            .checkbox-cell {
                position: relative;
            }

            .checkbox-cell:hover {
                background-color: rgba(13, 110, 253, 0.05);
            }

            .form-check-input:checked {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }

            .btn {
                transition: all 0.2s ease;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            #create-invoice-btn:disabled {
                transform: none !important;
            }

            .table-container {
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .alert {
                border-radius: 8px;
                border: none;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            mark.bg-warning {
                background-color: #fff3cd !important;
                color: #856404;
                padding: 1px 3px;
                border-radius: 3px;
                font-weight: bold;
            }

            #search-input:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.02);
                }

                100% {
                    transform: scale(1);
                }
            }

            .animate__pulse {
                animation: pulse 0.2s ease-in-out;
            }

            .policy-row {
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
        </style>
    @endpush

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
