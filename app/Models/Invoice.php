<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use App\Models\Zacta\InvoiceCustomer;
use App\Models\Zacta\InvoiceLine;
use App\Models\Zacta\InvoiceLineNoVat;
use App\Models\Zacta\InvoiceSeller;
use App\Models\Zacta\SimpleInvoiceCustomer;
use App\Models\Zacta\SimpleTaxInvoice;
use App\Models\Zacta\SimpleTaxInvoiceNoVat;
use App\Models\Zacta\TaxInvoice;
use App\Models\Zacta\TaxInvoiceNoVat;
use App\Models\Zacta\TaxClearanceInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use BelongsToCompany, HasUuid;

    public const TYPES = [ 'تخزين', 'خدمات', 'تخليص', 'شحن', 'مسودة', 'تخزين و شحن', 'محاسبية'];
    public const PAYMENT_METHODS = [ 'كاش', 'آجل', 'تحويل بنكي',];
    public const PAYMENT_STATUS = [ 'تم الدفع', 'لم يتم الدفع', 'تم الدفع جزئياً', 'مسودة'];
    
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
        'due_date',
        'is_posted',
        'status',
        'zatca_status',
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

        $paymentDueDate = Carbon::parse($this->due_date);
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

    public function journalEntry() {
        return $this->hasOne(JournalEntry::class);
    }

    public function zatcaInvoice() {
        return $this->hasOne(ZatcaInvoice::class);
    }

    public function invoiceNotes() {
        return $this->hasMany(InvoiceNote::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'invoice_products')
            ->withPivot('quantity', 'price', 'tax', 'price_after_tax', 'total')
            ->withTimestamps();
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $year = $invoice->date ? date('Y', strtotime($invoice->date)) : date('Y');
            if($invoice->status == 'مسودة') {
                $prefix = 'DR';
            } else {
                $prefix = 'IN';
            }

            $lastInvoice = self::where('code', 'like', $year . $prefix . '%')->whereYear('date', $year)->latest('code')->first();
            if ($lastInvoice && $lastInvoice->code) {
                $lastNumber = (int) substr($lastInvoice->code, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $invoice->code = $year . $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

            if (!$invoice->due_date && $invoice->date) { 
                $gracePeriod = optional($invoice->customer?->contract)->payment_grace_period ?? 15; 
                $invoice->due_date = Carbon::parse($invoice->date)->addDays((int) $gracePeriod); 
            } 
        });
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->attachments()->delete();
        });
    }

    public function createZatcaInvoice() {
        $company = $this->company;
        $zatcaCompany = $company->zatcaCompany;
        if (!$zatcaCompany) {
            throw new \Exception('Zatca company information is missing. Please configure it before creating a Zatca invoice.');
        }

        $env = $zatcaCompany->active_env . '_';
        $invoiceDate = Carbon::parse($this->date);
        $invoiceDueDate = Carbon::parse($this->due_date);
        $counter = 1;
        $customer = $this->customer;
        // $isInternational = $customer->country !== $zatcaCompany->country;
        $isInternational = false; 
        $path = app_path('Models/Zacta/signed_properties_template.php');

        if($customer->type == 'شركة') {
            $taxCustomer = new InvoiceCustomer(
                $customer->name,
                $customer->vatNumber,
                $customer->CR,
                $customer->street,
                $customer->city,
                $customer->district,
                $customer->building_number,
                $customer->secondary_number,
                $customer->postal_code
            );
        } else {
            $taxCustomer = new SimpleInvoiceCustomer(
                $customer->name,
            );
        }

        if($customer->type == 'شركة') {
            $invoiceClass = $isInternational ? TaxInvoiceNoVat::class : TaxInvoice::class;
            $invoiceClass = $this->type == 'تخليص' ? TaxClearanceInvoice::class : $invoiceClass;
        } else {
            $invoiceClass = $isInternational ? SimpleTaxInvoiceNoVat::class : SimpleTaxInvoice::class;
        }

        $taxInvoice = new $invoiceClass(
            $this->code,
            $invoiceDate->format('Y-m-d'),
            $invoiceDate->format('H:i:s'),
            $counter + 1,
            $invoiceDueDate->format('Y-m-d')
        );

        $taxSeller = new InvoiceSeller(
            $company->name,
            $zatcaCompany->vat,
            $zatcaCompany->crn,
            $zatcaCompany->street,
            $zatcaCompany->city,
            $zatcaCompany->sub_division,
            $zatcaCompany->building_no,
            $zatcaCompany->plot_no,
            $zatcaCompany->postal_code
        );

        $taxInvoice->setCustomer($taxCustomer);
        $taxInvoice->setSeller($taxSeller);

        $invoiceLineClass = $isInternational ? InvoiceLineNoVat::class : InvoiceLine::class;

        if ($this->type == 'تخزين') {
            foreach ($this->containers as $container) {
                $itemName = 'storage service for container ' . $container->code;
                $uom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $itemName,
                    $container->pivot->amount,
                    1,
                    15.00,
                    $uom
                ));
            }
        } elseif ($this->type == 'شحن') {
            foreach ($this->shippingPolicies as $policy) {
                $itemName = 'shipping service from ' . $policy->from . ' to ' . $policy->to;
                $uom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $itemName,
                    $policy->pivot->amount,
                    1,
                    15.00,
                    $uom
                ));
            }
        } elseif ($this->type == 'خدمات') {
            foreach ($this->containers as $container) {
                $itemName = $container->services->first()->description . ' for container ' . $container->code;
                $uom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $itemName,
                    $container->services->first()->pivot->price,
                    1,
                    15.00,
                    $uom
                ));
            }
        } elseif ($this->type == 'تخليص') {
            foreach ($this->clearanceInvoiceItems->sortBy('number') as $item) {
                $invoiceLineClass = $item->tax > 0 ? InvoiceLine::class : InvoiceLineNoVat::class;
                $vat = $item->tax > 0 ? 15.00 : 0.00;
                $uom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $item->description,
                    $item->amount,
                    1,
                    $vat,
                    $uom
                ));
            }
        } elseif ($this->type == 'تخزين و شحن') {
            // Storage lines
            foreach ($this->containers as $container) {
                $storageItemName = 'storage service for container ' . $container->code;
                $storageUom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $storageItemName,
                    $container->pivot->amount,
                    1,
                    15.00,
                    $storageUom
                ));
            }

            // Shipping lines
            foreach ($this->shippingPolicies as $policy) {
                $shippingItemName = 'shipping service from ' . $policy->from . ' to ' . $policy->to;
                $shippingUom = 'PCE';

                $taxInvoice->addInvoiceLine(new $invoiceLineClass(
                    $shippingItemName,
                    $policy->pivot->amount,
                    1,
                    15.00,
                    $shippingUom
                ));
            }
        }

        $taxInvoice->privateKey = $zatcaCompany->{$env . 'private_key'};
        $taxInvoice->certificate = $zatcaCompany->{$env . 'cert'};
        $preHash = $zatcaCompany->{$env . 'last_hash'};
        $taxInvoice->processInvoice($preHash, $path);

        $this->load('zatcaInvoice');
        $zatkaInvoice = $this->zatcaInvoice;

        DB::transaction(function () use ($zatcaCompany, $counter, $env, $taxInvoice, $preHash, $zatkaInvoice) {
            $zatcaCompany->update([
                $env . 'invoice_counter' => $counter + 1,
                $env . 'last_hash' => $taxInvoice->hash
            ]);
            $finalXML = $taxInvoice->getFinalXML();
            if(is_null($zatkaInvoice)){
                $this->zatcaInvoice()->create([
                    'invoice_uuid' => $taxInvoice->uuid,
                    'invoice_hash' => $taxInvoice->hash,
                    'pre_hash' => $preHash,
                    'request_xml' => $finalXML,
                    'encoded_xml' => $taxInvoice->encodedInvoice,
                    'qr_data' => $taxInvoice->qr
                ] + (getDataFromXml($finalXML) ?? []));
            }else{
                $zatkaInvoice->update([
                    'invoice_uuid' => $taxInvoice->uuid,
                    'invoice_hash' => $taxInvoice->hash,
                    'pre_hash' => $preHash,
                    'request_xml' => $finalXML,
                    'encoded_xml' => $taxInvoice->encodedInvoice,
                    'qr_data' => $taxInvoice->qr
                ] + (getDataFromXml($finalXML) ?? []));
            }
        });

        $this->load('zatcaInvoice');
        $taxInvoice->zactaInvoice = $this->zatcaInvoice;
        // dd($taxInvoice);
        return $taxInvoice;
    }

    public function submit_invoice() {
        $customer = $this->customer;
        $invoice = $this;
        $taxInvoice = $invoice->createZatcaInvoice();
        $company = $invoice->company;
        $zatcaCompany = $company->zatcaCompany;

        $simUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/invoices/clearance/single";
        $proUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/invoices/clearance/single";
        $simSimpleUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/invoices/reporting/single";
        $proSimpleUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/invoices/reporting/single";
        $url = $proUrl;

        if ($customer->type == 'فرد') {
            $url = $proSimpleUrl;
        }

        if (is_null($zatcaCompany)) {
            throw new \Exception('Zatca company information is missing. Please configure it before submitting a Zatca invoice.');
        }

        $env = $zatcaCompany->active_env . '_';
        if ($env == 'sim_') {
            if ($customer->type == 'فرد') {
                $url = $simSimpleUrl;
            } else {
                $url = $simUrl;
            }
        }

        try {
            $response = TaxInvoice::submitSinvoice($zatcaCompany->{$env . 'user_name'}, $zatcaCompany->{$env . 'user_secret'}, $url, $taxInvoice->hash, $taxInvoice->uuid, $taxInvoice->encodedInvoice);
            $body = $response->json();
            $bodyRaw = $response->body();
            Log::error('zatca submit invoice body',[$bodyRaw]);
            if (!is_null($body)) {
                $records_from_xml = getDataFromXml($taxInvoice->zactaInvoice->request_xml) ?? [];
                $invoice_amount = abs($this->invoice_amount);
                $diff_invoice_amount = round($invoice_amount - abs($this->invoice_vat_amount) - $records_from_xml['invoice_amount'], 2);
                $diff_invoice_vat_amount = round(abs($this->invoice_vat_amount) - $records_from_xml['invoice_vat_amount'], 2);
                $diff_invoice_total = round($invoice_amount - $records_from_xml['invoice_total'], 2);
                $diff_status = $diff_invoice_amount > 0 || $diff_invoice_vat_amount > 0 || $diff_invoice_total > 0;

                $invoice->update([
                    'zatca_status' => $body['clearanceStatus'] == 'NOT_CLEARED' ? 'sent with error' : 'sent without error',
                ]);

                $taxInvoice = $taxInvoice->zactaInvoice->update([
                        'status' => $body['clearanceStatus'],
                        'response_log' => $bodyRaw,
                        'request_date' => Carbon::now()
                    ] +
                    [
                        'diff_invoice_amount' => $diff_invoice_amount,
                        'diff_invoice_vat_amount' => $diff_invoice_vat_amount,
                        'diff_invoice_total' => $diff_invoice_total,
                        'diff_status' => $diff_status,
                    ]);
            } else {
                if ($response->unauthorized()) {
                    $taxInvoice = $taxInvoice->zactaInvoice->update([
                        'response_log' => json_encode(["validationResults" => [
                            "errorMessages" => [["message" => "Unauthorized"]],
                            "warningMessages" => []
                        ]]),
                        'request_date' => Carbon::now()
                    ]);
                } else {
                    $taxInvoice = $taxInvoice->zactaInvoice->update([
                        'response_log' => json_encode(["validationResults" => [
                            "errorMessages" => [["message" => $response->status()]],
                            "warningMessages" => []
                        ]
                        ]),
                        'request_date' => Carbon::now()
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('zatca submit invoice', [$e]);
            $invoice->update(['zatca_status' => 'sent with error']);
            $taxInvoice = $taxInvoice->zactaInvoice->update([
                'response_log' => json_encode(["validationResults" => [
                    "errorMessages" => [["message" => $e->getMessage()]],
                    "warningMessages" => []
                ]]),
                'response_date' => Carbon::now(),
            ]);
        }
    }
}
