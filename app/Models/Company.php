<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'branch',
        'logo',
        'CR',
        'TIN',
        'vatNumber',
        'national_address',
        'phone',
        'email'
    ];

    public function modules() {
        return $this->belongsToMany(Module::class, 'company_modules')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function activeModules() {
        return $this->modules()->wherePivot('is_active', true);
    }

    public function hasModule($moduleSlug) {
        return $this->modules()->where('slug', $moduleSlug)->wherePivot('is_active', true)->exists();
    }
}
