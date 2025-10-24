<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class TransportOrder extends Model
{
    use HasUuid, BelongsToCompany;
    
    protected $fillable = [
        'transaction_id',
        'customer_id',
        'driver_id',
        'vehicle_id',
        'supplier_id',
        'code',
        'type',
        'from',
        'to',
        'date',
        'notes',
        'diesel_cost',
        'driver_wage',
        'other_expenses',
        'user_id',
        'company_id',
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function containers() {
        return $this->belongsToMany(Container::class, 'transport_orders_containers');
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::creating(function ($transportOrder) {
            $year = date('Y');
            $prefix = 'TO';
            $lastTransportOrder = self::whereYear('date', $year)->latest('id')->first();
            if ($lastTransportOrder && $lastTransportOrder->code) {
                $lastNumber = (int) substr($lastTransportOrder->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $transportOrder->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
