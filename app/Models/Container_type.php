<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container_type extends Model
{
    protected $fillable = ['name', 'daily_price'];

    public function containers()
    {
        return $this->hasMany(Container::class);
    }
}
