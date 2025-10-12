<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'date',
        'amount',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $year = date('Y');
            $prefix = 'CT';
            $lastTransaction = self::whereYear('date', $year)->latest('id')->first();
            if ($lastTransaction && $lastTransaction->code) {
                $lastNumber = (int) substr($lastTransaction->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $transaction->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
