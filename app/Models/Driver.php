<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'NID',
        'phone',
        'vehicle_id',
        'cost_center_id',
        'company_id',
    ];

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
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
