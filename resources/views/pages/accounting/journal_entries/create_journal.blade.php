@extends('layouts.app')

@section('title', 'إنشاء قيد يومية')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>إنشاء قيد يومية</h1>
        <div class="d-flex gap-2">
            @can('إنشاء قيد إقفال')
                <a href="{{ route('money.create.opening.journal') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="قيد إفتتاحي">
                    <i class="fa-solid fa-lock-open"></i>
                </a>
            @endcan
            @can('إنشاء قيد إقفال')
                <a href="{{ route('money.create.closing.journal') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="قيد إقفال">
                    <i class="fa-solid fa-lock"></i>
                </a>
            @endcan
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

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table {
            min-width: 900px;
        }

        .cost-center-select {
            min-width: 180px;
        }
    </style>

    <form action="{{ route('store.journal') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm"
        enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
            <div class="col">
                <label class="form-label"><i class="fas fa-building me-2"></i>الشركة</label>
                <input type="text" class="form-control border-primary" value="{{ auth()->user()->company->name }}"
                    disabled>
            </div>
            <div class="col">
                <label class="form-label"><i class="fas fa-paperclip me-2"></i>المرفق</label>
                <input type="file" name="attachment" class="form-control border-primary">
            </div>
            <div class="col">
                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>تاريخ القيد</label>
                <input type="date" class="form-control border-primary" name="date"
                    value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
        <div class="table-container mb-2">
            <table class="table" id="journal-entries-table">
                <thead>
                    <tr class="table-dark">
                        <th class="text-center" style="min-width: 10px">#</th>
                        <th class="text-center" style="min-width: 400px">اسم الحساب</th>
                        <th class="text-center" style="min-width: 100px">مديــن</th>
                        <th class="text-center" style="min-width: 100px">دائــن</th>
                        <th class="text-center" style="min-width: 300px">مركز التكلفة</th>
                        <th class="text-center" style="min-width: 400px">البيـــان</th>
                        <th class="text-center" style="min-width: 50px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 2; $i++)
                        <tr>
                            <td class="text-center align-middle px-2">
                                <span class="row-number fw-bold">{{ $i + 1 }}</span>
                            </td>
                            <td class="px-2">
                                <select name="account_id[]" class="form-select account_name journal" style="width: 100%;">
                                    <option value="">-- اختر الحساب --</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->name }} ({{ $account->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2">
                                <input type="text" name="debit[]" placeholder="0.00"
                                    class="form-control text-center border-2">
                            </td>
                            <td class="px-2">
                                <input type="text" name="credit[]" placeholder="0.00"
                                    class="form-control text-center border-2">
                            </td>
                            <td class="px-2">
                                <select name="cost_center_id[]" class="form-select cost_center_select cost-center-select"
                                    style="width: 100%;">
                                    <option value="">-- مركز التكلفة --</option>
                                    @foreach ($costCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}">
                                            {{ $costCenter->name }} ({{ $costCenter->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2">
                                <textarea name="description[]" class="form-control border-2" rows="1" style="resize: none; overflow: hidden;"
                                    onInput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'"></textarea>
                            </td>
                            <td class="text-center px-2">
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @endfor
                    <tr class="table-secondary">
                        <td></td>
                        <td class="text-center fw-bold fs-5">الفــارق</td>
                        <td>
                            <input type="text" id="debitSum" name="debitSum" class="form-control text-center fw-bold"
                                value="0.00" readonly>
                        </td>
                        <td>
                            <input type="text" id="creditSum" name="creditSum"
                                class="form-control text-center fw-bold"value="0.00" readonly>
                        </td>
                        <td>
                            <input type="text" id="diff" name="diff" class="form-control text-center fw-bold"
                                value="0.00" readonly>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-block text-center">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-5" id="add-row">+ إضافة سطر</button>
        </div>

        <button type="submit" id="submit" class="btn btn-primary fw-bold">حفظ القيد</button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                console.log(`Difference: ${debitSum} - ${creditSum} =`, difference);

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

            // Account cost center required mapping
            const accountCostCenterRequired = {
                @foreach ($accounts as $account)
                    '{{ $account->id }}': {{ $account->cost_center_required ? 'true' : 'false' }},
                @endforeach
            };

            function initializeSelect2AndEvents() {
                $('.account_name').select2({
                    placeholder: "ابحث عن الحساب...",
                    allowClear: true
                });

                $('.cost_center_select').select2({
                    placeholder: "ابحث عن مركز التكلفة...",
                    allowClear: true
                });

                // Handle account change to set cost center required
                $('.account_name').off('change.costcenter').on('change.costcenter', function() {
                    let row = $(this).closest('tr');
                    let accountId = $(this).val();
                    let costCenterSelect = row.find('.cost_center_select');

                    if (accountId && accountCostCenterRequired[accountId]) {
                        costCenterSelect.attr('required', true);
                        costCenterSelect.addClass('border-danger');
                    } else {
                        costCenterSelect.removeAttr('required');
                        costCenterSelect.removeClass('border-danger');
                    }
                });

                // Trigger change on existing account selects to set initial state
                $('.account_name').each(function() {
                    if ($(this).val()) {
                        $(this).trigger('change.costcenter');
                    }
                });
            }

            initializeSelect2AndEvents();
            bindCalculationEvents();
            calculateTotals();

            let tableBody = document.querySelector("#journal-entries-table tbody");
            let addRowBtn = document.getElementById("add-row");

            addRowBtn.addEventListener("click", function() {
                let newRow = `
                    <tr>
                        <td class="text-center align-middle px-2">
                            <span class="row-number fw-bold"></span>
                        </td>
                        <td class="px-2">
                            <select name="account_id[]" class="form-select account_name">
                                <option value="">-- اختر الحساب --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-2"><input type="text" name="debit[]" placeholder="0.00" class="form-control border-2 text-center"></td>
                        <td class="px-2"><input type="text" name="credit[]" placeholder="0.00" class="form-control border-2 text-center"></td>
                        <td class="px-2">
                            <select name="cost_center_id[]" class="form-select cost_center_select cost-center-select" style="width: 100%;">
                                <option value="">-- مركز التكلفة --</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}">{{ $costCenter->name }} ({{ $costCenter->code }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-2"><input type="text" name="description[]" class="form-control border-2"></td>
                        <td class="text-center px-2"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash-can"></i></button></td>
                    </tr>
                `;

                let rows = tableBody.querySelectorAll('tr');
                let lastTwoRows = Array.from(rows).slice(-1);

                lastTwoRows[0].insertAdjacentHTML("beforebegin", newRow);

                initializeSelect2AndEvents();
                bindCalculationEvents();
                updateRowNumbers();
            });

            tableBody.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-row")) {
                    let row = e.target.closest("tr");
                    $(row).find('.account_name').select2('destroy');
                    $(row).find('.cost_center_select').select2('destroy');
                    row.remove();
                    updateRowNumbers();
                }
                calculateTotals();
            });

            function updateRowNumbers() {
                $('#journal-entries-table tbody tr').each(function(index) {
                    if (!$(this).hasClass('table-secondary')) {
                        $(this).find('.row-number').text(index + 1);
                    }
                });
            }

            updateRowNumbers();
        });
    </script>
@endsection
