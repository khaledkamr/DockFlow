<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // use BelongsToCompany;

    protected $fillable = [
        "name",
    ];

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
