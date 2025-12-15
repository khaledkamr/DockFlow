@extends('layouts.app')

@section('title', 'إنشاء مطالبة')

@section('content')
    <h1 class="mb-4">إنشاء مطالبة</h1>

    <form method="GET" action="" class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <select name="customer_id" class="form-select border-primary" required>
                    <option value="">-- اختر العميل --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <button type="submit" class="btn btn-primary fw-bold w-100"
                    onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
                    عرض الفواتير <i class="fas fa-eye ms-1"></i>
                </button>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="input-group">
                    <input id="search-input" class="form-control border-primary" type="search"
                        placeholder="إبحث عن فاتورة بالكود..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="button" id="clear-search">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if (isset($invoices) && $invoices->count() > 0)
        <form method="POST" action="{{ route('invoices.statements.store') }}" class="mb-5">
            @csrf
            <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

            <!-- Search results info -->
            <div class="alert alert-secondary mb-3" id="search-info" style="display: none;">
                <i class="fas fa-search me-1"></i>
                <span id="search-results-text"></span>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clear-search-btn">
                    <i class="fas fa-times me-1"></i> إلغاء البحث
                </button>
            </div>

            <!-- Selected invoices counter -->
            <div class="alert alert-info mb-3" id="selection-counter" style="display: none;">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div>
                        <i class="fas fa-info-circle"></i>
                        تم تحديد <span id="selected-count">0</span> فاتورة من أصل <span
                            id="visible-count">{{ $invoices->count() }}</span> فاتورة
                    </div>
                    <div class="fw-bold text-primary">
                        <i class="fas fa-calculator me-1"></i>
                        إجمالي المبلغ: <span id="total-amount">0.00</span> ريال
                    </div>
                </div>
            </div>

            <div class="table-container" id="tableContainer">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center bg-dark text-white text-nowrap" width="10%">
                                <button type="button" id="select-all-btn" class="btn btn-sm btn-primary fw-bold">
                                    تحديد الكل
                                </button>
                            </th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">#</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">كود الفاتورة</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">نوع الفاتورة</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">إسم العميل</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">تاريخ الفاتورة</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">المبلغ الإجمالي</th>
                            <th class="text-center fw-bold bg-dark text-white text-nowrap">الحالة</th>
                        </tr>
                    </thead>
                    <tbody id="invoices-table-body">
                        @foreach ($invoices as $invoice)
                            <tr class="text-center invoice-row" data-invoice-id="{{ $invoice->id }}"
                                data-invoice-code="{{ strtolower($invoice->code) }}"
                                data-customer-name="{{ strtolower($invoice->customer->name) }}"
                                data-remaining-amount="{{ $invoice->remaining_amount }}">
                                <td class="checkbox-cell" style="cursor: pointer;">
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                            class="form-check-input invoice-checkbox"
                                            data-amount="{{ $invoice->total_amount }}" style="transform: scale(1.2);">
                                    </div>
                                </td>
                                <td class="fw-bold row-number">{{ $loop->iteration }}</td>
                                <td class="text-primary fw-bold">
                                    <a href="{{ route('invoices.details', $invoice) }}"
                                        class="text-decoration-none invoice-code">
                                        {{ $invoice->code }}
                                    </a>
                                </td>
                                <td>{{ $invoice->type }}</td>
                                <td class="fw-bold">
                                    <a href="{{ route('users.customer.profile', $invoice->customer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $invoice->customer->name }}
                                    </a>
                                </td>
                                <td>{{ Carbon\Carbon::parse($invoice->created_at)->format('Y/m/d') }}</td>
                                <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }}</td>
                                <td><span class="badge bg-danger">غير مدفوعة <i class="fa-solid fa-clock"></i></span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- No results message -->
            <div class="alert alert-danger text-center" id="no-results" style="display: none;">
                <i class="fas fa-search me-2"></i>
                لم يتم العثور على فواتير تطابق البحث <span id="search-term"></span>
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
                        <div class="col-6 col-md-4">
                            <label for="payment_method" class="form-label fw-bold">
                                <i class="fas fa-money-bill me-1"></i>
                                طريقة الدفع
                            </label>
                            <select name="payment_method" id="payment_method" class="form-select border-primary"
                                required>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                                <option value="كاش">كاش</option>
                                <option value="آجل">آجل</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="payment_date" class="form-label fw-bold">
                                <i class="fas fa-calendar me-1"></i>
                                تاريخ الدفع
                            </label>
                            <input type="date" name="date" id="payment_date" class="form-control border-primary"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="notes" class="form-label fw-bold">
                                <i class="fas fa-note-sticky me-1"></i>
                                ملاحظات (اختياري)
                            </label>
                            <input type="text" name="notes" id="notes" class="form-control border-primary"
                                placeholder="أضف ملاحظات إن وجدت">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div
                            class="col-12 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                سيتم إنشاء مطالبة دفع للفواتير المحددة
                            </div>
                            <button type="submit" class="btn btn-primary fw-bold col-12 col-md-auto" id="create-statement-btn" disabled>
                                <i class="fas fa-plus me-1"></i>
                                إنشاء مطالبة دفع
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-outline-danger fw-bold" id="clear-selection-btn"
                    style="display: none;">
                    <i class="fas fa-times me-1"></i>
                    إلغاء التحديد
                </button>
            </div>
        </form>
    @elseif(request('customer_id'))
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد فواتير غير مدفوعة لهذا العميل.
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllBtn = document.getElementById('select-all-btn');
                const clearSelectionBtn = document.getElementById('clear-selection-btn');
                const createStatementBtn = document.getElementById('create-statement-btn');
                const selectionCounter = document.getElementById('selection-counter');
                const selectedCountSpan = document.getElementById('selected-count');
                const visibleCountSpan = document.getElementById('visible-count');
                const totalAmountSpan = document.getElementById('total-amount');
                const searchInput = document.getElementById('search-input');
                const clearSearchBtn = document.getElementById('clear-search');
                const clearSearchBtnAlt = document.getElementById('clear-search-btn');
                const searchInfo = document.getElementById('search-info');
                const searchResultsText = document.getElementById('search-results-text');
                const noResults = document.getElementById('no-results');
                const searchTerm = document.getElementById('search-term');

                const rows = document.querySelectorAll('.invoice-row');
                const checkboxCells = document.querySelectorAll('.checkbox-cell');

                let allSelected = false;
                let currentSearchTerm = '';

                // Get visible checkboxes and rows
                function getVisibleElements() {
                    const visibleRows = document.querySelectorAll('.invoice-row:not([style*="display: none"])');
                    const visibleCheckboxes = [];
                    visibleRows.forEach(row => {
                        const checkbox = row.querySelector('.invoice-checkbox');
                        if (checkbox) visibleCheckboxes.push(checkbox);
                    });
                    return {
                        visibleRows,
                        visibleCheckboxes
                    };
                }

                // Calculate total amount
                function calculateTotalAmount() {
                    let total = 0;
                    const checkboxes = document.querySelectorAll('.invoice-checkbox:checked');
                    checkboxes.forEach(checkbox => {
                        const amount = parseFloat(checkbox.getAttribute('data-amount')) || 0;
                        total += amount;
                    });
                    return total;
                }

                // Update row numbers for visible rows
                function updateRowNumbers() {
                    const visibleRows = document.querySelectorAll('.invoice-row:not([style*="display: none"])');
                    visibleRows.forEach((row, index) => {
                        const numberCell = row.querySelector('.row-number');
                        if (numberCell) {
                            numberCell.textContent = index + 1;
                        }
                    });
                }

                // Highlight search term in invoice code
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
                        const invoiceCode = row.getAttribute('data-invoice-code') || '';
                        const customerName = row.getAttribute('data-customer-name') || '';
                        const codeCell = row.querySelector('.invoice-code');

                        if (currentSearchTerm === '' ||
                            invoiceCode.includes(currentSearchTerm) ||
                            customerName.includes(currentSearchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                            hasResults = true;

                            // Highlight search term in invoice code
                            if (codeCell && currentSearchTerm) {
                                const originalCode = codeCell.textContent;
                                codeCell.innerHTML = highlightSearchTerm(originalCode, searchTerm);
                            }
                        } else {
                            row.style.display = 'none';
                            // Uncheck hidden checkboxes
                            const checkbox = row.querySelector('.invoice-checkbox');
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
                        searchResultsText.textContent = `عُثر على ${visibleCount} فاتورة من أصل ${rows.length} فاتورة`;
                        searchTerm.innerHTML = `"${searchTerm}"`;

                        if (!hasResults) {
                            noResults.style.display = 'block';
                            document.querySelector('.table-responsive').style.display = 'none';
                        } else {
                            noResults.style.display = 'none';
                            document.querySelector('.table-responsive').style.display = 'block';
                        }
                    } else {
                        searchInfo.style.display = 'none';
                        noResults.style.display = 'none';
                        document.querySelector('.table-responsive').style.display = 'block';

                        // Remove all highlighting
                        rows.forEach(row => {
                            const codeCell = row.querySelector('.invoice-code');
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
                    const totalAmount = calculateTotalAmount();

                    // Update counters
                    if (selectedCountSpan) selectedCountSpan.textContent = selectedCount;
                    if (visibleCountSpan) visibleCountSpan.textContent = totalVisibleCount;
                    if (totalAmountSpan) totalAmountSpan.textContent = totalAmount.toFixed(2);

                    // Show/hide elements based on selection
                    if (selectedCount > 0) {
                        if (selectionCounter) selectionCounter.style.display = 'block';
                        createStatementBtn.disabled = false;
                        clearSelectionBtn.style.display = 'inline-block';
                    } else {
                        if (selectionCounter) selectionCounter.style.display = 'none';
                        createStatementBtn.disabled = true;
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
                        const checkbox = row.querySelector('.invoice-checkbox');
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

                // Select/Deselect all functionality (only visible items)
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const {
                            visibleCheckboxes
                        } = getVisibleElements();

                        if (allSelected) {
                            // Deselect all visible
                            visibleCheckboxes.forEach(checkbox => {
                                checkbox.checked = false;
                            });
                        } else {
                            // Select all visible
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
                        const checkboxes = document.querySelectorAll('.invoice-checkbox');
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
                            const checkbox = this.querySelector('.invoice-checkbox');
                            if (checkbox) {
                                checkbox.checked = !checkbox.checked;
                                updateUI();

                                // Add click animation
                                const row = this.closest('.invoice-row');
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
                    const checkbox = row.querySelector('.invoice-checkbox');
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
            .invoice-row {
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .invoice-row.table-primary {
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

            #create-statement-btn:disabled {
                transform: none !important;
                opacity: 0.6;
                cursor: not-allowed;
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

            /* Search highlighting */
            mark.bg-warning {
                background-color: #fff3cd !important;
                color: #856404;
                padding: 1px 3px;
                border-radius: 3px;
                font-weight: bold;
            }

            /* Search input focus */
            #search-input:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            /* Animation classes */
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

            /* Smooth transitions for showing/hiding rows */
            .invoice-row {
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            /* Card styling */
            .card {
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                border-radius: 8px 8px 0 0 !important;
            }

            /* Total amount highlight */
            #total-amount {
                font-size: 1.1rem;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush

@endsection
