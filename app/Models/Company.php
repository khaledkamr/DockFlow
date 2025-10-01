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
}
