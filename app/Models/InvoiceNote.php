<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class InvoiceNote extends Model
{
    use HasUuid, BelongsToCompany;

    protected $fillable = [
        'code',
        'invoice_id',
        'type',
        'date',
        'reason',
        'amount',
        'tax',
        'total',
        'status',
        'is_posted',
        'zatca_status',
        'user_id',
        'company_id',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::creating(function ($note) {
            $year = $note->date ? date('Y', strtotime($note->date)) : date('Y');
            if($note->type == 'credit') {
                $prefix = 'CN';
            } else {
                $prefix = 'DN';
            }

            $lastNote = self::where('code', 'like', $year . $prefix . '%')->whereYear('date', $year)->latest('code')->first();
            if ($lastNote && $lastNote->code) {
                $lastNumber = (int) substr($lastNote->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $note->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
