<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Mockery\Matcher\Contains;

class Customer extends Model
{
    use BelongsToCompany, HasUuid;
    
    protected $fillable = [
        'name',
        'CR',
        'TIN',
        'vatNumber',
        'national_address',
        'phone',
        'email',
        'account_id',
        'user_id',
        'company_id',
    ];

    public function contract() {
        return $this->hasOne(Contract::class);
    }

    public function containers() {
        return $this->hasMany(Container::class);
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agingBalance($from, $to, int $minDays, ?int $maxDays = null) {

        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->filter(function ($invoice) use ($minDays, $maxDays) {
                return $invoice->lateDays >= $minDays && ($maxDays === null || $invoice->lateDays <= $maxDays);
            })
            ->sum('total_amount');
    }

    public function agingBalanceCount($from, $to, int $minDays, ?int $maxDays = null) {
    
        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->filter(function ($invoice) use ($minDays, $maxDays) {
                return $invoice->lateDays >= $minDays && ($maxDays === null || $invoice->lateDays <= $maxDays);
            })
            ->count();
    }

    public function totalAgingBalance($from, $to) {
        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->sum('total_amount');
    }

    public function totalAgingBalanceCount($from, $to) {
        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->count();
    }
}
