<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkBatches extends Model
{
    protected $fillable = [
        'bulk_inventory_id',
        'quantity_in',
        'quantity_remaining',
        'entry_date',
        'policy_id',
    ];

    public function bulkInventory() {
        return $this->belongsTo(BulkInventory::class);
    }

    public function policy() {
        return $this->belongsTo(Policy::class);
    }
}
