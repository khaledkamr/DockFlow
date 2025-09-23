<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Container_type extends Model
{
    use BelongsToCompany;

    protected $fillable = ['name', 'daily_price', 'company_id'];

    public function containers() {
        return $this->hasMany(Container::class);
    }
}
