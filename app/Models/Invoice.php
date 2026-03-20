<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToCompany, HasUuid;

    public const TYPES = [ 'تخزين', 'خدمات', 'تخليص', 'شحن',];
    public const PAYMENT_METHODS = [ 'كاش', 'آجل', 'تحويل بنكي',];
    public const PAYMENT_STATUS = [ 'تم الدفع', 'لم يتم الدفع',];
    
    protected $fillable = [
        'type',
        'customer_id', 
        'code',
        'amount_before_tax',
        'tax_rate',
        'tax',
        'discount',
        'amount_after_discount',
        'total_amount',
        'paid_amount',
        'payment_method',
        'date',
        'is_posted',
        'status',
        'user_id',
        'company_id',
        'notes',
    ];

    protected $appends = ['paymentDueDate', 'lateDays'];

    public function getPaymentDueDateAttribute() {
        if($this->customer->contract) {
            $paymentGracePeriod = (int) $this->customer->contract->payment_grace_period ?? 15; // Default to 15 days if not set
        } else {
            $paymentGracePeriod = 15; // Default to 15 days if no contract
        }

        if ($this->date) {
            return Carbon::parse($this->date)->addDays($paymentGracePeriod)->format('Y/m/d');
        }

        return null;
    }

    public function getLateDaysAttribute() {
        if ($this->status == 'تم الدفع' || !$this->date) {
            return 0;
        }

        $paymentDueDate = Carbon::parse($this->payment_due_date);
        $currentDate = Carbon::now();
        if($currentDate->greaterThan($paymentDueDate)) {
            return (int) $paymentDueDate->diffInDays($currentDate);
        }

        return 0;
    }

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

    public function shippingPolicies() {
        return $this->belongsToMany(ShippingPolicy::class, 'invoice_shipping')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function clearanceInvoiceItems() {
        return $this->hasMany(ClearanceInvoiceItem::class, 'invoice_id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function payments() {
        return $this->hasMany(InvoicePayment::class);
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $year = $invoice->date ? date('Y', strtotime($invoice->date)) : date('Y');
            $prefix = 'IN';
            $lastInvoice = self::whereYear('date', $year)->latest('code')->first();
            if ($lastInvoice && $lastInvoice->code) {
                $lastNumber = (int) substr($lastInvoice->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $invoice->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->attachments()->delete();
        });
    }
}
