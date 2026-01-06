@extends('layouts.app')

@section('title', 'إنشاء سند صرف')

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
        <h1 class="mb-0">إنشاء سند صرف</h1>
        <a href="{{ route('money.entries') }}?view=سندات%20صرف" class="btn btn-outline-secondary">
            العودة الى القيود
            <i class="fas fa-arrow-left ms-2"></i>
        </a>
    </div>

    <form action="{{ route('voucher.store') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
        @csrf
        <div class="row mb-3">
            <div class="col">
                <label for="type" class="form-label">نوع السنــد</label>
                <select id="type" name="type" class="form-select border-primary" style="width:100%;">
                    <option value="سند صرف نقدي">نقدي</option>
                    <option value="سند صرف بشيك">بشيك</option>
                    <option value="سند صرف فيزا">فيزا</option>
                    <option value="سند صرف تحويل بنكي">تحويل بنكي</option>
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
                <label for="account_name" class="mb-2">اسم الحساب المدين</label>
                <select id="account_name" name="debit_account_id" class="form-select border-primary">
                    <option value="">-- اختر الحساب --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" data-has-customer="{{ $account->customer ? 'true' : 'false' }}"
                            data-customer-name="{{ $account->customer ? $account->customer->name : '' }}">
                            {{ $account->name }} ({{ $account->code }})
                        </option>
                    @endforeach
                </select>
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
                <label for="credit_account_name" class="mb-2">اسم الحساب الدائن</label>
                <select id="credit_account_name" name="credit_account_id" class="form-select border-primary">
                    <option value="">-- اختر الحساب --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" data-code="{{ $account->code }}">
                            {{ $account->name }} ({{ $account->code }})
                        </option>
                    @endforeach
                </select>
                @error('credit_account_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <input type="hidden" name="invoice_id" id="invoice_id" value="">
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
                                        <th class="text-center">#</th>
                                        <th class="text-center">رقم الفاتورة</th>
                                        <th class="text-center">نوع الفاتورة</th>
                                        <th class="text-center">التاريخ</th>
                                        <th class="text-center">المبلغ قبل الضريبة</th>
                                        <th class="text-center">الضريبة</th>
                                        <th class="text-center">الإجمالي</th>
                                        <th class="text-center">طريقة الدفع</th>
                                        <th class="text-center">حالة الدفع</th>
                                        <th class="text-center">تحديد</th>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentCustomerId = null;

        $('#account_name').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true
        });

        $('#credit_account_name').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true
        });

        $('#account_name').on('change', function() {
            let selectedOption = $(this).find(':selected');
            let hasCustomer = selectedOption.data('has-customer');
            let customerName = selectedOption.data('customer-name');
            let accountId = selectedOption.val();

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
                    $('#invoicesTableBody').html('<tr><td colspan="10" class="text-center text-danger">حدث خطأ أثناء تحميل الفواتير</td></tr>');
                }
            });
        });

        function displayInvoices(invoices) {
            $('#invoicesLoadingSpinner').addClass('d-none');
            $('#invoicesContent').removeClass('d-none');

            if (!invoices || invoices.length === 0) {
                $('#invoicesTableBody').html('');
                $('#noInvoicesMessage').removeClass('d-none');
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
                    <tr class="invoice-row" style="cursor: pointer;" data-invoice-id="${invoice.id}" data-invoice-code="${invoice.code}" data-invoice-amount="${invoice.total_amount}">
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
                            <button type="button" class="btn btn-sm btn-success select-invoice-btn" data-invoice-id="${invoice.id}" data-invoice-code="${invoice.code}" data-invoice-amount="${invoice.total_amount}">
                                <i class="fas fa-check-circle"></i> اختيار
                            </button>
                        </td>
                        <td class="text-center">
                            <a href="${detailsUrl}" class="text-primary" target="_blank" title="عرض التفاصيل" onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            $('#invoicesTableBody').html(html);
            
            // Add click event listeners to select buttons
            $('.select-invoice-btn').on('click', function(e) {
                e.stopPropagation();
                selectInvoice($(this));
            });
            
            // Add click event to rows
            $('.invoice-row').on('click', function(e) {
                if (!$(e.target).closest('a').length && !$(e.target).closest('.select-invoice-btn').length) {
                    selectInvoice($(this));
                }
            });
        }
        
        function selectInvoice(element) {
            const invoiceId = element.data('invoice-id');
            const invoiceCode = element.data('invoice-code');
            const invoiceAmount = element.data('invoice-amount');
            
            // Populate form fields
            $('#invoice_id').val(invoiceId);
            $('#amount').val(parseFloat(invoiceAmount).toFixed(2));
            $('#hatching').val(numberToArabicMoney(parseFloat(invoiceAmount).toFixed(2)));
            $('#description').val('تحصيل فاتورة رقم ' + invoiceCode);
            
            // Trigger amount change to update hatching
            $('#amount').trigger('input');
            
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
        }

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
