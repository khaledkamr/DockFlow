<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'country',
        'city',
        'street',
        'district',
        'building_number',
        'secondary_number',
        'postal_code',
        'short_address'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
