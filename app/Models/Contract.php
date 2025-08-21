<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'customer_id',
        'company_id',
        'start_date',
        'end_date',
        'company_representative',
        'company_representative_nationality',
        'company_representative_NID',
        'company_representative_role',
        'customer_representative',
        'customer_representative_nationality',
        'customer_representative_NID',
        'customer_representative_role',
        'service_one',
        'container_storage_price',
        'container_storage_period',
        'service_two',
        'move_container_price',
        'move_container_count',
        'service_three',
        'late_fee',
        'late_fee_period',
        'service_four',
        'exchange_container_price',
        'exchange_container_count',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }
}
