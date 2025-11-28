<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id', 
        'account_id', 
        'debit',      // مبلغ المدين
        'credit',     // مبلغ الدائن
        'description'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function journal() {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
