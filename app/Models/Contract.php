<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'customer_id',
        'company_id',
        'start_date',
        'end_date',
        'company_representative',
        'company_representative_nationality',
        'company_representative_NID',
        'company_representative_role',
        'customer_representative',
        'customer_representative_nationality',
        'customer_representative_NID',
        'customer_representative_role',
        'user_id',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function services() {
        return $this->belongsToMany(Service::class, 'contract_services')
                    ->withPivot(['price', 'unit', 'unit_desc'])
                    ->withTimestamps();
    }

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
