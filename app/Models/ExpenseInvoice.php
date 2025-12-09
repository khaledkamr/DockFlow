<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenseInvoice extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'code',
        'date',
        'supplier_id',
        'supplier_invoice_number',
        'payment_method',
        'amount_before_tax',
        'tax_rate',
        'tax',
        'discount_rate',
        'discount',
        'total_amount',
        'is_posted',
        'is_paid',
        'user_id',
        'company_id',
        'expense_account_id',
        'notes',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function items() {
        return $this->hasMany(ExpenseInvoiceItems::class);
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function expense_account() {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    protected static function booted()
    {
        static::creating(function ($expenseInvoice) {
            $year = date('Y');
            $prefix = 'EI';
            $lastExpenseInvoice = self::whereYear('date', $year)->latest('code')->first();
            if ($lastExpenseInvoice && $lastExpenseInvoice->code) {
                $lastNumber = (int) substr($lastExpenseInvoice->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $expenseInvoice->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}
