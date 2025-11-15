<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearanceInvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'number',
        'description',
        'amount',
        'tax',
        'total',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
