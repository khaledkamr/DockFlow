<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class InvoiceStatement extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'uuid',
        'code',
        'customer_id',
        'subtotal',
        'tax',
        'amount',
        'payment_method',
        'date',
        'notes',
        'company_id',
        'user_id',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function invoices() {
        return $this->belongsToMany(Invoice::class, 'invoice_statement_invoices');
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    protected static function booted() {
        static::creating(function ($invoiceStatement) {
            $year = date('Y');
            $prefix = 'IS';
            $lastInvoiceStatement = self::whereYear('date', $year)->latest('id')->first();
            if ($lastInvoiceStatement && $lastInvoiceStatement->code) {
                $lastNumber = (int) substr($lastInvoiceStatement->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $invoiceStatement->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
