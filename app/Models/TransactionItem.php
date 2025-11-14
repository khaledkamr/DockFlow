<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'number',
        'description',
        'type',
        'amount',
        'tax',
        'total',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
