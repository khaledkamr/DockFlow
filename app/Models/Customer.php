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
        if (!$this->contract) {
            return 0;
        }

        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->filter(function ($invoice) use ($minDays, $maxDays) {
                $paymentDueDate = \Carbon\Carbon::parse($invoice->date)
                    ->addDays((int) ($this->contract->payment_grace_period ?? 0));
                
                $lateDays = \Carbon\Carbon::now()->gt($paymentDueDate) 
                    ? \Carbon\Carbon::parse($paymentDueDate)->diffInDays(\Carbon\Carbon::now()) 
                    : 0;
                
                return $lateDays >= $minDays && ($maxDays === null || $lateDays <= $maxDays);
            })
            ->sum('total_amount');
    }

    public function agingBalanceCount($from, $to, int $minDays, ?int $maxDays = null) {
        if (!$this->contract) {
            return 0;
        }

        return $this->invoices()
            ->where('isPaid', 'لم يتم الدفع')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->filter(function ($invoice) use ($minDays, $maxDays) {
                $paymentDueDate = \Carbon\Carbon::parse($invoice->date)
                    ->addDays((int) ($this->contract->payment_grace_period ?? 0));
                
                $lateDays = \Carbon\Carbon::now()->gt($paymentDueDate) 
                    ? \Carbon\Carbon::parse($paymentDueDate)->diffInDays(\Carbon\Carbon::now()) 
                    : 0;
                
                return $lateDays >= $minDays && ($maxDays === null || $lateDays <= $maxDays);
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
