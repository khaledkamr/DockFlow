<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'contract_id',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'price',
        'status'
    ];

    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function containers() {
        return $this->belongsToMany(Container::class, 'contract_container');
    }

    public function invoice() {
        return $this->hasOne(Invoice::class);
    }
}
