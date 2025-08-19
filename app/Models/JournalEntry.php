<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'code',
        'voucher_id', 
        'date',
    ];

    public function lines() {
        return $this->hasMany(JournalEntryLine::class);
    }
}
