<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'customer_id',
        'claim_number',
        'total_amount'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function Invoices() {
        return $this->belongsToMany(Invoice::class, 'claim_invoice');
    }

    protected static function booted()
    {
        static::creating(function ($claim) {
            $year = date('Y');
            $prefix = 'CL';
            $lastClaim = self::whereYear('created_at', $year)->latest('id')->first();
            if ($lastClaim && $lastClaim->claim_number) {
                $lastNumber = (int) substr($lastClaim->claim_number, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $claim->claim_number = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
