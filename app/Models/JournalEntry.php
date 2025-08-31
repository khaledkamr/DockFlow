<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'code',
        'date',
        'totalDebit',
        'totalCredit',
        'made_by',
        'modified_by',
        'voucher_id'
    ];

    public function lines() {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class);
    }
}
