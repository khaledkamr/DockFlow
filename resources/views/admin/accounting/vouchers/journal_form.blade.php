<style>
    .bg-unbalanced {
        background-color: rgb(250, 203, 203);
    }
    .bg-balanced {
        background-color: rgb(203, 250, 203);
    }
</style>
<form action="{{ route('admin.create.journal') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">الشركة</label>
            <input type="text" class="form-control" value="شركة تاج للتخزين" disabled>
        </div>
        <div class="col">
            <label class="form-label">الفرع</label>
            <input type="text" class="form-control" value="الفرع الرئيسي" disabled>
        </div>
        <div class="col">
            <label class="form-label">رقم القيد</label>
            <input type="text" class="form-control" name="code">
        </div>
        <div class="col">
            <label class="form-label">تاريخ القيد</label>
            <input type="date" class="form-control" name="date" value="{{ now()->format('Y-m-d') }}">
        </div>
    </div>
    <div style="border-radius: 8px; overflow: hidden;" class="mb-2">
        <table class="table" id="journal-entries-table">
            <thead>
                <tr class="table-secondary">
                    <th class="text-center" width="20%">رقم الحساب</th>
                    <th class="text-center" width="25%">اسم الحساب</th>
                    <th class="text-center" width="10%">مديــن</th>
                    <th class="text-center" width="10%">دائــن</th>
                    <th class="text-center" width="30%">البيـــان</th>
                    <th class="text-center" width="5%">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 2; $i++)
                    <tr>
                        <td>
                            <select name="account_id[]" class="form-select account_name">
                                <option value="">-- اختر الحساب --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" data-code="{{ $account->code }}">
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="account_code[]" class="form-control account_code"></td>
                        <td><input type="text" name="debit[]" placeholder="0.00" class="form-control text-center"></td>
                        <td><input type="text" name="credit[]" placeholder="0.00" class="form-control text-center"></td>
                        <td><input type="text" name="description[]" class="form-control"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row">حذف</button>
                        </td>
                    </tr>
                @endfor
                <tr class="table-secondary">
                    <td colspan="2" class="text-center fw-bold fs-5">الفــارق</td>
                    <td><input type="text" id="debitSum" name="debitSum" class="form-control text-center" value="0.00"></td>
                    <td><input type="text" id="creditSum" name="creditSum" class="form-control text-center" value="0.00"></td>
                    <td colspan="2"><input type="text" id="diff" name="diff" class="form-control text-center" value="0.00"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-block text-center">
        <button type="button" class="btn btn-secondary btn-sm" id="add-row">+ إضافة سطر</button>
    </div>
    @if (session('success'))
        @push('scripts')
            <script>
                showToast("{{ session('success') }}", "success");
            </script>
        @endpush
    @endif
    @if (session('error'))
        @push('scripts')
            <script>
                showToast("{{ session('error') }}", "danger");
            </script>
        @endpush
    @endif
    @if (session('errors'))
        @push('scripts')
            <script>
                showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
            </script>
        @endpush
    @endif
    <button type="submit" id="submit" class="btn btn-primary fw-bold">حفظ القيد</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", function () {
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
        
        if (difference !== 0) {
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
        $('input[name="debit[]"], input[name="credit[]"]').off('input keyup paste').on('input keyup paste', function() {
            calculateTotals();
        });
    }

    function initializeSelect2AndEvents() {
        $('.account_name').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true
        });

        $('.account_name').off('change.custom');
        
        $('.account_name').on('change.custom', function () {
            let code = $(this).find(':selected').data('code');
            let row = $(this).closest('tr');
            row.find('.account_code').val(code || '');
        });
    }

    initializeSelect2AndEvents();
    bindCalculationEvents();
    calculateTotals();
    
    let tableBody = document.querySelector("#journal-entries-table tbody");
    let addRowBtn = document.getElementById("add-row");

    addRowBtn.addEventListener("click", function () {
        let newRow = `
            <tr>
                <td>
                    <select name="account_name[]" class="form-select account_name">
                        <option value="">-- اختر الحساب --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" data-code="{{ $account->code }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="account_code[]" class="form-control account_code"></td>
                <td><input type="text" name="debit[]" class="form-control"></td>
                <td><input type="text" name="credit[]" class="form-control"></td>
                <td><input type="text" name="description[]" class="form-control"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">حذف</button></td>
            </tr>
        `;
        
        let rows = tableBody.querySelectorAll('tr');
        let lastTwoRows = Array.from(rows).slice(-1);
        
        lastTwoRows[0].insertAdjacentHTML("beforebegin", newRow);
        
        initializeSelect2AndEvents();
        bindCalculationEvents();
    });

    tableBody.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-row")) {
            let row = e.target.closest("tr");
            $(row).find('.account_name').select2('destroy');
            row.remove();
        }
    });
});
</script>

<style>
    .select2-container .select2-selection {
        height: 38px;     
        border-radius: 8px; 
        border: 1px solid #d7dde4;
        padding: 5px;
    }
    .select2-container .select2-selection__rendered {
        line-height: 30px; 
    }
    /* .select2-container .select2-selection__arrow {
        height: 100%; /* يخلي السهم في النص */
    } */
    
</style>