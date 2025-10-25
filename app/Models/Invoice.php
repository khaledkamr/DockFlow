<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToCompany, HasUuid;

    public const TYPES = [ 'تخزين', 'خدمات', 'تخليص',];
    public const PAYMENT_METHODS = [ 'كاش', 'آجل', 'تحويل بنكي',];
    public const PAYMENT_STATUS = [ 'تم الدفع', 'لم يتم الدفع',];
    
    protected $fillable = [
        'type',
        'customer_id', 
        'code',
        'amount_before_tax',
        'tax',
        'discount',
        'amount_after_discount',
        'total_amount',
        'payment_method',
        'date',
        'isPaid',
        'user_id',
        'company_id',
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

    public function company() {
        return $this->belongsTo(Company::class);
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
