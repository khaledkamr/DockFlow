<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use BelongsToCompany, HasUuid;
    
    protected $fillable = [
        'contract_id',
        'customer_id',
        'external_customer',
        'driver_name',
        'driver_NID',
        'driver_number',
        'driver_car',
        'car_code',
        'date',
        'code',
        'type',
        'tax_statement',
        'user_id',
        'company_id',
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

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::creating(function ($policy) {
            $year = date('Y');

            if($policy->type == 'تخزين') {
                $prefix = 'ST';
            } elseif($policy->type == 'تسليم') {
                $prefix = 'RE';
            } elseif($policy->type == 'خدمات') {
                $prefix = 'SV';
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
