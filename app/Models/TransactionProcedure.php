<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionProcedure extends Model
{
    protected $fillable = [
        'name',
        'transaction_id',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
