<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'name',
        'type',
        'debit_account_id',
        'company_id',
    ];

    public function debitAccount() {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }
}
