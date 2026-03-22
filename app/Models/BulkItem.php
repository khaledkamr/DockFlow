<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkItem extends Model
{
    protected $fillable = [
        'name',
        'unit'
    ];

    public function inventories() {
        return $this->hasMany(BulkInventory::class, 'item_id');
    }
}
