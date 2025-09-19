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
        'code',
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

    protected static function booted()
    {
        static::creating(function ($policy) {
            $year = date('Y');
            if($policy->type == 'تخزين') {
                $prefix = 'ST';
            } elseif($policy->type == 'إستلام') {
                $prefix = 'RE';
            }
            $lastPolicy = self::where('type', $policy->type)->whereYear('date', $year)->latest('id')->first();
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
