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
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@if(request()->query('view', 'قيود يومية') == 'قيود يومية')
    @include('admin.accounting.vouchers.journal_form')
@else
    @include('admin.accounting.vouchers.voucher_form')
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
    @include('admin.accounting.vouchers.daily')
@elseif(request()->query('view') == 'سند قبض نقدي')
    @include('admin.accounting.vouchers.cash_payment')
@elseif(request()->query('view') == 'سند قبض بشيك')
    @include('admin.accounting.vouchers.cheque_payment')
@elseif(request()->query('view') == 'سند صرف نقدي')
    @include('admin.accounting.vouchers.cash_receipt')
@elseif(request()->query('view') == 'سند صرف بشيك')
    @include('admin.accounting.vouchers.cheque_receipt')
@elseif(request()->query('view') == 'الصندوق')
    @include('admin.accounting.vouchers.box')
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
        height: 38px;       
        border-radius: 8px; 
        border: 1px solid #0d6efd;
        padding: 5px;
    }
    .select2-container .select2-selection__rendered {
        line-height: 30px; 
    }
    /* .select2-container .select2-selection__arrow {
        height: 100%; /* يخلي السهم في النص */
    } */
</style>
@endsection