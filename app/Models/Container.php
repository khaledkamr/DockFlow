<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use BelongsToCompany, HasUuid;

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
        'exit_date',
        'company_id',
        'user_id',
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

    public function transactions() {
        return $this->belongsToMany(Transaction::class, 'transaction_containers');
    }

    public function invoices() {
        return $this->belongsToMany(Invoice::class, 'invoice_containers')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function services() {
        return $this->belongsToMany(Service::class, 'container_services')
            ->withPivot('price', 'notes')
            ->withTimestamps();
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
