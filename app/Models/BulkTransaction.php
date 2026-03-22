<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkTransaction extends Model
{
    protected $fillable = [
        'bulk_inventory_id',
        'transaction_type',
        'quantity',
        'balance_after',
        'policy_id',
        'date',
        'notes',    
    ];

    public function bulkInventory() {
        return $this->belongsTo(BulkInventory::class);
    }

    public function policy() {
        return $this->belongsTo(Policy::class);
    }
}
