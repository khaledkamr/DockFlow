<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'item_id',
        'quantity',
        'price',
        'total',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
