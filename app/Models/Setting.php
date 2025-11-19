<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'name',
        'description',
        'is_enabled',
        'is_active',
        'company_id',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
