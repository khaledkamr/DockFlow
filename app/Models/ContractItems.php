<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractItems extends Model
{
    protected $fillable = [
        'contract_id',
        'item'
    ];

    public function contract() {
        return $this->belongsTo(Contract::class);
    }
}
