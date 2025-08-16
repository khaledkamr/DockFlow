<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'contract_id', 
        'invoice_date',
        'base_price', 
        'late_fee_total', 
        'tax_total', 
        'grand_total'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }
}
