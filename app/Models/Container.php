<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = ['code', 'status', 'container_type_id'];

    public function containerType()
    {
        return $this->belongsTo(Container_type::class);
    }

    public function contracts()
    {
        return $this->belongsTo(Contract::class);
    }
}
