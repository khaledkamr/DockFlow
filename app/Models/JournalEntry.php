<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'code',
        'date',
        'amount',
        'made_by',
        'modified_by',
        'voucher_id'
    ];

    public function lines() {
        return $this->hasMany(JournalEntryLine::class);
    }
}
