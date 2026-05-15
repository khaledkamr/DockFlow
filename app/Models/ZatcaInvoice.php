<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZatcaInvoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'invoice_uuid',
        'invoice_hash',
        'pre_hash',
        'request_xml',
        'encoded_xml',
        'qr_data',
        'response_log',
        'request_date',
        'status',
        'invoice_amount',
        'invoice_vat_amount',
        'invoice_total',
        'issue_date',
        'diff_invoice_amount',
        'diff_invoice_vat_amount',
        'diff_invoice_total',
        'diff_status',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
