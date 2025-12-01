<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'type', 
        'plate_number',
        'cost_center_id',
        'company_id',
    ];

    public function driver() {
        return $this->hasOne(Driver::class);
    }

    public function costCenter() {
        return $this->belongsTo(CostCenter::class);
    }

    public function transportOrders() {
        return $this->hasMany(TransportOrder::class);
    }

    public function shippingPolicies() {
        return $this->hasMany(ShippingPolicy::class);
    }
}
