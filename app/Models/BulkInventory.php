<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class BulkInventory extends Model
{
    use HasUuid;
    
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

    public function transactions() {
        return $this->hasMany(BulkTransaction::class);
    }

    public function batches() {
        return $this->hasMany(BulkBatch::class);
    }
}
