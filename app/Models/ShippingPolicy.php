<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ShippingPolicy extends Model
{
    use HasUuid, BelongsToCompany;
    
    protected $fillable = [
        'uuid',
        'code',
        'customer_id',
        'driver_id',
        'vehicle_id',
        'supplier_id',
        'driver_name',
        'driver_contact',
        'vehicle_plate',
        'type',
        'date',
        'from',
        'to',
        'duration',
        'notes',
        'supplier_cost',
        'diesel_cost',
        'driver_wage',
        'other_expenses',
        'client_cost',
        'is_received',
        'company_id',
        'user_id',
    ];

    public function goods() {
        return $this->hasMany(ShippingPolicyGood::class);
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

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoices() {
        return $this->belongsToMany(Invoice::class, 'invoice_shipping')->withPivot('amount')->withTimestamps();
    }

    protected static function booted()
    {
        static::creating(function ($policy) {
            $year = date('Y');
            $prefix = 'SP';
            $lastPolicy = self::whereYear('date', $year)->latest('id')->first();
            if ($lastPolicy && $lastPolicy->code) {
                $lastNumber = (int) substr($lastPolicy->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $policy->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
