<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToCompany, HasUuid;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nationality',
        'NID',
        'phone',
        'avatar',
        'timezone',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions() {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->unique()->toArray();
    }

    public function hasPermission(string $permission) {
        return in_array($permission, $this->permissions());
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
