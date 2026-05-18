<?php

namespace App\Models;

use Carbon\Carbon;
use App\Zacta\TaxInvoice;
use App\Zacta\TaxInvoiceNoVat;
use App\Zacta\InvoiceLine;
use App\Zacta\InvoiceLineNoVat;
use App\Models\CompanyZatca;
use App\Models\InvoiceZatca;
use App\Zacta\CreditInvoice;
use App\Zacta\InvoiceSeller;
use App\Zacta\InvoiceCustomer;
use App\Zacta\SimpleTaxCredit;
use App\Zacta\SimpleTaxInvoice;
use App\Zacta\SimpleTaxInvoiceNoVat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;
use App\Zacta\SimpleInvoiceCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;
use  App\WhatsApp\HillProvider;
use App\Models\Customer;
use NumberToWords\NumberToWords;

class InvoiceHd extends Model
{
    use HasFactory;
    use Sortable;

    protected $table = 'invoice_header';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $primaryKey = 'invoice_id';

    protected $dates = ['invoice_due_date', 'invoice_date', 'supply_date'];

    public $sortable = ['invoice_no', 'invoice_date', 'customer_id'];
    protected $guarded = [];

//    protected $appends = ['detailsTotal'];

    public function getCreatedDateAttribute($value)
    {
        return date('d-m-Y h:m A', strtotime($value));
    }

    public function getInvoiceDateAttribute($value)
    {
        return date('Y-m-d h:m', strtotime($value));
    }

    public function waybill()
    {
        return $this->hasOne('App\Models\WaybillHd', 'waybill_invoice_id');
    }

    public function discountInvoice()
    {
        return $this->belongsTo('App\Models\InvoiceHd', 'credit_invoice_id');
    }

    public function getSupplyDateAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Riyadh')->format('Y-m-d');
    }

    public function invoiceReturn()
    {
        /////////////ربط اشعار الخصم بالفاتوره الي عليها الخصم
        return $this->belongsTo('App\Models\InvoiceHd', 'credit_invoice_id');
    }

    public function invoiceReturnStation()
    {
        /////////////ربط اشعار الخصم بالفاتوره الي عليها الخصم
        return $this->belongsTo('App\Models\StationInvoiceQR', 'station_inv_id');
    }

    public function waybillCars()
    {
        return $this->hasMany('App\Models\WaybillHd', 'waybill_invoice_id');
    }

    public function getInvoiceDueDateAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Riyadh')->format('Y-m-d');
    }

