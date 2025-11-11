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
        'account_id',
        'company_id',
    ];

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function transportOrders() {
        return $this->hasMany(TransportOrder::class);
    }

    public function shippingPolicies() {
        return $this->hasMany(ShippingPolicy::class);
    }
}
