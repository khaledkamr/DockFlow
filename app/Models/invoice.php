<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'policy_id',
        'customer_id', 
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
}
