<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseInvoiceItems extends Model
{
    protected $fillable = [
        'expense_invoice_id',
        'account_id',
        'description',
        'cost_center_id',
        'quantity',
        'price',
        'amount',
        'tax',
        'total_amount',
    ];
    
    public function expenseInvoice() {
        return $this->belongsTo(ExpenseInvoice::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function costCenter() {
        return $this->belongsTo(Account::class, 'cost_center_id');
    }
}
