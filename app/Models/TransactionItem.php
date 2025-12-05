<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'credit_account_id',
        'debit_account_id',
        'number',
        'description',
        'type',
        'amount',
        'tax',
        'total',
        'is_posted',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function creditAccount() {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    public function debitAccount() {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }
}
