<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type', 
        'date', 
        'amount', 
        'hatching',
        'description',
        'account_id', 
        'is_posted'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    protected static function booted()
    {
        static::creating(function ($voucher) {
            $year = date('Y');
            $prefix = match($voucher->type) {
                'سند صرف نقدي' => 'RC',
                'سند صرف بشيك' => 'RB',
                'سند قبض نقدي' => 'PC',
                'سند قبض بشيك' => 'PB',
                default => 'XX',
            };

            $lastVoucher = self::where('type', $voucher->type)->whereYear('date', $year)->latest('id')->first();

            if ($lastVoucher && $lastVoucher->code) {
                $lastNumber = (int) substr($lastVoucher->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $voucher->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
