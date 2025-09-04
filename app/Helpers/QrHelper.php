<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrHelper
{
    public static function generateZatcaQr($seller, $vatNumber, $timestamp, $total, $vatTotal)
    {
        $elements = [
            [1, $seller],
            [2, $vatNumber],
            [3, $timestamp],
            [4, $total],
            [5, $vatTotal],
        ];

        $tlv = '';
        foreach ($elements as [$tag, $value]) {
            $length = strlen($value);
            $tlv .= chr($tag) . chr($length) . $value;
        }

        // TLV → Base64
        $base64 = base64_encode($tlv);

        // QR code
        return QrCode::size(120)->color(11, 50, 255)->generate($base64);
    }
}
