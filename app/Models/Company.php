<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'branch',
        'logo',
        'CR',           // السجل التجاري
        'TIN',          // الرقم الموحد
        'vatNumber',    // الرقم الضريبي
        'national_address',
        'phone',
        'email'
    ];

    public function modules() {
        return $this->belongsToMany(Module::class, 'company_modules')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function activeModules() {
        return $this->modules()->wherePivot('is_active', true);
    }

    public function hasModule($moduleSlug) {
        return $this->modules()->where('slug', $moduleSlug)->wherePivot('is_active', true)->exists();
    }

    public function bankAccounts() {
        return $this->hasMany(BankAccount::class);
    }

    public function address() {
        return $this->hasOne(CompanyAddress::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function roles() {
        return $this->hasMany(Role::class);
    }

    public function customers() {
        return $this->hasMany(Customer::class);
    }

    public function suppliers() {
        return $this->hasMany(Supplier::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function contracts() {
        return $this->hasMany(Contract::class);
    }

    public function containerTypes() {
        return $this->hasMany(Container_type::class);
    }

    public function zatcaCompany() {
        return $this->hasOne(ZatcaCompany::class);
    }
}
