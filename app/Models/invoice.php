<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'policy_id',
        'customer_id', 
        'date',
        'base_price', 
        'late_fee_total', 
        'tax_total', 
        'grand_total',
        'payment_method'
    ];

    public function policy() {
        return $this->belongsTo(Policy::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}
