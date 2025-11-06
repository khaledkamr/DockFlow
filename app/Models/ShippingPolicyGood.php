<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPolicyGood extends Model
{
    protected $fillable = [
        'shipping_policy_id',
        'description',
        'quantity',
        'weight',
        'notes',
    ];

    public function shippingPolicy() {
        return $this->belongsTo(ShippingPolicy::class);
    }
}
