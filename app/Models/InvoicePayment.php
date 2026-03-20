<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'voucher_id',
        'amount',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class);
    }
}
