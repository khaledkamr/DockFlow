<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkInventory extends Model
{
    protected $table = 'bulk_inventory';

    protected $fillable = [
        'customer_id',
        'item_id',
        'balance',
        'price_per_unit',
        'price_type',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function item() {
        return $this->belongsTo(BulkItem::class);
    }
}
