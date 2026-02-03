@extends('layouts.app')

@section('title', 'إنشاء سند قبض')

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
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-4">إنشاء سند قبض</h1>
        <a href="{{ route('money.entries') }}?view=سندات%20قبض" class="btn btn-outline-secondary">
            العودة الى القيود
            <i class="fas fa-arrow-left ms-2"></i>
        </a>
    </div>
    <form action="{{ route('voucher.store') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
        @csrf
        <div class="row mb-3">
            <div class="col-3">
                <label for="type" class="form-label">نوع السنــد</label>
                <select id="type" name="type" class="form-select border-primary" style="width:100%;">
                    <option value="سند قبض تحويل بنكي">تحويل بنكي</option>
                    <option value="سند قبض نقدي">نقدي</option>
                    <option value="سند قبض بشيك">بشيك</option>
                    <option value="سند قبض فيزا">فيزا</option>
                </select>
            </div>
            <div class="col-3">
                <label for="date" class="form-label">التاريــخ</label>
                <input type="date" class="form-control border-primary" id="date" name="date"
                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                @error('date')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6">
                <label for="account_name" class="mb-2">اسم الحساب الدائن</label>
                <div class="d-flex gap-2 align-items-end">
                    <select id="account_name" name="credit_account_id" class="form-select border-primary">
                        <option value="">-- اختر الحساب --</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" data-has-customer="{{ $account->customer ? 'true' : 'false' }}"
                                data-customer-name="{{ $account->customer ? $account->customer->name : '' }}">
                                {{ $account->name }} ({{ $account->code }})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="customerBtn" class="btn btn-primary d-none" title="هذا الحساب مرتبط بعميل">
                        <i class="fas fa-scroll"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <label for="amount" class="form-label">المبلــغ</label>
                <input type="text" class="form-control border-primary" id="amount" name="amount" value="">
                @error('amount')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-9">
                <label for="hatching" class="form-label">التفقيـــط</label>
                <input type="text" class="form-control border-primary" id="hatching" name="hatching" value="">
                @error('hatching')
                    <div class="text-danger">{{ $message }}</div>
                    @endif
                </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="description" class="form-label">البيـــان</label>
                <input type="text" class="form-control border-primary" id="description" name="description"
                    value="">
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col-6">
                <label for="debit_account_name" class="mb-2">اسم الحساب المدين</label>
                <select id="debit_account_name" name="debit_account_id" class="form-select border-primary">
                    <option value="">-- اختر الحساب --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->name }} ({{ $account->code }})
                        </option>
                    @endforeach
                </select>
                @error('debit_account_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <div id="invoice_ids_container"></div>
        <button type="submit" class="btn btn-primary fw-bold mt-2">حفظ السند</button>
    </form>

    <!-- Customer Invoices Modal -->
    <div class="modal fade" id="customerInvoicesModal" tabindex="-1" aria-labelledby="customerInvoicesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="customerInvoicesModalLabel">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        فواتير العميل: <span id="modalCustomerName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="invoicesLoadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل الفواتير...</p>
                    </div>
                    <div id="invoicesContent" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="selectAllInvoices" class="form-check-input" title="تحديد الكل">
                                        </th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">رقم الفاتورة</th>
                                        <th class="text-center">نوع الفاتورة</th>
                                        <th class="text-center">التاريخ</th>
                                        <th class="text-center">المبلغ قبل الضريبة</th>
                                        <th class="text-center">الضريبة</th>
                                        <th class="text-center">الإجمالي</th>
                                        <th class="text-center">طريقة الدفع</th>
                                        <th class="text-center">حالة الدفع</th>
                                        <th class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                    <!-- Invoices will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noInvoicesMessage" class="alert alert-info text-center d-none">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد فواتير لهذا العميل
                        </div>
                        <!-- Selected Invoices Summary -->
                        <div id="selectedInvoicesSummary" class="alert alert-primary d-none mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>الفواتير المحددة:</strong> <span id="selectedCount">0</span> فاتورة
                                </div>
                                <div>
                                    <strong>إجمالي المبلغ:</strong> <span id="selectedTotal">0.00</span> ريال
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-success" id="submitSelectedInvoices" disabled>
                        <i class="fas fa-check me-2"></i>
                        تأكيد الفواتير المحددة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentCustomerId = null;
        let selectedInvoices = []; // Array to store selected invoices

        $('#account_name').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true
        });

        $('#debit_account_name').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true
        });

        $('#account_name').on('change', function() {
            let selectedOption = $(this).find(':selected');
            let hasCustomer = selectedOption.data('has-customer');
            let customerName = selectedOption.data('customer-name');
            let accountId = selectedOption.val();

            $('#invoice_ids_container').empty(); // Clear previous invoice IDs
            selectedInvoices = []; // Reset selected invoices

            // Show/hide customer button
            if (hasCustomer) {
                $('#customerBtn').removeClass('d-none');
                $('#customerBtn').attr('title', 'عرض فواتير العميل');
                currentCustomerId = accountId; // Store account ID to fetch customer invoices
            } else {
                $('#customerBtn').addClass('d-none');
                currentCustomerId = null;
            }
        });

        // Customer button click - open modal and load invoices
        $('#customerBtn').on('click', function() {
            if (!currentCustomerId) return;

            let customerName = $('#account_name').find(':selected').data('customer-name');
            $('#modalCustomerName').text(customerName);
            
            // Reset selected invoices when opening modal
            selectedInvoices = [];
            updateSelectedSummary();
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('customerInvoicesModal'));
            modal.show();

            // Show loading spinner
            $('#invoicesLoadingSpinner').removeClass('d-none');
            $('#invoicesContent').addClass('d-none');

            // Fetch invoices via AJAX
            $.ajax({
                url: `/api/customers/account/${currentCustomerId}/invoices`,
                method: 'GET',
                success: function(response) {
                    displayInvoices(response.invoices);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching invoices:', error);
                    $('#invoicesLoadingSpinner').addClass('d-none');
                    $('#invoicesContent').removeClass('d-none');
                    $('#invoicesTableBody').html('<tr><td colspan="11" class="text-center text-danger">حدث خطأ أثناء تحميل الفواتير</td></tr>');
                }
            });
        });

        function displayInvoices(invoices) {
            $('#invoicesLoadingSpinner').addClass('d-none');
            $('#invoicesContent').removeClass('d-none');

            if (!invoices || invoices.length === 0) {
                $('#invoicesTableBody').html('');
                $('#noInvoicesMessage').removeClass('d-none');
                $('#selectAllInvoices').prop('checked', false);
                return;
            }

            $('#noInvoicesMessage').addClass('d-none');
            let html = '';
            
            invoices.forEach((invoice, index) => {
                let detailsUrl = '';

                // Determine the correct route based on invoice type
                if (invoice.type === 'تخزين') {
                    detailsUrl = `{{ route('invoices.details', '') }}/${invoice.uuid}`;
                } else if (invoice.type === 'خدمات') {
                    detailsUrl = `{{ route('invoices.services.details', '') }}/${invoice.uuid}`;
                } else if (invoice.type === 'تخليص') {
                    detailsUrl = `{{ route('invoices.clearance.details', '') }}/${invoice.uuid}`;
                } else if (invoice.type === 'شحن') {
                    detailsUrl = `{{ route('invoices.shipping.details', '') }}/${invoice.uuid}`;
                }

                let paymentStatusBadge = invoice.isPaid == 'تم الدفع'
                    ? '<span class="badge bg-success">مدفوع</span>' 
                    : '<span class="badge bg-danger">غير مدفوع</span>';

                html += `
                    <tr class="invoice-row" data-invoice-id="${invoice.id}" data-invoice-code="${invoice.code}" data-invoice-amount="${invoice.total_amount}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input invoice-checkbox" 
                                data-invoice-id="${invoice.id}" 
                                data-invoice-code="${invoice.code}" 
                                data-invoice-amount="${invoice.total_amount}">
                        </td>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center fw-bold">${invoice.code}</td>
                        <td class="text-center">${invoice.type || '---'}</td>
                        <td class="text-center">${invoice.date ? new Date(invoice.date).toLocaleDateString('en-US') : '---'}</td>
                        <td class="text-center">${parseFloat(invoice.amount_before_tax || 0).toFixed(2)}</td>
                        <td class="text-center">${parseFloat(invoice.tax || 0).toFixed(2)}</td>
                        <td class="text-center fw-bold">${parseFloat(invoice.total_amount || 0).toFixed(2)}</td>
                        <td class="text-center">${invoice.payment_method || '---'}</td>
                        <td class="text-center">${paymentStatusBadge}</td>
                        <td class="text-center">
                            <a href="${detailsUrl}" class="text-primary" target="_blank" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            $('#invoicesTableBody').html(html);
            
            // Add click event listeners to checkboxes
            $('.invoice-checkbox').on('change', function() {
                const invoiceId = $(this).data('invoice-id');
                const invoiceCode = $(this).data('invoice-code');
                const invoiceAmount = parseFloat($(this).data('invoice-amount'));
                
                if ($(this).is(':checked')) {
                    // Add to selected invoices
                    selectedInvoices.push({
                        id: invoiceId,
                        code: invoiceCode,
                        amount: invoiceAmount
                    });
                    $(this).closest('tr').addClass('table-success');
                } else {
                    // Remove from selected invoices
                    selectedInvoices = selectedInvoices.filter(inv => inv.id !== invoiceId);
                    $(this).closest('tr').removeClass('table-success');
                }
                
                updateSelectedSummary();
                updateSelectAllCheckbox();
            });
            
            // Add click event to rows (excluding checkbox and links)
            $('.invoice-row').on('click', function(e) {
                if (!$(e.target).closest('a').length && !$(e.target).is('input[type="checkbox"]')) {
                    const checkbox = $(this).find('.invoice-checkbox');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });
            
            // Select all checkbox
            $('#selectAllInvoices').off('change').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.invoice-checkbox').each(function() {
                    if ($(this).prop('checked') !== isChecked) {
                        $(this).prop('checked', isChecked).trigger('change');
                    }
                });
            });
        }
        
        function updateSelectedSummary() {
            const count = selectedInvoices.length;
            const total = selectedInvoices.reduce((sum, inv) => sum + inv.amount, 0);
            
            $('#selectedCount').text(count);
            $('#selectedTotal').text(total.toFixed(2));
            
            if (count > 0) {
                $('#selectedInvoicesSummary').removeClass('d-none');
                $('#submitSelectedInvoices').prop('disabled', false);
            } else {
                $('#selectedInvoicesSummary').addClass('d-none');
                $('#submitSelectedInvoices').prop('disabled', true);
            }
        }
        
        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.invoice-checkbox').length;
            const checkedCheckboxes = $('.invoice-checkbox:checked').length;
            
            if (totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0) {
                $('#selectAllInvoices').prop('checked', true);
            } else {
                $('#selectAllInvoices').prop('checked', false);
            }
        }
        
        // Submit selected invoices button
        $('#submitSelectedInvoices').on('click', function() {
            if (selectedInvoices.length === 0) return;
            
            // Calculate total amount
            const totalAmount = selectedInvoices.reduce((sum, inv) => sum + inv.amount, 0);
            
            // Get all invoice codes
            const invoiceCodes = selectedInvoices.map(inv => inv.code).join(' - ');
            
            // Clear and populate invoice IDs as array
            $('#invoice_ids_container').empty();
            selectedInvoices.forEach(inv => {
                $('#invoice_ids_container').append(
                    `<input type="hidden" name="invoice_id[]" value="${inv.id}">`
                );
            });
            
            // Populate form fields
            $('#amount').val(totalAmount.toFixed(2));
            $('#hatching').val(numberToArabicMoney(totalAmount.toFixed(2)));
            $('#description').val('تحصيل فواتير: ' + invoiceCodes);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('customerInvoicesModal'));
            modal.hide();
            
            // Visual feedback
            $('#amount').addClass('border-success border-2');
            $('#hatching').addClass('border-success border-2');
            $('#description').addClass('border-success border-2');
            setTimeout(function() {
                $('#amount').removeClass('border-success border-2');
                $('#hatching').removeClass('border-success border-2');
                $('#description').removeClass('border-success border-2');
            }, 2000);
        });

        function numberToArabicWords(num) {
            if (num === 0) return "صفر";

            const ones = [
                "", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة",
                "ستة", "سبعة", "ثمانية", "تسعة", "عشرة",
                "أحد عشر", "اثنا عشر", "ثلاثة عشر", "أربعة عشر", "خمسة عشر",
                "ستة عشر", "سبعة عشر", "ثمانية عشر", "تسعة عشر"
            ];

            const tens = [
                "", "", "عشرون", "ثلاثون", "أربعون", "خمسون",
                "ستون", "سبعون", "ثمانون", "تسعون"
            ];

            const hundreds = [
                "", "مئة", "مئتان", "ثلاثمائة", "أربعمائة", "خمسمائة",
                "ستمائة", "سبعمائة", "ثمانمائة", "تسعمائة"
            ];

            function convertThreeDigits(n) {
                let parts = [];

                // المئات
                if (n >= 100) {
                    let h = Math.floor(n / 100);
                    n %= 100;
                    parts.push(hundreds[h]);
                }

                // العشرات والآحاد
                if (n > 0) {
                    if (n < 20) {
                        parts.push(ones[n]);
                    } else {
                        let t = Math.floor(n / 10);
                        let o = n % 10;
                        if (o > 0) {
                            parts.push(ones[o] + " و" + tens[t]);
                        } else {
                            parts.push(tens[t]);
                        }
                    }
                }

                return parts.join(" و");
            }

            let result = [];
            let originalNum = num;

            // الملايين
            if (num >= 1000000) {
                let millions = Math.floor(num / 1000000);
                num %= 1000000;

                if (millions === 1) {
                    result.push("مليون");
                } else if (millions === 2) {
                    result.push("مليونان");
                } else if (millions < 11) {
                    result.push(ones[millions] + " ملايين");
                } else {
                    result.push(convertThreeDigits(millions) + " مليون");
                }
            }

            // الآلاف
            if (num >= 1000) {
                let thousands = Math.floor(num / 1000);
                num %= 1000;

                if (thousands === 1) {
                    result.push("ألف");
                } else if (thousands === 2) {
                    result.push("ألفان");
                } else if (thousands < 11) {
                    result.push(ones[thousands] + " آلاف");
                } else {
                    let thousandsText = convertThreeDigits(thousands);
                    result.push(thousandsText + " ألف");
                }
            }

            // المئات والعشرات والآحاد
            if (num > 0) {
                result.push(convertThreeDigits(num));
            }

            return result.join(" و");
        }

        function numberToArabicMoney(amount) {
            if (amount === 0) return "صفر ريال";

            // تحويل إلى string للتعامل مع الأرقام العشرية بدقة
            const amountStr = amount.toString();
            const parts = amountStr.split('.');

            const riyals = parseInt(parts[0]) || 0;
            let halalas = 0;

            if (parts.length > 1) {
                // إضافة صفر إذا كان هناك رقم واحد فقط بعد العلامة العشرية
                const decimalPart = parts[1].padEnd(2, '0').substring(0, 2);
                halalas = parseInt(decimalPart);
            }

            let result = [];

            // إضافة الريالات
            if (riyals > 0) {
                result.push(numberToArabicWords(riyals) + " ريال");
            }

            // إضافة الهللات
            if (halalas > 0) {
                result.push(numberToArabicWords(halalas) + " هللة");
            }

            // إذا لم يكن هناك ريالات أو هللات
            if (result.length === 0) {
                return "صفر ريال";
            }

            return result.join(" و");
        }

        document.getElementById("amount").addEventListener("input", function() {
            const amount = parseFloat(this.value) || 0;
            document.getElementById("hatching").value = numberToArabicMoney(amount);
        });
    </script>

@endsection
