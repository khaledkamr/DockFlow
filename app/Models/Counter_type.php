<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter_type extends Model
{
    protected $fillable = ['name', 'daily_price'];

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }
}
