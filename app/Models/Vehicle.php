<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'type', 
        'plate_number',
        'account_id',
        'company_id',
    ];

    public function driver() {
        return $this->hasOne(Driver::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }
}