//    public function getInvoiceCreatedDateAttribute($value)
//    {
//        return Carbon::parse($value)->format('Y-m-d\TH:i');
//    }

    public function waybillDiesel()
    {
        return $this->hasOne('App\Models\WaybillHd', 'purchase_invoice_id');
    }

    public function bond()
    {
        return $this->belongsTo('App\Models\Bond', 'bond_code');
    }

    public function bondsCapture()
    {
        return $this->belongsTo('App\Models\Bond', 'transaction_id')
            ->where('transaction_type',10);
    }

    public function journalPurchase()
    {
        return $this->belongsTo('App\Models\JournalHd', 'journal_hd_id')
            ->where('journal_category_id', 34);
    }

    public function journalHd()
    {
        return $this->belongsTo('App\Models\JournalHd', 'journal_hd_id');
    }

    public function journalDts(): HasMany
    {
        return $this->hasMany('App\Models\JournalDt', 'cc_voucher_id');
    }

    public function journalHdCars()
    {
        return $this->belongsTo('App\Models\JournalHd', 'journal_hd_id');
    }


    public function journalHdReturn() ////فاتوره مرتجع اشعار الخصم
    {
        return $this->belongsTo('App\Models\JournalHd', 'journal_hd_id');
    }

    public function journalHdDiesel()  ////قيد فاتوره البيع لديزل
    {
        return $this->belongsTo('App\Models\JournalHd', 'journal_hd_id')
            ->where('journal_category_id', 38);
    }


    public function companyGroup()
    {
        return $this->belongsTo('App\Models\CompanyGroup', 'company_group_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function report_url()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73001');
    }

    public function report_sample()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73002');
    }

    public function report_url_acc()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73003');
    }

    public function report_url_purchase()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '7311');
    }

    // public function report_url_car_index()
    // {
    //     return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73019');
    // }

    public function report_url_car()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73009');
    }

    public function report_url_car_10()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73010');
    }

    public function report_url_car_11()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73011');
    }

    public function report_url_car_12()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73012');
    }

    public function report_url_car_13()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73013');
    }

    public function report_url_car_14()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73014');
    }

    public function report_url_all()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73005');
    }

    public function report_url_credit()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73008');
    }

    public function report_url_debit()
    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73007');
    }

    public function report_url_return()

    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '73004');
    }

    public function report_url_cargo_smal()

    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '910001');
    }

    public function report_url_cargo_smal_dt()

    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '910002');
    }


    public function report_url_cargo_smal_dt_1()

    {
        return $this->belongsTo('App\Models\Reports', 'company_id', 'company_id')->where('report_code', '910003');
    }


    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function Waybilltickno()
    {
        return $this->hasOne('App\Models\WaybillHd', 'waybill_invoice_id', 'invoice_id');
    }


    public function Waybillptickno()
    {
        return $this->hasOne('App\Models\WaybillHd', 'purchase_invoice_id', 'invoice_id');
    }

    public function userCreated()
    {
        return $this->belongsTo('App\Models\User', 'created_user');
    }

    public function userUpdated()
    {
        return $this->belongsTo('App\Models\User', 'updated_user');
    }

    public function AccountPeriod()
    {
        return $this->belongsTo('App\Models\AccounPeriod', 'acc_period_id');
    }

    public function invoiceDetails()
    {
        return $this->hasMany('App\Models\InvoiceDt', 'invoice_id');
    }

    public function invoiceDetail()
    {
        return $this->hasOne('App\Models\InvoiceDt', 'invoice_id');
    }

    public function zatcaInvoice()
    {
        return $this->hasOne(InvoiceZatca::class, 'invoice_header_id', 'invoice_id');
    }

    public function invoicestatus()
    {
        return $this->hasOne('App\Models\SystemCodeCode', 'system_code', 'invoice_status');
    }

    public function invoicemeth()
    {
        return $this->hasOne('App\Models\SystemCodeCode', 'system_code', 'payment_tems');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\SystemCode', 'payment_tems');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CarRentContract::class, 'contract_id');
    }


    public function getTotalValueinvoice()
    {

        return $this->invoiceDetails->sum(function (InvoiceDt $invoice_detail) {

            return ($invoice_detail->invoice_item_amount + $invoice_detail->invoice_item_vat_amount - $invoice_detail->invoice_discount_total);

        });

    }

    public function createZactaInvoice()
    {
        // dd($this->company_id);
        $company = company::find($this->company_id);
        // dd($company);
        $branch = $this->branch;
        // $companyGroup = CompanyGroup::where('company_group_id',$company->company_group_id)->first();
        $companyZacta = CompanyZatca::where('company_id', $this->company_id)->first();
        if (is_null($companyZacta)) {
            return;
        }
        // dd($companyZacta);
        $env = $companyZacta->active_env . '_';
        $invoiceDate = Carbon::parse($this->invoice_date);
        $deliveryDate = Carbon::parse($this->invoice_due_date)->format('Y/m/d');
        $details = $this->invoiceDetails;
        $counter = 1;//intval($companyZacta->{$env.'invoice_counter'});
        $customer = $this->customer;
        $isInternational = $customer->customer_category == "132005";
        $customerType = optional($customer->cus_type)->system_code;
        $path = app_path("Zacta/signed_properties_template.php");
        $customerTaxNo = trim($this->customer_tax_no);
        $taxCustomer = new InvoiceCustomer($customer->customer_name_full_ar, $customerTaxNo, $customer->customer_address_1, $customer->customer_address_2, $customer->postal_box, $customer->build_no, $customer->unit_no, $customer->postal_code);
        if (($customerType == "74" || $customerType == "538") && strlen($customerTaxNo) == 15) {
            $taxCustomer = new InvoiceCustomer($customer->customer_name_full_ar, $customerTaxNo, $customer->customer_address_1 ?? $companyZacta->city, $customer->customer_address_2 ?? $companyZacta->city,
                $customer->postal_box ?? $companyZacta->postal_code, $customer->build_no ?? $companyZacta->building_no, $customer->unit_no ?? $companyZacta->plot_no, $customer->postal_code ?? $companyZacta->postal_code);
        }
        // dd( $companyZacta->building_no);
//538 Ind
        if ($this->invoice_type == "8" || $this->invoice_type == "998") {

            if (($customerType == "74" || $customerType == "538") && (empty($customerTaxNo) || $customerTaxNo == "0")) {
                $refInvoice = InvoiceHd::find($this->credit_invoice_id);
                $taxInvoice = new SimpleTaxCredit($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);
                $taxInvoice->setCreditInfo($refInvoice->invoice_no, $this->invoice_notes);
            } else {
                $taxInvoice = new CreditInvoice($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);
                $refInvoice = InvoiceHd::find($this->credit_invoice_id);
                $taxInvoice->setCreditInfo($refInvoice->invoice_no, $this->invoice_notes);

            }

        } else {
            if (($customerType == "74" || $customerType == "538") && (empty($customerTaxNo) || $customerTaxNo == "0")) {
                if($isInternational){
                    $taxInvoice = new SimpleTaxInvoiceNoVat($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);
                }else{
                    $taxInvoice = new SimpleTaxInvoice($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);
                }
                $taxCustomer = new SimpleInvoiceCustomer($customer->customer_name_full_ar, $customerTaxNo, $customer->customer_address_1, $customer->customer_address_2, $customer->postal_box, $customer->build_no, $customer->unit_no, $customer->postal_code);
            } else {
                if($isInternational){
                    $taxInvoice = new TaxInvoiceNoVat($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);

                }else{
                    $taxInvoice = new TaxInvoice($this->invoice_no, $invoiceDate->format('Y/m/d'), $invoiceDate->format('H:i:s'), $counter + 1, $deliveryDate);
                }
            }
        }
        $crn = $branch->branch_cr ?? $companyZacta->crn;
        $taxSeller = new InvoiceSeller($companyZacta->seller_name ?? $company->company_name_ar, $companyZacta->vat, $crn, $companyZacta->street, $companyZacta->city, $companyZacta->sub_division, $companyZacta->building_no, $companyZacta->plot_no, $companyZacta->postal_code);
        $taxInvoice->setCustomer($taxCustomer);
        $taxInvoice->setSeller($taxSeller);


        foreach ($details as $line) {
            $itemName = empty(trim($line->invoice_item_notes)) ? optional($line->invoiceItemSetting)->system_code_name_ar : $line->invoice_itemـnotes;
            $uom = optional($line->invoiceItemUnit)->zacta_unit_code ?? "PCE";
            $vat = floatval($line->invoice_item_vat_rate);
            if ($vat < 1) {
                $vat = abs(round($vat * 100, 2));
            }
            if ($line->invoice_item_quantity <= 0) {
                $line->invoice_item_quantity = 1;
            }
            $totalPrice = abs(floatval($line->invoice_item_amount));
            $itemPrice =  $totalPrice / (abs(floatval($line->invoice_item_quantity ?? 1)));
            $itemPrice = round($itemPrice, 2);
            if($isInternational){
                $taxInvoice->addInvoiceLine(new InvoiceLineNoVat($itemName, $itemPrice, abs(floatval($line->invoice_item_quantity)), $vat, $uom));
            }else{
                $taxInvoice->addInvoiceLine(new InvoiceLine($itemName, $itemPrice, abs(floatval($line->invoice_item_quantity)), $vat, $uom));

            }
        }


        $taxInvoice->privateKey = $companyZacta->{$env . 'private_key'};
        $taxInvoice->certificate = $companyZacta->{$env . 'cert'};
        $preHash = $companyZacta->{$env . 'last_hash'};
        $taxInvoice->processInvoice($preHash, $path);
        // dd($taxInvoice->encodedInvoice,$taxInvoice);
        $this->load('zatcaInvoice');
        $zatkaInvoice = $this->zatcaInvoice;
        DB::transaction(function () use ($companyZacta, $counter, $env, $taxInvoice, $preHash, $zatkaInvoice) {
            $companyZacta->update([
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

    public function isAfter2024()
    {
        $date = Carbon::parse($this->invoice_date);
        $year2024 = Carbon::parse('2024-01-01 00:00:00');
        return !$date->isBefore($year2024);
    }

    public function validateInvoice()
    {
        $invoice = $this;
        $customer = $invoice->customer;
        $errors = [];
        $message = ["<ul>"];
        if (empty(trim($customer->customer_address_1))) {
            $errors[] = "يجب اضافه عنوان العميل";
        }
        if (empty(trim($customer->customer_address_2))) {
            $errors[] = "يجب اضافه المدينه في بيانات العميل";
        }
        if (empty(trim($customer->postal_box))) {
            $errors[] = "يجب اضافه المنطقه في بيانات العميل";
        }
        if (empty(trim($customer->build_no))) {
            $errors[] = "يجب اضافه رقم المبني في بيانات العميل";
        }
        if (empty(trim($customer->unit_no))) {
            $errors[] = "يجب اضافه رقم الوحده في بيانات العميل";
        }
        if (empty(trim($customer->postal_code))) {
            $errors[] = "يجب اضافه  الرمز البريدي في بيانات العميل";
        }

        if ($invoice->invoice_type == "8") {
            if (empty(trim($invoice->invoice_notes))) {
                $errors[] = "يجب اضافه سبب اصدار اشعار الخصم";
            }
            $refInvoice = InvoiceHd::find($this->credit_invoice_id);
            if (is_null($refInvoice)) {
                $errors[] = "يجب اضافه الفاتوره المصدر لها اشعار الخصم";
            }
        }
        return $errors;

    }

    public function isValidToSend()
    {
        return count($this->validateInvoice()) == 0;
    }


    public function submit_invoice()
    {
        $customer = $this->customer;
        $customerType = optional($customer->cus_type)->system_code;
        $invoice = $this;
        if ($invoice->invoice_type == "8" || $this->invoice_type == "998") {
            $taxInvoice = $invoice->createZactaInvoice();
        } else {
            $taxInvoice = $invoice->createZactaInvoice();
        }
        $company = Company::find($invoice->company_id);

        $companyZacta = $company->companyZatca;
        $simUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/invoices/clearance/single";
        $proUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/invoices/clearance/single";
        $simSimpleUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/invoices/reporting/single";
        $proSimpleUrl = "https://gw-fatoora.zatca.gov.sa/e-invoicing/core/invoices/reporting/single";
        $url = $proUrl;
        if ($customerType == "538") {
            $url = $proSimpleUrl;
        }
        if (is_null($companyZacta)) {
            //error response;
            return;
        }
        $env = $companyZacta->active_env . '_';
        if ($env == 'sim') {
            if ($customerType == "538") {
                $url = $simSimpleUrl;
            } else {
                $url = $simUrl;
            }
        }
        try {
            // throw new \Exception("test");
            $response = TaxInvoice::submitSinvoice($companyZacta->{$env . 'user_name'}, $companyZacta->{$env . 'user_secret'}, $url, $taxInvoice->hash, $taxInvoice->uuid, $taxInvoice->encodedInvoice);
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

                //$zatcaStatus = $body['clearanceStatus'];
                $invoice->update([
                    'zatca_invoice_status' => $body['clearanceStatus'] == 'NOT_CLEARED' ? 'sent_with_error' : 'sent_without_error',
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
                    // dd(json_encode([
                    //     "errorMessages"=>[["message"=>"Unauthorized"]]
                    // ]));
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
            Log::error('zatca submit invoice',[$e]);
            $invoice->update([
                'zatca_invoice_status' => 'sent_with_error',
            ]);
            $taxInvoice = $taxInvoice->zactaInvoice->update([
                'response_log' => json_encode(["validationResults" => [
                    "errorMessages" => [["message" => $e->getMessage()]],
                    "warningMessages" => []
                ]]),
                'request_date' => Carbon::now()
            ]);
        }


        // dd($body);
        // return redirect()->route('invoices-acc');
        // return redirect()->route('invoices-acc')->with(['success' => 'تمت الاضافه']);

        // dd($response->json(),$response->status());
    }

    public function getInvoicePDFPath()
    {
        $pdf = Pdf::loadView('Invoices.pdf.index', ['invoice' => $this]);
        $path = storage_path($this->invoice_no . ".pdf");
        $pdf->save($path);
        return $path;
    }

    public function SendWhatApp()
    {
        $provider = new HillProvider();
        $pdf = Pdf::loadView('Invoices.pdf.index_new', ['invoice' => $this], [], []);
        $filename = $this->invoice_no . '_' . strtotime(now()) . Str::random(4) . ".pdf";
        $mobile = '966' . ltrim($this->waybill->waybill_sender_mobile, '0');
        if (empty($mobile)) {
            return;
        }
        Storage::disk(config('filesystems.disks.google.driver'))->put($filename, $pdf->output());
        $company_name = $this->company->company_name_ar;
        $message =
            'نشكر لكم زيارتكم ويمكنكم الاطلاع علي فاتورتك';
        $publicUrl = Storage::disk(config('filesystems.disks.google.driver') ?? 'public')->url($filename);
        $provider->sendFileTo($mobile, $filename, $publicUrl, $message);
    }


    public function getInvoiceDueAmountToWordAttribute()
    {
        $amount = round($this->invoice_amount, 2);
        $riyals = intval($amount);
        $halalas = intval(round(($amount - $riyals) * 100));

        $replacements = [
            'تسع مئة' => 'تسعمائة',
            'ثمان مئة' => 'ثمانمائة',
            'سبع مئة' => 'سبعمائة',
            'ست مئة' => 'ستمائة',
            'خمس مئة' => 'خمسمائة',
            'أربع مئة' => 'أربعمائة',
            'ثلاث مئة' => 'ثلاثمائة',
            'مئتان' => 'مائتان',
            'مئة' => 'مائة',
        ];

        $riyalsInWords = NumberToWords::transformNumber('ar', $riyals);
        $riyalsInWords = str_replace(array_keys($replacements), array_values($replacements), $riyalsInWords);

        if ($halalas > 0) {
            $halalasInWords = NumberToWords::transformNumber('ar', $halalas);
            $halalasInWords = str_replace(array_keys($replacements), array_values($replacements), $halalasInWords);
            return $riyalsInWords . ' ريال و ' . $halalasInWords . ' هللة فقط لاغير';
        }

        return $riyalsInWords . ' ريال فقط لاغير';
    }
}
