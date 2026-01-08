<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'name',
        'CR',
        'TIN',
        'vat_number',
        'national_address',
        'phone',
        'email',
        'account_id',
        'settlement_account_id',
        'user_id',
        'company_id',
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function settlementAccount() {
        return $this->belongsTo(Account::class, 'settlement_account_id');
    }

    public function expenseInvoices() {
        return $this->hasMany(ExpenseInvoice::class);
    }

    public function shippingPolicies() {
        return $this->hasMany(ShippingPolicy::class);
    }

    public function transportOrders() {
        return $this->hasMany(TransportOrder::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
