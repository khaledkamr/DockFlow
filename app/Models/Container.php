<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = [
        'code', 
        'status', 
        'received_by',
        'delivered_by',
        'container_type_id', 
        'customer_id', 
        'location',
        'notes',
        'date',
        'exit_date'
    ];

    public function containerType() {
        return $this->belongsTo(Container_type::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function policies() {
        return $this->belongsToMany(Policy::class, 'policy_container');
    }

    public function invoices() {
        return $this->belongsToMany(Invoice::class, 'invoice_containers')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
