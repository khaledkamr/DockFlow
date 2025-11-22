<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'old_data',
        'new_data',
        'diff',
        'ip',
        'user_agent',
        'url',
        'method',
        'company_id',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'diff' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
