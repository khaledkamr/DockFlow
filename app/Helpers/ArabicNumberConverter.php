<?php

namespace App\Helpers;

class ArabicNumberConverter
{
    public static function numberToArabicWords($num)
    {
        if ($num === 0) return "صفر";

        $ones = [
            "", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة",
            "ستة", "سبعة", "ثمانية", "تسعة", "عشرة",
            "أحد عشر", "اثنا عشر", "ثلاثة عشر", "أربعة عشر", "خمسة عشر",
            "ستة عشر", "سبعة عشر", "ثمانية عشر", "تسعة عشر"
        ];

        $tens = [
            "", "", "عشرون", "ثلاثون", "أربعون", "خمسون",
            "ستون", "سبعون", "ثمانون", "تسعون"
        ];

        $hundreds = [
            "", "مئة", "مئتان", "ثلاثمائة", "أربعمائة", "خمسمائة",
            "ستمائة", "سبعمائة", "ثمانمائة", "تسعمائة"
        ];

        $convertThreeDigits = function($n) use ($ones, $tens, $hundreds) {
            $parts = [];
            
            // المئات
            if ($n >= 100) {
                $h = intval($n / 100);
                $n %= 100;
                $parts[] = $hundreds[$h];
            }

            // العشرات والآحاد
            if ($n > 0) {
                if ($n < 20) {
                    $parts[] = $ones[$n];
                } else {
                    $t = intval($n / 10);
                    $o = $n % 10;
                    if ($o > 0) {
                        $parts[] = $ones[$o] . " و" . $tens[$t];
                    } else {
                        $parts[] = $tens[$t];
                    }
                }
            }

            return implode(" و", $parts);
        };

        $result = [];
        $originalNum = $num;

        // الملايين
        if ($num >= 1000000) {
            $millions = intval($num / 1000000);
            $num %= 1000000;

            if ($millions === 1) {
                $result[] = "مليون";
            } elseif ($millions === 2) {
                $result[] = "مليونان";
            } elseif ($millions < 11) {
                $result[] = $ones[$millions] . " ملايين";
            } else {
                $result[] = $convertThreeDigits($millions) . " مليون";
            }
        }

        // الآلاف
        if ($num >= 1000) {
            $thousands = intval($num / 1000);
            $num %= 1000;

            if ($thousands === 1) {
                $result[] = "ألف";
            } elseif ($thousands === 2) {
                $result[] = "ألفان";
            } elseif ($thousands < 11) {
                $result[] = $ones[$thousands] . " آلاف";
            } else {
                $thousandsText = $convertThreeDigits($thousands);
                $result[] = $thousandsText . " ألف";
            }
        }

        // المئات والعشرات والآحاد
        if ($num > 0) {
            $result[] = $convertThreeDigits($num);
        }

        return implode(" و", $result);
    }

    public static function numberToArabicMoney($amount)
    {
        if ($amount == 0) return "صفر ريال";
        
        // تحويل إلى string للتعامل مع الأرقام العشرية بدقة
        $amountStr = (string) $amount;
        $parts = explode('.', $amountStr);
        
        $parts[0] = str_replace(',', '', $parts[0]);
        $riyals = intval($parts[0]) ?: 0;
        $halalas = 0;
        
        if (count($parts) > 1) {
            // إضافة صفر إذا كان هناك رقم واحد فقط بعد العلامة العشرية
            $decimalPart = str_pad(substr($parts[1], 0, 2), 2, '0', STR_PAD_RIGHT);
            $halalas = intval($decimalPart);
        }
        
        $result = [];
        
        // إضافة الريالات
        if ($riyals > 0) {
            $result[] = self::numberToArabicWords($riyals) . " ريال";
        }
        
        // إضافة الهللات
        if ($halalas > 0) {
            $result[] = self::numberToArabicWords($halalas) . " هللة";
        }
        
        // إذا لم يكن هناك ريالات أو هللات
        if (count($result) === 0) {
            return "صفر ريال";
        }
        
        return implode(" و", $result);
    }
}