<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'type',
        'company_id',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
