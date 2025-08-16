<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'contract_id',
        'user_id', 
        'date',
        'base_price', 
        'late_fee_total', 
        'tax_total', 
        'grand_total',
        'payment_method'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
