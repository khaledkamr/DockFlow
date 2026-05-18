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

<form action="{{ route('invoices.store.unified') }}" method="POST" id="accountingInvoiceForm" class="mb-5">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
    <input type="hidden" name="type" value="محاسبية">
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
                    <label for="customer_id" class="form-label fw-bold">العميل</label>
                    <select class="form-select border-primary" id="customer_id" name="customer_id" required>
                        <option value="">اختر العميل</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label for="invoice_date" class="form-label fw-bold">التاريخ</label>
                    <input type="date" class="form-control border-primary" id="invoice_date" name="date"
                        value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label for="due_date" class="form-label fw-bold">تاريخ الاستحقاق</label>
                    <input type="date" class="form-control border-primary" id="due_date" name="due_date"
                        value="{{ old('due_date', Carbon\Carbon::now()->addDays(15)->format('Y-m-d')) }}" required>
                    @error('due_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
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
        <button type="button" class="btn btn-primary btn-sm fw-bold" id="addRowBtn">
            <i class="fas fa-plus me-1"></i>
            إضافة بند
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0" id="itemsTable">
            <thead class="table-dark">
                <tr>
                    <th class="text-center text-nowrap">#</th>
                    <th class="text-center text-nowrap">المنتج</th>
                    <th class="text-center text-nowrap">الكمية</th>
                    <th class="text-center text-nowrap">السعر</th>
                    <th class="text-center text-nowrap">الضريبة</th>
                    <th class="text-center text-nowrap">السعر بعد الضريبة</th>
                    <th class="text-center text-nowrap">الإجمالي</th>
                    <th class="text-center text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="itemsTableBody">
                <!-- Dynamic rows will be added here -->
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="6" class="text-end fw-bold">الإجمالي:</td>
                    <td class="text-center fw-bold">
                        <span id="totalAmount">0.00</span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
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
                <div class="col-12 col-md-3">
                    <label for="payment_method" class="form-label fw-bold">طريقة الدفع</label>
                    <select class="form-select border-primary" id="payment_method" name="payment_method" required>
                        @foreach ($payment_methods as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md">
                    <label for="invoice-type" class="form-label fw-bold">نوع الفاتورة</label>
                    <select class="form-select border-primary" id="invoice-type" name="invoice_type" required>
                        <option value="ضريبية">فاتورة ضريبية</option>
                        <option value="مسودة">فاتورة مسودة</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-bold">نسبة الخصم (%)</label>
                    <input type="number" class="form-control border-primary" id="discount_rate" name="discount" step="0.01"
                        value="0" min="0">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-bold">مبلغ الخصم</label>
                    <input type="number" class="form-control border-primary" id="discount" name="discount_amount" step="0.01"
                        value="0" min="0">
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-3 mt-3">
                <div class="col-6 col-md">
                    <div class="p-3 bg-light rounded border border-primary text-center">
                        <small class="text-muted">المبلغ قبل الخصم</small>
                        <h6 class="mb-0" id="amount-before-tax">0.00 ر.س</h6>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-3 bg-light rounded border border-danger text-center">
                        <small class="text-muted">الخصم</small>
                        <h6 class="mb-0" id="discount-value">0.00 ر.س</h6>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-3 bg-light rounded border border-primary text-center">
                        <small class="text-muted">المبلغ بعد الخصم</small>
                        <h6 class="mb-0" id="amount-after-discount">0.00 ر.س</h6>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-3 bg-light rounded border border-primary text-center">
                        <small class="text-muted">الضريبة المضافة</small>
                        <h6 class="mb-0" id="tax-amount">0.00 ر.س</h6>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-3 bg-primary text-white rounded fw-bold text-center">
                        <small>إجمالي المبلغ</small>
                        <h6 class="fw-bold mb-0" id="total-amount">0.00 ر.س</h6>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <label for="notes" class="form-label fw-bold">
                    <i class="fas fa-sticky-note me-1"></i>
                    ملاحظات
                </label>
                <textarea name="notes" id="notes" class="form-control border-primary" rows="3"
                    placeholder="أضف أي ملاحظات على الفاتورة...">{{ old('notes') }}</textarea>
            </div>

            <!-- Hidden inputs for totals -->
            <input type="hidden" id="tax_input" name="tax_amount" value="0">
            <input type="hidden" id="amount_before_tax_input" name="amount_before_tax" value="0">
            <input type="hidden" id="total_amount_input" name="total_amount" value="0">
        </div>
    </div>

    <button type="submit" class="btn btn-primary px-4 fw-bold" id="create-invoice-btn">
        <i class="fas fa-plus me-1"></i>
        إنشاء فاتورة
    </button>
</form>

<script>
    let rowCounter = 0;
    const products = @json($products);
    const taxRate = 0.15; // 15% tax rate

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for customer
        $('#customer_id').select2({
            placeholder: "ابحث عن العميل...",
            allowClear: true
        });

        // Add first row on page load
        addRow();

        // Initialize totals display
        calculateTotals();
    });

    // Add Row Function
    document.getElementById('addRowBtn').addEventListener('click', function() {
        addRow();
    });

    function addRow() {
        rowCounter++;
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.id = `row-${rowCounter}`;

        const productOptions = products.map(p =>
            `<option value="${p.id}">${p.name_ar}</option>`
        ).join('');

        row.innerHTML = `
            <td class="text-center">
                <span class="row-number fw-bold">${rowCounter}</span>
            </td>
            <td>
                <select class="form-select form-select-sm border-primary product-select" 
                    id="product-${rowCounter}" name="products[${rowCounter}][product_id]" required>
                    <option value="">اختر المنتج</option>
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm border-primary quantity" 
                    id="quantity-${rowCounter}" name="products[${rowCounter}][quantity]" 
                    value="1" min="1" step="0.01" required 
                    onInput="calculateRow(${rowCounter})">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm border-primary amount" 
                    id="amount-${rowCounter}" name="products[${rowCounter}][price]" 
                    value="0.00" min="0" step="0.01" required 
                    onInput="onAmountChange(${rowCounter})">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm bg-light tax" 
                    id="tax-${rowCounter}" name="products[${rowCounter}][tax]" 
                    value="0.00" readonly>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm border-primary amount-after-tax" 
                    id="amount-after-tax-${rowCounter}" name="products[${rowCounter}][price_after_tax]" 
                    value="0.00" min="0" step="0.01" required 
                    onInput="onAmountAfterTaxChange(${rowCounter})">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm bg-light total-amount" 
                    id="total-amount-${rowCounter}" name="products[${rowCounter}][total]" 
                    value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(${rowCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // Initialize Select2 for the new product select
        $(`#product-${rowCounter}`).select2({
            placeholder: "ابحث عن المنتج...",
            allowClear: true,
            width: '100%'
        });

        updateRowNumbers();
    }

    function deleteRow(rowId) {
        const rowCount = document.querySelectorAll('#itemsTableBody tr').length;
        if (rowCount > 1) {
            document.getElementById(`row-${rowId}`).remove();
            updateRowNumbers();
            calculateTotals();
        } else {
            alert('يجب أن تكون هناك بند واحد على الأقل');
        }
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function onAmountChange(rowId) {
        const row = document.getElementById(`row-${rowId}`);
        const amount = parseFloat(row.querySelector('.amount').value) || 0;
        const isTaxChecked = document.getElementById('tax_checkbox').checked;

        const tax = isTaxChecked ? amount * taxRate : 0;
        const amountAfterTax = amount + tax;

        row.querySelector('.tax').value = tax.toFixed(2);
        row.querySelector('.amount-after-tax').value = amountAfterTax.toFixed(2);

        calculateRow(rowId);
    }

    function onAmountAfterTaxChange(rowId) {
        const row = document.getElementById(`row-${rowId}`);
        const isTaxChecked = document.getElementById('tax_checkbox').checked;
        const amountAfterTax = parseFloat(row.querySelector('.amount-after-tax').value) || 0;

        let amount, tax;
        if (isTaxChecked) {
            amount = amountAfterTax / (1 + taxRate);
            tax = amountAfterTax - amount;
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
        const priceAfterTax = parseFloat(row.querySelector('.amount-after-tax').value) || 0;

        const total = priceAfterTax * quantity;
        row.querySelector('.total-amount').value = total.toFixed(2);

        calculateTotals();
    }

    function calculateTotals() {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        let subtotalBeforeTax = 0; // Sum of (price * quantity)
        let totalTaxAmount = 0; // Sum of (tax * quantity)
        let totalAmountBeforeDiscount = 0; // subtotal + tax

        rows.forEach(row => {
            const price = parseFloat(row.querySelector('.amount').value) || 0;
            const tax = parseFloat(row.querySelector('.tax').value) || 0;
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;

            subtotalBeforeTax += price * quantity;
            totalTaxAmount += tax * quantity;
        });

        // Amount before discount = subtotal + tax
        totalAmountBeforeDiscount = subtotalBeforeTax + totalTaxAmount;

        // Get discount value
        const discountValue = parseFloat(document.getElementById('discount').value) || 0;

        // Amount after discount
        const amountAfterDiscount = totalAmountBeforeDiscount - discountValue;

        // Final total
        const finalTotal = amountAfterDiscount;

        // Update summary section display
        document.getElementById('amount-before-tax').textContent = totalAmountBeforeDiscount.toFixed(2) + ' ر.س';
        document.getElementById('discount-value').textContent = discountValue.toFixed(2) + ' ر.س';
        document.getElementById('amount-after-discount').textContent = amountAfterDiscount.toFixed(2) + ' ر.س';
        document.getElementById('tax-amount').textContent = totalTaxAmount.toFixed(2) + ' ر.س';
        document.getElementById('total-amount').textContent = finalTotal.toFixed(2) + ' ر.س';

        // Update table footer
        document.getElementById('totalAmount').textContent = finalTotal.toFixed(2);

        // Update hidden inputs for form submission
        document.getElementById('tax_input').value = totalTaxAmount.toFixed(2);
        document.getElementById('amount_before_tax_input').value = subtotalBeforeTax.toFixed(2);
        document.getElementById('total_amount_input').value = finalTotal.toFixed(2);
    }

    // Discount Rate Change
    document.getElementById('discount_rate').addEventListener('input', function() {
        const discountRate = parseFloat(this.value) || 0;
        const subtotalBeforeTax = Array.from(document.querySelectorAll('#itemsTableBody tr')).reduce((sum,
            row) => {
                const price = parseFloat(row.querySelector('.amount').value) || 0;
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                return sum + (price * quantity);
            }, 0);
        const totalTax = Array.from(document.querySelectorAll('#itemsTableBody tr')).reduce((sum, row) => {
            const tax = parseFloat(row.querySelector('.tax').value) || 0;
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            return sum + (tax * quantity);
        }, 0);
        const subtotal = subtotalBeforeTax + totalTax;

        const discount = (subtotal * discountRate) / 100;
        document.getElementById('discount').value = discount.toFixed(2);

        calculateTotals();
    });

    // Discount Amount Change
    document.getElementById('discount').addEventListener('input', function() {
        const discount = parseFloat(this.value) || 0;
        const subtotalBeforeTax = Array.from(document.querySelectorAll('#itemsTableBody tr')).reduce((sum,
            row) => {
                const price = parseFloat(row.querySelector('.amount').value) || 0;
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                return sum + (price * quantity);
            }, 0);
        const totalTax = Array.from(document.querySelectorAll('#itemsTableBody tr')).reduce((sum, row) => {
            const tax = parseFloat(row.querySelector('.tax').value) || 0;
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            return sum + (tax * quantity);
        }, 0);
        const subtotal = subtotalBeforeTax + totalTax;

        const discountRate = subtotal > 0 ? (discount / subtotal) * 100 : 0;
        document.getElementById('discount_rate').value = discountRate.toFixed(2);

        calculateTotals();
    });

    // Tax Checkbox Change - Recalculate all rows
    document.getElementById('tax_checkbox').addEventListener('change', function() {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach(row => {
            const rowId = row.id.replace('row-', '');
            const amount = parseFloat(row.querySelector('.amount').value) || 0;
            const isTaxChecked = this.checked;

            const tax = isTaxChecked ? amount * taxRate : 0;
            const amountAfterTax = amount + tax;

            row.querySelector('.tax').value = tax.toFixed(2);
            row.querySelector('.amount-after-tax').value = amountAfterTax.toFixed(2);

            // Recalculate row totals
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const total = amountAfterTax * quantity;
            row.querySelector('.total-amount').value = total.toFixed(2);
        });

        // Update the tax rate display
        document.getElementById('tax_rate').value = this.checked ? 15 : 0;

        // Recalculate all totals
        calculateTotals();
    });

    // Form Validation
    document.getElementById('accountingInvoiceForm').addEventListener('submit', function(e) {
        const rowCount = document.querySelectorAll('#itemsTableBody tr').length;
        if (rowCount === 0) {
            e.preventDefault();
            alert('يجب إضافة بند واحد على الأقل');
            return false;
        }
    });
</script>
