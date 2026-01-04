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
        'credit_account_id',   // حساب الدائن
        'debit_account_id',    // حساب المدين
        'is_posted',
        'user_id',
        'company_id',
    ];

    public const TYPES = [
        'سند صرف نقدي',
        'سند صرف بشيك',
        'سند صرف فيزا',
        'سند صرف تحويل بنكي',
        'سند قبض نقدي',
        'سند قبض بشيك',
        'سند قبض فيزا',
        'سند قبض تحويل بنكي',
    ];

    public function credit_account() {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    public function debit_account() {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($voucher) {
            $year = $voucher->date ? date('Y', strtotime($voucher->date)) : date('Y');
            $prefix = match($voucher->type) {
                'سند صرف نقدي' => 'VR',
                'سند صرف بشيك' => 'VR',
                'سند صرف فيزا' => 'VR',
                'سند صرف تحويل بنكي' => 'VR',
                'سند قبض نقدي' => 'VP',
                'سند قبض بشيك' => 'VP',
                'سند قبض فيزا' => 'VP',
                'سند قبض تحويل بنكي' => 'VP',
                default => 'XX',
            };

            $lastVoucher = self::where('code', 'like', $year . $prefix . '%')->whereYear('date', $year)->latest('code')->first();

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
