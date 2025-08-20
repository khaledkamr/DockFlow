<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'customer_id',
        'start_date',
        'end_date',
        'price',
        'late_fee',
        'tax'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function items() {
        return $this->hasMany(ContractItems::class);
    }
}
