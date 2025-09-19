<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'branch',
        'logo',
        'CR',
        'TIN',
        'national_address',
        'phone',
        'email'
    ];
}
