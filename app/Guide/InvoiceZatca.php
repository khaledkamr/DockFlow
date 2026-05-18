<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceZatca extends Model
{
    protected $table = 'invoice_zatcas';
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function invoiceHd(): BelongsTo
    {
        return $this->belongsTo(InvoiceHd::class, 'invoice_header_id');
    }
}
