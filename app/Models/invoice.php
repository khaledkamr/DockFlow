<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'policy_id',
        'customer_id', 
        'code',
        'made_by',
        'amount',
        'payment_method',
        'date',
        'payment'
    ];

    public function policy() {
        return $this->belongsTo(Policy::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function claims() {
        return $this->belongsToMany(Claim::class, 'claim_invoice');
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $year = date('Y');
            $prefix = 'IN';
            $lastInvoice = self::whereYear('date', $year)->latest('id')->first();
            if ($lastInvoice && $lastInvoice->code) {
                $lastNumber = (int) substr($lastInvoice->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $invoice->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
