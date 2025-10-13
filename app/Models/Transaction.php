<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'date',
        'tax_statement',
        'tax_statement_date',
        'code',
        'contract_id',
        'customer_id',
        'user_id',
        'company_id',
    ];

    public function containers() {
        return $this->belongsToMany(Container::class, 'transaction_containers');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function items() {
        return $this->hasMany(TransactionItem::class);
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $year = date('Y');
            $prefix = 'CT';
            $lastTransaction = self::whereYear('date', $year)->latest('id')->first();
            if ($lastTransaction && $lastTransaction->code) {
                $lastNumber = (int) substr($lastTransaction->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $transaction->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
