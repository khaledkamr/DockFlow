@extends('layouts.app')

@section('title', 'القيود')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h1 class="mb-4">القيود والسندات</h1>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'قيود يومية') === 'قيود يومية' ? 'active' : '' }}" href="?view=قيود يومية">قيود يومية</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سندات قبض' ? 'active' : '' }}" href="?view=سندات قبض">سندات قبض</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'سندات صرف' ? 'active' : '' }}" href="?view=سندات صرف">سندات صرف</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الصندوق' ? 'active' : '' }}" href="?view=الصندوق">الصندوق ({{ $balance }})</a>
    </li>
</ul>

@if(request()->query('view', 'قيود يومية') == 'قيود يومية')
    @include('pages.accounting.journal_entries.journals')
@elseif(request()->query('view') == 'سندات قبض')
    @include('pages.accounting.vouchers.vouchers_payment')
@elseif(request()->query('view') == 'سندات صرف')
    @include('pages.accounting.vouchers.vouchers_receipt')
@elseif(request()->query('view') == 'الصندوق')
    @include('pages.accounting.vouchers.box')
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