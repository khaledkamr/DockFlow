<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'contract_id',
        'customer_id',
        'driver_name',
        'driver_NID',
        'driver_car',
        'car_code',
        'date',
        'type',
        'storage_price',
        'late_fee',
        'tax'
    ];

    public function contract() {
        return $this->belongsTo(Contract::class);
    }
    
    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function containers() {
        return $this->belongsToMany(Container::class, 'policy_container');
    }

    public function invoice() {
        return $this->hasOne(Invoice::class);
    }
}
