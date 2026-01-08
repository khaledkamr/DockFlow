@extends('layouts.app')

@section('title', 'انشاء فاتورة مصاريف')

@section('content')
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

        input.form-control {
            height: 38px;
            border-radius: 8px;
            border: 1px solid #0d6efd;
            padding: 5px;
        }
    </style>

    <h1 class="mb-4">انشاء فاتورة مصاريف</h1>

    <form action="{{ route('expense.invoices.store') }}" method="POST" id="expenseInvoiceForm" class="mb-5">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    بيانات الفاتورة
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label for="supplier_id" class="form-label fw-bold">
                            المورد <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id" id="supplier_id" class="form-select border-primary" required>
                            <option value="">-- اختر المورد --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="supplier_invoice_number" class="form-label fw-bold">
                            رقم فاتورة المورد
                        </label>
                        <input type="text" name="supplier_invoice_number" id="supplier_invoice_number"
                            class="form-control border-primary">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="date" class="form-label fw-bold">
                            تاريخ الفاتورة <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" id="date" class="form-control border-primary"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-bold ms-2">
                            ضريبة القيمة المضافة (15%)
                        </label>
                        <div class="input-group">
                            <div class="input-group-text border-primary">
                                <input class="form-check-input border-primary mt-0" type="checkbox" checked
                                    id="tax_checkbox">
                            </div>
                            <input type="number" name="tax_rate" id="tax_rate_input" class="form-control border-primary"
                                value="15" readonly>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold">
                <i class="fas fa-list me-2"></i>
                بنود الفاتورة
            </h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm fw-bold d-none" id="showPoliciesBtn"
                    data-bs-toggle="modal" data-bs-target="#supplierPoliciesModal">
                    <i class="fas fa-file-alt me-1"></i>
                    عرض بوالص المورد
                </button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" id="addRowBtn">
                    <i class="fas fa-plus me-1"></i>
                    إضافة بند
                </button>
            </div>
        </div>

        <div class="table-container mb-4">
            <table class="table table-hover mb-0" id="itemsTable">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center text-nowrap" style="width: 50px;">#</th>
                        <th class="text-center text-nowrap" style="min-width: 300px;">البند</th>
                        <th class="text-center text-nowrap" style="min-width: 300px;">البيان</th>
                        <th class="text-center text-nowrap" style="min-width: 100px;">الكمية</th>
                        <th class="text-center text-nowrap" style="min-width: 120px;">السعر</th>
                        <th class="text-center text-nowrap" style="min-width: 120px;">السعر شامل الضريبة</th>
                        <th class="text-center text-nowrap" style="min-width: 300px;">مركز التكلفة</th>
                        <th class="text-center text-nowrap" style="min-width: 120px;">المبلغ</th>
                        <th class="text-center text-nowrap" style="min-width: 100px;">الضريبة</th>
                        <th class="text-center text-nowrap" style="min-width: 120px;">الإجمالي</th>
                        <th class="text-center text-nowrap" style="min-width: 80px;">إجراء</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- Dynamic rows will be added here -->
                </tbody>
            </table>
        </div>

        <!-- Payment Details Section -->
        <div class="card border-dark mb-3 mt-4" id="payment-details">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    ملخص الفاتورة
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="payment_method" class="form-label fw-bold">
                            طريقة الدفع <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" class="form-select border-primary" required>
                            @foreach ($payment_methods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-bold">حساب الدفع</label>
                        <select name="expense_account_id" id="expense_account" class="form-select border-primary">
                            <option value="">-- اختر حساب الدفع --</option>
                            @foreach ($expenseAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3">
                        <label for="discount" class="form-label fw-bold">
                            نسبة الخصم (%)
                        </label>
                        <input type="number" name="discount_rate" id="discount_rate"
                            class="form-control border-primary" min="0" max="100" step="0.01"
                            value="0" placeholder="0.00" required>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3">
                        <label for="discount" class="form-label fw-bold">
                            قيمة الخصم (ريال)
                        </label>
                        <input type="number" name="discount" id="discount" class="form-control border-primary"
                            placeholder="0" min="0" step="0.01" value="0">
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-primary bg-light">
                            <div class="card-body">
                                <div class="row text-center g-3">
                                    <div class="col-6 col-sm-3 col-md-3">
                                        <div class="p-2">
                                            <small class="text-muted d-block">إجمالي قبل الضريبة</small>
                                            <div class="d-flex justify-content-center align-items-center mb-1">
                                                <h4 class="text-primary mb-0" id="totalBeforeTax">0.00</h4>
                                                <i data-lucide="saudi-riyal" class="text-primary ms-1"></i>
                                            </div>
                                            <input type="hidden" name="amount_before_tax" id="amount_before_tax_input"
                                                value="0">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3 col-md-3">
                                        <div class="p-2">
                                            <small class="text-muted d-block">الضريبة المضافة</small>
                                            <div class="d-flex justify-content-center align-items-center mb-1">
                                                <h4 class="text-primary mb-0" id="totalTax">0.00</h4>
                                                <i data-lucide="saudi-riyal" class="text-primary ms-1"></i>
                                            </div>
                                            <input type="hidden" name="tax" id="tax_input" value="0">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3 col-md-3">
                                        <div class="p-2">
                                            <small class="text-muted d-block">قيمة الخصم</small>
                                            <div class="d-flex justify-content-center align-items-center mb-1">
                                                <h4 class="text-danger mb-0" id="totalDiscount">0.00</h4>
                                                <i data-lucide="saudi-riyal" class="text-danger ms-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3 col-md-3">
                                        <div class="p-2">
                                            <small class="text-muted d-block">المبلغ النهائي</small>
                                            <div class="d-flex justify-content-center align-items-center mb-1">
                                                <h4 class="text-success fw-bold mb-0" id="totalAmount">0.00</h4>
                                                <i data-lucide="saudi-riyal" class="text-success ms-1"></i>
                                            </div>
                                            <input type="hidden" name="total_amount" id="total_amount_input"
                                                value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label for="notes" class="form-label fw-bold">
                        <i class="fas fa-note-sticky me-1"></i>
                        ملاحظات
                    </label>
                    <textarea name="notes" id="notes" class="form-control border-primary" rows="3"
                        placeholder="أضف ملاحظات إضافية إن وجدت"></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-bold" id="create-invoice-btn">
            <i class="fas fa-plus me-1"></i>
            إنشاء فاتورة
        </button>
    </form>

    <!-- Supplier Policies Modal -->
    <div class="modal fade" id="supplierPoliciesModal" tabindex="-1" aria-labelledby="supplierPoliciesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="supplierPoliciesModalLabel">
                        <i class="fas fa-file-alt me-2"></i>
                        بوالص نقل المورد غير المدفوعة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="policiesLoadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل البوالص...</p>
                    </div>
                    <div id="noPoliciesMessage" class="text-center py-4 d-none">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد بوالص غير مدفوعة لهذا المورد</p>
                    </div>
                    <div id="policiesTableContainer" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <input type="checkbox" class="form-check-input" id="selectAllPolicies">
                                        </th>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th class="text-center">الكود</th>
                                        <th class="text-center">اسم العميل</th>
                                        <th class="text-center">من</th>
                                        <th class="text-center">إلى</th>
                                        <th class="text-center">تكلفة المورد</th>
                                        <th class="text-center">حالة الدفع</th>
                                    </tr>
                                </thead>
                                <tbody id="policiesTableBody">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-6">
                                    <span class="fw-bold">البوالص المحددة: </span>
                                    <span id="selectedPoliciesCount" class="badge bg-primary">0</span>
                                </div>
                                <div class="col-12 col-md-6 text-md-end mt-2 mt-md-0">
                                    <span class="fw-bold">إجمالي المبلغ: </span>
                                    <span id="selectedPoliciesTotal" class="text-success fw-bold">0.00</span>
                                    <i data-lucide="saudi-riyal" class="text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary fw-bold" id="addSelectedPoliciesBtn" disabled>
                        <i class="fas fa-plus me-1"></i>
                        إضافة البوالص المحددة
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let rowCounter = 0;
            const accounts = @json($accounts);
            const costCenters = @json($costCenters);
            let supplierPolicies = [];
            let supplierSettlementAccount = null;

            $('#payment_method').select2({
                placeholder: "ابحث عن طريقة الدفع...",
                allowClear: true
            });

            $('#expense_account').select2({
                placeholder: "ابحث عن حساب الدفع...",
                allowClear: true
            });

            // Add first row on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2 for supplier
                $('#supplier_id').select2({
                    placeholder: "ابحث عن المورد...",
                    allowClear: true
                });

                // Handle supplier selection change
                $('#supplier_id').on('change', function() {
                    const supplierId = $(this).val();
                    const showPoliciesBtn = document.getElementById('showPoliciesBtn');

                    if (supplierId) {
                        showPoliciesBtn.classList.remove('d-none');
                        fetchSupplierPolicies(supplierId);
                    } else {
                        showPoliciesBtn.classList.add('d-none');
                        supplierPolicies = [];
                        supplierSettlementAccount = null;
                    }
                });

                // addRow();
            });

            // Fetch supplier policies
            function fetchSupplierPolicies(supplierId) {
                const url = `/expense/invoices/supplier/${supplierId}/policies`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        supplierPolicies = data.policies;
                        supplierSettlementAccount = data.settlement_account;
                    })
                    .catch(error => {
                        console.error('Error fetching supplier policies:', error);
                        supplierPolicies = [];
                        supplierSettlementAccount = null;
                    });
            }

            // Show supplier policies in modal
            document.getElementById('supplierPoliciesModal').addEventListener('show.bs.modal', function() {
                renderPoliciesTable();
            });

            function renderPoliciesTable() {
                const loadingSpinner = document.getElementById('policiesLoadingSpinner');
                const noPoliciesMessage = document.getElementById('noPoliciesMessage');
                const policiesTableContainer = document.getElementById('policiesTableContainer');
                const policiesTableBody = document.getElementById('policiesTableBody');

                loadingSpinner.classList.add('d-none');

                if (supplierPolicies.length === 0) {
                    noPoliciesMessage.classList.remove('d-none');
                    policiesTableContainer.classList.add('d-none');
                    return;
                }

                noPoliciesMessage.classList.add('d-none');
                policiesTableContainer.classList.remove('d-none');

                policiesTableBody.innerHTML = supplierPolicies.map((policy, index) => `
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input policy-checkbox" 
                                data-id="${policy.id}" 
                                data-type="${policy.type}"
                                data-code="${policy.code}"
                                data-customer="${policy.customer_name}"
                                data-from="${policy.from}"
                                data-to="${policy.to}"
                                data-cost="${policy.supplier_cost}">
                        </td>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center fw-bold text-primary">${policy.code}</td>
                        <td class="text-center">${policy.customer_name}</td>
                        <td class="text-center"><i class="fa-solid fa-location-dot text-danger"></i> ${policy.from}</td>
                        <td class="text-center"><i class="fa-solid fa-location-dot text-danger"></i> ${policy.to}</td>
                        <td class="text-center fw-bold">${parseFloat(policy.supplier_cost).toFixed(2)}</td>
                        <td class="text-center">
                            <span class="badge bg-danger">غير مدفوع</span>
                        </td>
                    </tr>
                `).join('');

                // Reset select all checkbox
                document.getElementById('selectAllPolicies').checked = false;
                updateSelectedPoliciesInfo();

                // Re-initialize lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // Select all policies checkbox
            document.getElementById('selectAllPolicies').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.policy-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedPoliciesInfo();
            });

            // Individual checkbox change
            document.getElementById('policiesTableBody').addEventListener('change', function(e) {
                if (e.target.classList.contains('policy-checkbox')) {
                    updateSelectedPoliciesInfo();

                    // Update select all checkbox state
                    const allCheckboxes = document.querySelectorAll('.policy-checkbox');
                    const checkedCheckboxes = document.querySelectorAll('.policy-checkbox:checked');
                    document.getElementById('selectAllPolicies').checked = allCheckboxes.length === checkedCheckboxes
                        .length;
                }
            });

            function updateSelectedPoliciesInfo() {
                const checkedCheckboxes = document.querySelectorAll('.policy-checkbox:checked');
                const count = checkedCheckboxes.length;
                let total = 0;

                checkedCheckboxes.forEach(cb => {
                    total += parseFloat(cb.dataset.cost) || 0;
                });

                document.getElementById('selectedPoliciesCount').textContent = count;
                document.getElementById('selectedPoliciesTotal').textContent = total.toFixed(2);
                document.getElementById('addSelectedPoliciesBtn').disabled = count === 0;
            }

            // Add selected policies as invoice items
            document.getElementById('addSelectedPoliciesBtn').addEventListener('click', function() {
                const checkedCheckboxes = document.querySelectorAll('.policy-checkbox:checked');

                if (checkedCheckboxes.length === 0) {
                    alert('الرجاء تحديد بوليصة واحدة على الأقل');
                    return;
                }

                if (!supplierSettlementAccount) {
                    alert('لم يتم تحديد حساب تسوية للمورد. الرجاء تحديث بيانات المورد أولاً.');
                    return;
                }

                checkedCheckboxes.forEach(cb => {
                    const policyData = {
                        id: cb.dataset.id,
                        type: cb.dataset.type,
                        code: cb.dataset.code,
                        customer: cb.dataset.customer,
                        from: cb.dataset.from,
                        to: cb.dataset.to,
                        cost: parseFloat(cb.dataset.cost)
                    };

                    addPolicyAsInvoiceItem(policyData);
                });

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('supplierPoliciesModal'));
                modal.hide();
            });

            function addPolicyAsInvoiceItem(policyData) {
                rowCounter++;
                const tbody = document.getElementById('itemsTableBody');
                const row = document.createElement('tr');
                row.id = `row-${rowCounter}`;

                const typeLabel = policyData.type === 'shipping_policy' ? 'بوليصة شحن' : 'إشعار نقل';
                const description =
                    `${typeLabel} رقم ${policyData.code}`;

                const taxRateChecked = document.getElementById('tax_checkbox').checked;
                const amount = policyData.cost;
                const tax = taxRateChecked ? amount * 0.15 : 0;
                const amountAfterTax = amount + tax;
                const itemAmount = amount;
                const total = amountAfterTax;

                row.innerHTML = `
                    <td class="text-center align-middle">
                        <span class="row-number fw-bold">${rowCounter}</span>
                    </td>
                    <td>
                        <select name="items[${rowCounter}][account_id]" class="form-select form-select-sm account-select" id="account-${rowCounter}" required>
                            <option value="">-- اختر البند --</option>
                            ${accounts.map(acc => `<option value="${acc.id}" ${acc.id == supplierSettlementAccount.id ? 'selected' : ''}>${acc.name}</option>`).join('')}
                        </select>
                        <input type="hidden" name="items[${rowCounter}][policy_id]" value="${policyData.id}">
                        <input type="hidden" name="items[${rowCounter}][policy_type]" value="${policyData.type}">
                    </td>
                    <td>
                        <input type="text" name="items[${rowCounter}][description]" 
                            class="form-control form-control-sm" value="${description}">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][quantity]" 
                            class="form-control form-control-sm quantity" 
                            placeholder="1" min="1" step="any" value="1" required 
                            oninput="calculateRow(${rowCounter})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][price]"
                            class="form-control form-control-sm amount" 
                            placeholder="0.00" min="0" step="any" value="${amount.toFixed(2)}" required 
                            oninput="onAmountChange(${rowCounter})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][price_with_tax]" 
                            class="form-control form-control-sm amount-after-tax" 
                            placeholder="0.00" min="0" step="any" value="${amountAfterTax.toFixed(2)}" required 
                            oninput="onAmountAfterTaxChange(${rowCounter})">
                    </td>
                    <td>
                        <select name="items[${rowCounter}][cost_center_id]" class="form-select form-select-sm cost-center-select" id="cost-center-${rowCounter}">
                            <option value="">-- اختياري --</option>
                            ${costCenters.map(cc => `<option value="${cc.id}">${cc.name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][amount]" 
                            class="form-control form-control-sm item-amount" 
                            placeholder="0.00" value="${itemAmount.toFixed(2)}" step="any">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][tax]" 
                            class="form-control form-control-sm tax" 
                            placeholder="0.00" value="${tax.toFixed(2)}" step="any">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][total_amount]" 
                            class="form-control form-control-sm total-amount" 
                            placeholder="0.00" value="${total.toFixed(2)}" step="any">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowCounter})">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                // Initialize Select2 for the new row
                $(`#account-${rowCounter}`).select2({
                    placeholder: "ابحث عن البند...",
                    allowClear: true,
                    width: '100%'
                });

                $(`#cost-center-${rowCounter}`).select2({
                    placeholder: "ابحث عن مركز التكلفة...",
                    allowClear: true,
                    width: '100%'
                });

                updateRowNumbers();
                calculateTotals();
            }

            // Add Row Function
            document.getElementById('addRowBtn').addEventListener('click', function() {
                addRow();
            });

            function addRow() {
                rowCounter++;
                const tbody = document.getElementById('itemsTableBody');
                const row = document.createElement('tr');
                row.id = `row-${rowCounter}`;
                row.innerHTML = `
                    <td class="text-center align-middle">
                        <span class="row-number fw-bold">${rowCounter}</span>
                    </td>
                    <td>
                        <select name="items[${rowCounter}][account_id]" class="form-select form-select-sm account-select" id="account-${rowCounter}" required>
                            <option value="">-- اختر البند --</option>
                            ${accounts.map(acc => `<option value="${acc.id}">${acc.name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="items[${rowCounter}][description]" 
                            class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][quantity]" 
                            class="form-control form-control-sm quantity" 
                            placeholder="1" min="1" step="any" value="1" required 
                            oninput="calculateRow(${rowCounter})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][price]"
                            class="form-control form-control-sm amount" 
                            placeholder="0.00" min="0" step="any" value="0" required 
                            oninput="onAmountChange(${rowCounter})">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][price_with_tax]" 
                            class="form-control form-control-sm amount-after-tax" 
                            placeholder="0.00" min="0" step="any" value="0" required 
                            oninput="onAmountAfterTaxChange(${rowCounter})">
                    </td>
                    <td>
                        <select name="items[${rowCounter}][cost_center_id]" class="form-select form-select-sm cost-center-select" id="cost-center-${rowCounter}">
                            <option value="">-- اختياري --</option>
                            ${costCenters.map(cc => `<option value="${cc.id}">${cc.name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][amount]" 
                            class="form-control form-control-sm item-amount" 
                            placeholder="0.00" value="0" step="any">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][tax]" 
                            class="form-control form-control-sm tax" 
                            placeholder="0.00" value="0" step="any">
                    </td>
                    <td>
                        <input type="number" name="items[${rowCounter}][total_amount]" 
                            class="form-control form-control-sm total-amount" 
                            placeholder="0.00" value="0" step="any">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowCounter})">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                // Initialize Select2 for the new row
                $(`#account-${rowCounter}`).select2({
                    placeholder: "ابحث عن البند...",
                    allowClear: true,
                    width: '100%'
                });

                $(`#cost-center-${rowCounter}`).select2({
                    placeholder: "ابحث عن مركز التكلفة...",
                    allowClear: true,
                    width: '100%'
                });

                updateRowNumbers();
            }

            function deleteRow(rowId) {
                const rowCount = document.querySelectorAll('#itemsTableBody tr').length;
                document.getElementById(`row-${rowId}`).remove();
                updateRowNumbers();
                calculateTotals();
            }

            function updateRowNumbers() {
                const rows = document.querySelectorAll('#itemsTableBody tr');
                rows.forEach((row, index) => {
                    const numberSpan = row.querySelector('.row-number');
                    if (numberSpan) {
                        numberSpan.textContent = index + 1;
                    }
                });
            }

            function onAmountChange(rowId) {
                const row = document.getElementById(`row-${rowId}`);
                const amount = parseFloat(row.querySelector('.amount').value) || 0;
                const taxRateChecked = document.getElementById('tax_checkbox').checked;

                let tax = 0;
                if (taxRateChecked) {
                    tax = amount * 0.15;
                }

                const amountAfterTax = amount + tax;

                row.querySelector('.tax').value = tax.toFixed(2);
                row.querySelector('.amount-after-tax').value = amountAfterTax.toFixed(2);

                calculateRow(rowId);
            }

            function onAmountAfterTaxChange(rowId) {
                const row = document.getElementById(`row-${rowId}`);
                const amountAfterTax = parseFloat(row.querySelector('.amount-after-tax').value) || 0;
                const taxRateChecked = document.getElementById('tax_checkbox').checked;

                let amount, tax;
                if (taxRateChecked) {
                    // amount_after_tax = amount + (amount * 0.15)
                    // amount_after_tax = amount * 1.15
                    amount = amountAfterTax / 1.15;
                    tax = amount * 0.15;
                } else {
                    amount = amountAfterTax;
                    tax = 0;
                }

                row.querySelector('.amount').value = amount.toFixed(2);
                row.querySelector('.tax').value = tax.toFixed(2);

                calculateRow(rowId);
            }

            function calculateRow(rowId) {
                const row = document.getElementById(`row-${rowId}`);
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.amount').value) || 0;
                const amountAfterTax = parseFloat(row.querySelector('.amount-after-tax').value) || 0;

                // Calculate amount before tax (price * quantity)
                const itemAmount = price * quantity;
                row.querySelector('.item-amount').value = itemAmount.toFixed(2);

                // Calculate total (price_with_tax * quantity)
                const total = amountAfterTax * quantity;
                row.querySelector('.total-amount').value = total.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                const rows = document.querySelectorAll('#itemsTableBody tr');
                let totalBeforeTax = 0;
                let totalTax = 0;
                let totalAmount = 0;

                rows.forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const amount = parseFloat(row.querySelector('.amount').value) || 0;
                    const tax = parseFloat(row.querySelector('.tax').value) || 0;
                    const total = parseFloat(row.querySelector('.total-amount').value) || 0;

                    totalBeforeTax += (amount * quantity);
                    totalTax += (tax * quantity);
                    totalAmount += total;
                });

                const discount = parseFloat(document.getElementById('discount').value) || 0;
                const finalTotal = totalAmount - discount;

                document.getElementById('totalBeforeTax').textContent = totalBeforeTax.toFixed(2);
                document.getElementById('totalTax').textContent = totalTax.toFixed(2);
                document.getElementById('totalDiscount').textContent = discount.toFixed(2);
                document.getElementById('totalAmount').textContent = finalTotal.toFixed(2);

                // Update hidden inputs
                document.getElementById('tax_input').value = totalTax.toFixed(2);
                document.getElementById('amount_before_tax_input').value = totalBeforeTax.toFixed(2);
                document.getElementById('total_amount_input').value = finalTotal.toFixed(2);
            }

            // Tax Rate Checkbox Change
            document.getElementById('tax_checkbox').addEventListener('input', function() {
                const isChecked = this.checked;
                const taxRateInput = document.getElementById('tax_rate_input');

                // Update hidden tax_rate input
                taxRateInput.value = isChecked ? 15 : 0;

                // Recalculate all rows
                const rows = document.querySelectorAll('#itemsTableBody tr');
                rows.forEach((row, index) => {
                    const rowId = row.id.split('-')[1];
                    onAmountChange(rowId);
                });
            });

            // Discount Rate Change
            document.getElementById('discount_rate').addEventListener('input', function() {
                const discountRate = parseFloat(this.value) || 0;
                const totalBeforeTax = parseFloat(document.getElementById('totalBeforeTax').textContent) || 0;
                const totalTax = parseFloat(document.getElementById('totalTax').textContent) || 0;
                const subtotal = totalBeforeTax + totalTax;

                const discount = (subtotal * discountRate) / 100;
                document.getElementById('discount').value = discount.toFixed(2);

                calculateTotals();
            });

            // Discount Amount Change
            document.getElementById('discount').addEventListener('input', function() {
                const discount = parseFloat(this.value) || 0;
                const totalBeforeTax = parseFloat(document.getElementById('totalBeforeTax').textContent) || 0;
                const totalTax = parseFloat(document.getElementById('totalTax').textContent) || 0;
                const subtotal = totalBeforeTax + totalTax;

                const discountRate = subtotal > 0 ? (discount / subtotal) * 100 : 0;
                document.getElementById('discount_rate').value = discountRate.toFixed(2);

                calculateTotals();
            });

            // Form Validation
            document.getElementById('expenseInvoiceForm').addEventListener('submit', function(e) {
                const rowCount = document.querySelectorAll('#itemsTableBody tr').length;
                if (rowCount === 0) {
                    e.preventDefault();
                    alert('يجب إضافة بند واحد على الأقل');
                    return false;
                }
            });
        </script>
    @endpush

@endsection
