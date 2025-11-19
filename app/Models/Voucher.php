<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'code',
        'type', 
        'date', 
        'amount', 
        'hatching',
        'description',
        'account_id', 
        'is_posted',
        'user_id',
        'company_id',
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
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

            $lastVoucher = self::where('type', $voucher->type)->whereYear('date', $year)->latest('code')->first();

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
