<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function companies() {
        return $this->belongsToMany(Company::class, 'company_modules')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }
}
