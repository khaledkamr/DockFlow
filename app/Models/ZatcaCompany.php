<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZatcaCompany extends Model
{
    protected $fillable = [
        'company_id',
        'company_group_id',
        'vat',
        'crn',
        'street',
        'city',
        'sub_division',
        'building_no',
        'plot_no',
        'postal_code',
        'active_env',
        'pro_request_id',
        'pro_private_key',
        'pro_user_secret',
        'pro_user_name',
        'pro_publickey',
        'pro_cert',
        'pro_cert_expire_date',
        'pro_invoice_counter',
        'pro_last_hash',
        'sim_request_id',
        'sim_private_key',
        'sim_user_secret',
        'sim_user_name',
        'sim_publickey',
        'sim_cert',
        'sim_cert_expire_date',
        'sim_invoice_counter',
        'sim_last_hash',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
