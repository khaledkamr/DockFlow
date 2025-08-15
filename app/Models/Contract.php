<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'user_id', 
        'start_date', 
        'expected_end_date', 
        'actual_end_date',
        'price', 
        'late_fee', 
        'tax', 
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function containers()
    {
        return $this->belongsToMany(Container::class, 'contract_container');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
