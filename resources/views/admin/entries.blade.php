@extends('layouts.admin')

@section('title', 'القيود')

@section('content')
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        background-color: #ffffff;
        border-color: #48a0ff #48a0ff #ffffff;
        color: #007bff;
        font-weight: bold;
    }
        .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-average {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-high {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@if(request()->query('view', 'قيود يومية') == 'قيود يومية')
<form action="{{ route('admin.create.voucher') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label for="date" class="form-label">الشركة</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
            @error('date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="date" class="form-label">رقم القيد</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
            @error('date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="code" class="form-label">تاريخ القيد</label>
            <input type="text" class="form-control" id="code" name="code" value="">
            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-3">
            <label for="amount" class="form-label">رقم الحساب</label>
            <input type="text" class="form-control" id="amount" name="amount" value="">
            @error('amount')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="hatching" class="form-label">إسم الحساب</label>
            <input type="text" class="form-control" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col-1">
            <label for="hatching" class="form-label">مدين</label>
            <input type="text" class="form-control" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col-1">
            <label for="hatching" class="form-label">دائن</label>
            <input type="text" class="form-control" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="hatching" class="form-label">البيان</label>
            <input type="text" class="form-control" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary fw-bold mt-2">إضافة قيد</button>
</form>
@else
<form action="{{ route('admin.create.voucher') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label for="type" class="form-label">نوع السنــد</label>
            <select id="type" name="type" class="form-select" style="width:100%;">
                <option value="receipt_cash">سند صرف نقدي</option>
                <option value="receipt_cheque">سند صرف بشيك</option>
                <option value="payment_cash">سند قبض نقدي</option>
                <option value="payment_cheque">سند قبض بشيك</option>
            </select>
        </div>
        <div class="col">
            <label for="date" class="form-label">التاريــخ</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
            @error('date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="code" class="form-label">رقم السنــد </label>
            <input type="text" class="form-control" id="code" name="code" value="">
            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="account_name">اسم الحساب</label>
            <select id="account_name" class="form-select" style="width:100%;">
                <option value="">-- اختر الحساب --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" data-code="{{ $account->code }}">
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="account_code" class="form-label">رقم الحســاب</label>
            <input type="text" class="form-control" id="account_code" name="account_code" value="">
            @error('account_code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-3">
            <label for="amount" class="form-label">المبلــغ</label>
            <input type="text" class="form-control" id="amount" name="amount" value="">
            @error('amount')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col-9">
            <label for="hatching" class="form-label">التفقيـــط</label>
            <input type="text" class="form-control" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="description" class="form-label">البيـــان</label>
            <input type="text" class="form-control" id="description" name="description" value="">
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        
    </div>
    <button type="submit" class="btn btn-primary fw-bold mt-2">إضافة سند</button>
</form>
@endif


<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'قيود يومية') === 'قيود يومية' ? 'active' : '' }}" href="?view=قيود يومية">قيود يومية</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سند قبض نقدي' ? 'active' : '' }}" href="?view=سند قبض نقدي">سند قبض نقدي</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سند قبض بشيك' ? 'active' : '' }}" href="?view=سند قبض بشيك">سند قبض بشيك</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سند صرف نقدي' ? 'active' : '' }}" href="?view=سند صرف نقدي">سند صرف نقدي</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سند صرف بشيك' ? 'active' : '' }}" href="?view=سند صرف بشيك">سند صرف بشيك</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الصندوق' ? 'active' : '' }}" href="?view=الصندوق">الصندوق ({{ $balance }})</a>
    </li>
</ul>

@if(request()->query('view', 'قيود يومية') == 'قيود يومية')
    @include('admin.vouchers.daily')
@elseif(request()->query('view') == 'سند قبض نقدي')
    @include('admin.vouchers.cash_payment')
@elseif(request()->query('view') == 'سند قبض بشيك')
    @include('admin.vouchers.cheque_payment')
@elseif(request()->query('view') == 'سند صرف نقدي')
    @include('admin.vouchers.cash_receipt')
@elseif(request()->query('view') == 'سند صرف بشيك')
    @include('admin.vouchers.cheque_receipt')
@elseif(request()->query('view') == 'الصندوق')
    @include('admin.vouchers.box')
@endif

<script>
    $('#account_name').select2({
        placeholder: "ابحث عن الحساب...",
        allowClear: true
    });

    $('#account_name').on('change', function () {
        let code = $(this).find(':selected').data('code');
        $('#account_code').val(code || '');
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

    document.getElementById("amount").addEventListener("input", function() {
        const num = parseInt(this.value) || 0;
        document.getElementById("hatching").value = numberToArabicWords(num) + " ريال";
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 38px;       /* زي input العادي */
        border-radius: 8px; /* زوايا دائرية */
        border: 1px solid #d7dde4;
        padding: 5px;
        margin-top: 7px;
    }
    .select2-container .select2-selection__rendered {
        line-height: 30px; /* يظبط النص في النص */
    }
    /* .select2-container .select2-selection__arrow {
        height: 100%; /* يخلي السهم في النص */
    } */

    
</style>

@endsection