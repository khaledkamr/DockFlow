<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $fillable = [
        'customer_id', 
        'code',
        'amount',
        'payment_method',
        'date',
        'payment',
        'user_id'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function containers() {
        return $this->belongsToMany(Container::class, 'invoice_containers')
            ->withPivot('amount')
            ->withTimestamps();
    }
    
    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $year = date('Y');
            $prefix = 'IN';
            $lastInvoice = self::whereYear('date', $year)->latest('id')->first();
            if ($lastInvoice && $lastInvoice->code) {
                $lastNumber = (int) substr($lastInvoice->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $invoice->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
