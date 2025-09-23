<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'type_id',
        'level',
        'is_active',
        'company_id',
    ];

    public function type() {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    public function parent() {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
