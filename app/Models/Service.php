<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'description',
        'company_id',
    ];

    public function contracts() {
        return $this->belongsToMany(Contract::class, 'contract_services')
                    ->withPivot(['price', 'unit', 'unit_desc'])
                    ->withTimestamps();
    }

    public function containers() {
        return $this->belongsToMany(Container::class, 'container_services')
            ->withPivot('price', 'notes')
            ->withTimestamps();
    }
}
