<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = ['status', 'container_type_id', 'user_id', 'location'];

    public function containerType()
    {
        return $this->belongsTo(Container_type::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
