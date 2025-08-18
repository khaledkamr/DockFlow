<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type', 
        'date', 
        'amount', 
        'hatching',
        'description',
        'account_id', 
        'is_posted'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }
}
