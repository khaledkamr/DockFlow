<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Contains;

class Customer extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'name',
        'CR',
        'TIN',
        'national_address',
        'phone',
        'email',
        'account_id',
        'user_id',
        'company_id',
    ];

    public function contract() {
        return $this->hasOne(Contract::class);
    }

    public function containers() {
        return $this->hasMany(Container::class);
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
