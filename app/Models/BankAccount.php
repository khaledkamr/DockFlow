<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'company_id',
        'bank',
        'account_number',
        'iban',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
