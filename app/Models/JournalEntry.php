<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use BelongsToCompany;
    
    protected $fillable = [
        'code',
        'type',
        'date',
        'totalDebit',
        'totalCredit',
        'user_id',
        'modified_by',
        'voucher_id',
        'company_id',
    ];

    public function lines() {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($journalEntry) {
            if($journalEntry->voucher) {
                $journalEntry->type = $journalEntry->voucher->type;
                $journalEntry->code = $journalEntry->voucher->code;
            } else {
                $year = date('Y');
                $journalEntry->type = 'قيد يومي';
                $prefix = 'JD';
                $lastJournal = self::where('type', $journalEntry->type)->whereYear('date', $year)->latest('id')->first();
                if ($lastJournal && $lastJournal->code) {
                    $lastNumber = (int) substr($lastJournal->code, -5);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                $journalEntry->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
