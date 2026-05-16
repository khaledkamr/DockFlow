@extends('layouts.app')

@section('title', 'إنشاء إشعار جديد')

@section('content')
    <h1 class="mb-4">إنشـــاء إشعــــار</h1>

    <!-- Main Form Container -->
    <form id="noteForm" action="{{ route('invoices.notes.store') }}" method="POST" novalidate>
        @csrf
        <div class="row g-4">
            <!-- Left Column - Form Inputs -->
            <div class="col-lg-8">

                <!-- Form Fields -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="noteType" class="form-label fw-bold">نوع الإشعار</label>
                                <select class="form-control form-control border-primary" id="noteType" name="type">
                                    <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>إشعار دائن</option>
                                    <option value="debit" {{ old('type') == 'debit' ? 'selected' : '' }}>إشعار مدين</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="noteDate" class="form-label fw-bold">التاريخ</label>
                                <input type="date" class="form-control form-control border-primary" id="noteDate" name="date"
                                    value="{{ old('date', \Carbon\Carbon::now()->toDateString()) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="noteAmount" class="form-label fw-bold">المبلغ</label>
                                <div class="input-group input-group">
                                    <input type="number" class="form-control form-control border-primary" id="noteAmount" name="amount"
                                        step="0.01" min="0" placeholder="0.00" value="{{ old('amount') }}" required>
                                    <span class="input-group-text bg-light border-primary">ر.س</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="noteReason" class="form-label fw-bold">السبب</label>
                                <textarea class="form-control border-primary" id="noteReason" name="reason" rows="1" placeholder="أدخل سبب الإشعار...">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Title and Search Row -->
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-6">
                                <h5 class="card-title mb-0">اختر الفاتورة</h5>
                            </div>
                            <div class="col-6">
                                <!-- Search Box -->
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-primary">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-primary bg-light"
                                        id="invoiceSearch" placeholder="ابحث برقم الفاتورة أو اسم العميل...">
                                </div>
                            </div>
                        </div>

                        <!-- Invoices Table -->
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="invoicesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th class="text-center">رقم الفاتورة</th>
                                        <th class="text-center">اسم العميل</th>
                                        <th class="text-center">النوع</th>
                                        <th class="text-center">التاريخ</th>
                                        <th class="text-center">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                    @foreach ($invoices as $invoice)
                                        <tr class="invoice-row text-center" data-invoice-id="{{ $invoice->id }}"
                                            data-invoice-code="{{ $invoice->code }}"
                                            data-customer-name="{{ $invoice->customer->name }}"
                                            data-invoice-type="{{ $invoice->status }}"
                                            data-tax-rate="{{ $invoice->tax_rate ?? 15 }}"
                                            data-total="{{ $invoice->total_amount }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input invoice-radio" type="radio"
                                                        name="invoice" value="{{ $invoice->id }}"
                                                        data-tax-rate="{{ $invoice->tax_rate ?? 15 }}" 
                                                        {{ old('invoice_id') == $invoice->id ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                <a href="{{ route('invoices.unified.details', $invoice) }}"
                                                    class="text-decoration-none" target="_blank">
                                                    {{ $invoice->code }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->customer->name }}</td>
                                            <td>{{ $invoice->type }}</td>
                                            <td>{{ \Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                            <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }} <i data-lucide="saudi-riyal"></i></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if ($invoices->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">لا توجد فواتير متاحة</p>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" id="invoiceIdInput" name="invoice_id" required>
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    </div>
                </div>               
            </div>

            <!-- Right Column - Summary Section -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-4">
                            <i class="fas fa-list-check me-2"></i>الملخص
                        </h5>

                        <!-- Selected Invoice Info -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <small class="text-muted d-block">الفاتورة المختارة</small>
                            <div class="d-flex justify-content-between">
                                <div class="mb-0 fw-bold text-primary" id="summaryInvoiceCode">-</div>
                                <div class="text-muted fw-bold" id="summaryInvoiceTotal">-</div>
                            </div>
                            <small class="text-muted" id="summaryCustomerName">-</small>
                        </div>

                        <!-- Tax Rate Info -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">معدل الضريبة</small>
                                <span class="badge bg-primary" id="summaryTaxRate">0%</span>
                            </div>
                        </div>

                        <hr>

                        <!-- Amount -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">المبلغ</small>
                                <span id="summaryAmount" class="fw-bold">0.00 <i data-lucide="saudi-riyal"></i></span>
                            </div>
                        </div>

                        <!-- Tax Calculation -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">الضريبة</small>
                                <span id="summaryTax" class="fw-bold text-info">0.00 <i data-lucide="saudi-riyal"></i></span>
                            </div>
                        </div>

                        <hr>

                        <!-- Total -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">الإجمالي</span>
                                <span id="summaryTotal" class="h5 text-primary mb-0 fw-bold">0.00 <i data-lucide="saudi-riyal"></i></span>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Calculations -->
                        <input type="hidden" id="taxInput" name="tax" value="0">
                        <input type="hidden" id="totalInput" name="total" value="0">
                        <input type="hidden" id="taxRateInput" name="tax_rate" value="0">

                        <!-- Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-check me-2"></i>حفظ الإشعار
                            </button>
                            <a href="" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover: #0056b3;
        }

        body {
            direction: rtl;
            text-align: right;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15) !important;
        }

        .invoice-row {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .invoice-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .invoice-row.selected {
            background-color: rgba(0, 123, 255, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .btn-group .btn-check:checked+.btn-outline-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-group .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-group .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 10;
        }

        .input-group-text {
            border-color: #dee2e6;
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sticky-top {
                position: static;
                margin-top: 2rem;
            }

            h1.h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .table {
                font-size: 0.875rem;
            }

            .btn {
                font-size: 0.875rem;
            }

            .card {
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const noteForm = document.getElementById('noteForm');
            const noteTypeRadios = document.querySelectorAll('input[name="noteType"]');
            const noteTypeInput = document.getElementById('noteTypeInput');
            const invoiceRadios = document.querySelectorAll('.invoice-radio');
            const invoiceIdInput = document.getElementById('invoiceIdInput');
            const noteAmountInput = document.getElementById('noteAmount');
            const noteReasonInput = document.getElementById('noteReason');
            const noteDateInput = document.getElementById('noteDate');
            const invoiceSearch = document.getElementById('invoiceSearch');
            const invoicesTableBody = document.getElementById('invoicesTableBody');
            const validationAlert = document.getElementById('validationAlert');
            const validationMessage = document.getElementById('validationMessage');

            // Summary elements
            const summaryInvoiceCode = document.getElementById('summaryInvoiceCode');
            const summaryInvoiceTotal = document.getElementById('summaryInvoiceTotal');
            const summaryCustomerName = document.getElementById('summaryCustomerName');
            const summaryTaxRate = document.getElementById('summaryTaxRate');
            const summaryAmount = document.getElementById('summaryAmount');
            const summaryTax = document.getElementById('summaryTax');
            const summaryTotal = document.getElementById('summaryTotal');
            const taxInput = document.getElementById('taxInput');
            const totalInput = document.getElementById('totalInput');
            const taxRateInput = document.getElementById('taxRateInput');

            // Handle note type selection
            noteTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    noteTypeInput.value = this.value;
                });
            });

            // Handle invoice selection
            invoiceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        // Remove previous selection highlight
                        document.querySelectorAll('.invoice-row').forEach(row => {
                            row.classList.remove('selected');
                            row.classList.remove('table-primary');
                        });

                        // Highlight selected row
                        this.closest('.invoice-row').classList.add('selected');
                        this.closest('.invoice-row').classList.add('table-primary');

                        // Update hidden input
                        invoiceIdInput.value = this.value;

                        // Get invoice data
                        const row = this.closest('.invoice-row');
                        const invoiceCode = row.dataset.invoiceCode;
                        const invoiceTotal = row.dataset.total;
                        const customerName = row.dataset.customerName;
                        const taxRate = row.dataset.taxRate;

                        // Update summary
                        summaryInvoiceCode.textContent = invoiceCode;
                        summaryInvoiceTotal.textContent = 'بمبلغ ' + invoiceTotal;
                        summaryCustomerName.textContent = customerName;
                        summaryTaxRate.textContent = taxRate + '%';
                        taxRateInput.value = taxRate;

                        updateCalculations();
                    }
                });
            });

            // Handle amount input for auto-calculation
            noteAmountInput.addEventListener('input', updateCalculations);

            // Handle invoice search
            invoiceSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.invoice-row');

                rows.forEach(row => {
                    const code = row.dataset.invoiceCode.toLowerCase();
                    const customerName = row.dataset.customerName.toLowerCase();

                    if (code.includes(searchTerm) || customerName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Calculate tax and total
            function updateCalculations() {
                const amount = parseFloat(noteAmountInput.value) || 0;
                const taxRate = parseFloat(taxRateInput.value) || 0;

                const tax = (amount * taxRate) / 100;
                const total = amount + tax;

                // Update summary
                summaryAmount.textContent = amount.toFixed(2) + ' ر.س';
                summaryTax.textContent = tax.toFixed(2) + ' ر.س';
                summaryTotal.textContent = total.toFixed(2) + ' ر.س';

                // Update hidden inputs
                taxInput.value = tax.toFixed(2);
                totalInput.value = total.toFixed(2);
            }

            // Initialize calculations
            updateCalculations();
        });
    </script>
@endsection
