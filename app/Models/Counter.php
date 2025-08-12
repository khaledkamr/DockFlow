<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = ['code', 'status', 'counter_type_id'];

    public function counterType()
    {
        return $this->belongsTo(Counter_type::class);
    }

    public function contracts()
    {
        return $this->belongsTo(Contract::class);
    }
}
