@extends('layouts.app')

@section('title', 'إنشاء قيد إقفال')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>إنشاء قيد إقفال</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('money.create.journal') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip"
                data-bs-placement="top" title="قيد يومية">
                <i class="fa-solid fa-file-lines"></i>
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                العودة <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <style>
        .bg-unbalanced {
            background-color: rgb(250, 203, 203);
        }

        .bg-balanced {
            background-color: rgb(203, 250, 203);
        }

        .select2-container .select2-selection {
            height: 38px;
            border-radius: 6px;
            border: 2px solid #dddddd;
            padding: 5px;
        }

        .select2-container .select2-selection__rendered {
            line-height: 30px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
    </style>

    <form action="{{ route('store.closing.journal') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm"
        id="closing-journal-form">
        @csrf
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-building me-2"></i>الشركة</label>
                <input type="text" class="form-control border-primary" value="{{ auth()->user()->company->name }}"
                    disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>سنة الإقفال</label>
                <select name="year" id="closing-year" class="form-select border-primary" required>
                    <option value="">-- اختر السنة --</option>
                    @for ($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-calendar-day me-2"></i>تاريخ القيد</label>
                <input type="date" class="form-control border-primary" name="date"
                    value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        <div class="position-relative">
            <div id="loading-indicator" class="loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
            </div>

            <div class="table-container mb-2">
                <table class="table" id="journal-entries-table">
                    <thead>
                        <tr class="table-dark">
                            <th class="text-center" width="2%">#</th>
                            <th class="text-center" width="35%">اسم الحساب</th>
                            <th class="text-center" width="15%">مديــن</th>
                            <th class="text-center" width="15%">دائــن</th>
                            <th class="text-center" width="28%">البيـــان</th>
                            <th class="text-center" width="5%">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="empty-row">
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-info-circle me-2"></i>
                                اختر السنة لعرض بيانات الإقفال
                            </td>
                        </tr>
                        <tr class="table-secondary totals-row d-none">
                            <td></td>
                            <td class="text-center fw-bold fs-5">الفــارق</td>
                            <td>
                                <input type="text" id="debitSum" name="debitSum"
                                    class="form-control text-center fw-bold" value="0.00" readonly>
                            </td>
                            <td>
                                <input type="text" id="creditSum" name="creditSum"
                                    class="form-control text-center fw-bold" value="0.00" readonly>
                            </td>
                            <td>
                                <input type="text" id="diff" name="diff" class="form-control text-center fw-bold"
                                    value="0.00" readonly>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-block text-center mb-3 add-row-container d-none">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-5" id="add-row">+ إضافة سطر</button>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" id="submit" class="btn btn-primary fw-bold" disabled>
                حفظ قيد الاقفال
            </button>
            <button type="button" id="clear-data" class="btn btn-outline-danger d-none">
                <i class="fas fa-eraser me-2"></i>مسح البيانات
            </button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const yearSelect = document.getElementById('closing-year');
            const tableBody = document.querySelector("#journal-entries-table tbody");
            const addRowBtn = document.getElementById("add-row");
            const loadingIndicator = document.getElementById('loading-indicator');
            const clearBtn = document.getElementById('clear-data');

            // Account options for select2
            const accountOptions = `
                <option value="">-- اختر الحساب --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                @endforeach
            `;

            function calculateTotals() {
                let debitSum = 0;
                let creditSum = 0;

                $('input[name="debit[]"]').each(function() {
                    let value = parseFloat($(this).val()) || 0;
                    debitSum += value;
                });
                $('input[name="credit[]"]').each(function() {
                    let value = parseFloat($(this).val()) || 0;
                    creditSum += value;
                });

                let difference = Math.abs(debitSum - creditSum);

                $('#debitSum').val(debitSum.toFixed(2));
                $('#creditSum').val(creditSum.toFixed(2));
                $('#diff').val(difference.toFixed(2));

                if (difference > 0.009) {
                    $('#diff').addClass('bg-unbalanced');
                    $('#diff').removeClass('bg-balanced');
                    $('#submit').prop('disabled', true);
                } else {
                    $('#diff').addClass('bg-balanced');
                    $('#diff').removeClass('bg-unbalanced');
                    $('#submit').prop('disabled', false);
                }
            }

            function bindCalculationEvents() {
                $('input[name="debit[]"], input[name="credit[]"]').off('input keyup paste').on('input keyup paste',
                    function() {
                        calculateTotals();
                    });
            }

            function initializeSelect2AndEvents() {
                $('.account_name').select2({
                    placeholder: "ابحث عن الحساب...",
                    allowClear: true
                });

                $('.account_name').off('change.custom');
            }

            function updateRowNumbers() {
                $('#journal-entries-table tbody tr').each(function(index) {
                    if (!$(this).hasClass('table-secondary') && !$(this).hasClass('empty-row')) {
                        $(this).find('.row-number').text(index + 1);
                    }
                });
            }

            function showLoading() {
                loadingIndicator.classList.remove('d-none');
            }

            function hideLoading() {
                loadingIndicator.classList.add('d-none');
            }

            function clearTable() {
                // Remove all rows except totals row
                $('#journal-entries-table tbody tr:not(.table-secondary):not(.totals-row)').each(function() {
                    $(this).find('.account_name').select2('destroy');
                    $(this).remove();
                });

                // Add empty row back
                const emptyRow = `
                    <tr class="empty-row">
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-info-circle me-2"></i>
                            اختر السنة لعرض بيانات الاقفال
                        </td>
                    </tr>
                `;
                $('.totals-row').before(emptyRow);
                $('.totals-row').addClass('d-none');
                $('.add-row-container').addClass('d-none');
                clearBtn.classList.add('d-none');
                $('#submit').prop('disabled', true);
            }

            function addRow(accountId = '', accountName = '', debit = '', credit = '', description = '') {
                const newRow = `
                    <tr>
                        <td class="text-center align-middle px-2">
                            <span class="row-number fw-bold"></span>
                        </td>
                        <td class="px-2">
                            <select name="account_id[]" class="form-select account_name" style="width: 100%;">
                                ${accountOptions}
                            </select>
                        </td>
                        <td class="px-2">
                            <input type="text" name="debit[]" placeholder="0.00" value="${debit}" class="form-control text-center border-2">
                        </td>
                        <td class="px-2">
                            <input type="text" name="credit[]" placeholder="0.00" value="${credit}" class="form-control text-center border-2">
                        </td>
                        <td class="px-2">
                            <textarea name="description[]" class="form-control border-2" rows="1" style="resize: none; overflow: hidden;"
                                onInput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'">${description}</textarea>
                        </td>
                        <td class="text-center px-2">
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('.totals-row').before(newRow);

                // Set selected account
                if (accountId) {
                    const newSelect = $('#journal-entries-table tbody tr:not(.table-secondary):last').find(
                        '.account_name');
                    newSelect.val(accountId);
                }

                initializeSelect2AndEvents();
                bindCalculationEvents();
                updateRowNumbers();
            }

            // Year selection change handler
            yearSelect.addEventListener('change', function() {
                const year = this.value;

                if (!year) {
                    clearTable();
                    return;
                }

                showLoading();

                // Fetch closing journal data via AJAX
                fetch(`{{ route('get.closing.journal.data') }}?year=${year}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();

                        // Remove empty row
                        $('.empty-row').remove();

                        // Remove existing data rows (keep totals row)
                        $('#journal-entries-table tbody tr:not(.table-secondary):not(.totals-row)')
                            .each(function() {
                                $(this).find('.account_name').select2('destroy');
                                $(this).remove();
                            });

                        // Add expense accounts (as credit - closing expenses)
                        if (data.expenses && data.expenses.length > 0) {
                            data.expenses.forEach(item => {
                                addRow(item.account_id, item.account_name, '', item.balance,
                                    `إقفال ${item.account_name} لسنة ${year}`);
                            });
                        }

                        // Add revenue accounts (as debit - closing revenues)
                        if (data.revenues && data.revenues.length > 0) {
                            data.revenues.forEach(item => {
                                addRow(item.account_id, item.account_name, item.balance, '',
                                    `إقفال ${item.account_name} لسنة ${year}`);
                            });
                        }

                        // Add profit/loss account row
                        if (data.profit_loss) {
                            if (data.profit_loss.type === 'profit') {
                                // Profit goes to credit (to balance)
                                addRow(data.profit_loss.account_id, data.profit_loss.account_name, '',
                                    data.profit_loss.amount, `صافي الربح لسنة ${year}`);
                            } else {
                                // Loss goes to debit (to balance)
                                addRow(data.profit_loss.account_id, data.profit_loss.account_name, data
                                    .profit_loss.amount, '', `صافي الخسارة لسنة ${year}`);
                            }
                        }

                        // Show totals row and buttons
                        $('.totals-row').removeClass('d-none');
                        $('.add-row-container').removeClass('d-none');
                        clearBtn.classList.remove('d-none');

                        calculateTotals();
                        updateRowNumbers();

                        if (data.revenues.length === 0 && data.expenses.length === 0) {
                            // No data found, show message
                            const noDataRow = `
                            <tr class="no-data-row">
                                <td colspan="6" class="text-center text-warning py-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    لا توجد بيانات للإقفال في السنة المحددة
                                </td>
                            </tr>
                        `;
                            $('.totals-row').before(noDataRow);
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء جلب البيانات');
                    });
            });

            // Add row button
            addRowBtn.addEventListener("click", function() {
                addRow();
                calculateTotals();
            });

            // Remove row
            tableBody.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-row") || e.target.closest('.remove-row')) {
                    let row = e.target.closest("tr");
                    $(row).find('.account_name').select2('destroy');
                    row.remove();
                    updateRowNumbers();
                    calculateTotals();
                }
            });

            // Clear data button
            clearBtn.addEventListener('click', function() {
                yearSelect.value = '';
                clearTable();
            });
        });
    </script>
@endsection
