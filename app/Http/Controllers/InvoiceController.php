<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicNumberConverter;
use App\Helpers\QrHelper;
use App\Http\Requests\ClaimRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\Claim;
use App\Models\Container;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceStatement;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Policy;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function invoices(Request $request) {
        $invoices = Invoice::query();
        $invoiceType = request()->query('invoice_type', 'ضريبية');
        $paymentFilter = request()->query('status');
        $search = $request->input('search', null);

        if($invoiceType && $invoiceType == 'ضريبية') {
            $invoices->where('status', '!=', 'مسودة');
        } elseif ($invoiceType && $invoiceType == 'مسودة') {
            $invoices->where('status', 'مسودة');
        }
        if ($paymentFilter && $paymentFilter !== 'all') {
            $invoices->where('status', $paymentFilter);
        }
        if($search) {
            $invoices->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })->orWhere('date', 'like', "%$search%");
            });
        }

        $invoices = $invoices->with(['customer'])->orderBy('code', 'desc')->paginate(100)->onEachSide(1)->withQueryString();

        return view('pages.invoices.invoices', compact('invoices'));
    }

    // ----------------------- Storage Invoices -----------------------

    public function createInvoice(Request $request) {
        $customers = Customer::all(); 
        $customer_id = $request->input('customer_id');
        $containers = Container::where('status', 'تم التسليم')->where('customer_id', $customer_id)->get();
        $containers = $containers->filter(function($container) {
            return $container->invoices->filter(function($invoice) {
                return $invoice->type == 'تخزين' || $invoice->type == 'تخليص';
            })->isEmpty();
        });

        // add the containers that are linked to the customer by policies but not linked by customer_id on the container itself, 
        // this is for the case when the container is linked to the customer by a storage policy but the customer_id on the container is not set for some reason
        $policies = Policy::where('customer_id', $customer_id)->where('type', 'تخزين')->get();
        foreach($policies as $policy) {
            if($policy->containers()->where('status', 'تم التسليم')->exists() && !$policy->containers()->whereHas('invoices', function($q) {
                $q->where('type', 'تخزين');
            })->exists()) {
                $containers = $containers->merge($policy->containers()->where('status', 'تم التسليم')->get());
            }
        }

        foreach($containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
            $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;
            if($storage_duration && $container->period > $storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $storage_duration)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $container->late_days = $days;
                $container->late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
            } else {
                $container->late_days = 'لا يوجد';
                $container->late_fee = 0;
            }
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $container->total = $container->storage_price + $container->late_fee + $services;
        }

        return view('pages.invoices.create_invoice', compact('customers', 'containers'));
    }
    
    public function storeInvoice(InvoiceRequest $request) {
        // return $request;
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $containerIds = $request->input('container_ids', []);
        $containers = Container::with(['transactions', 'policies', 'services'])->whereIn('id', $containerIds)->get();

        // foreach($containers as $container) {
        //     if($container->transactions->first()) {
        //         return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة تخزين على حاوية مرتبطة بمعاملة تخليص');
        //     }
        // }

        $amountBeforeTax = 0;
        $containerData = []; 

        foreach($containers as $container) {
            $policy = $container->policies->where('type', 'تخزين')->first();
            if (!$policy) continue;

            $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $storage_price = $policy->storage_price;
            
            $late_fee = 0;
            if($period > $policy->storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $policy->storage_duration)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $late_fee = $days * $policy->late_fee;
            }

            $servicesSum = $container->services->sum('pivot.price');
            $totalForThisContainer = $storage_price + $late_fee + $servicesSum;

            $amountBeforeTax += $totalForThisContainer;
            $containerData[$container->id] = ['amount' => $totalForThisContainer];
        }

        if($amountBeforeTax <= 0 && $request->invoice_type != 'مسودة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة بقيمة صفرية، يرجى التأكد من أسعار التخزين والخدمات.');
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate_percent = $request->input('tax_rate', 15);
        $tax = $amountAfterDiscount * ($tax_rate_percent / 100);
        $totalAmount = $amountAfterDiscount + $tax;

        return DB::transaction(function () use ($request, $amountBeforeTax, $amountAfterDiscount, $tax, $totalAmount, $tax_rate_percent, $containerData) {
            $validated = $request->validated();
            if($request->invoice_type == 'مسودة') {
                $validated['status'] = 'مسودة';
            } else {
                $validated['status'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
            }

            $invoice = Invoice::create(array_merge($validated, [
                'amount_before_tax' => $amountBeforeTax,
                'tax_rate' => $tax_rate_percent,
                'tax' => $tax,
                'amount_after_discount' => $amountAfterDiscount,
                'total_amount' => $totalAmount,
            ]));

            $invoice->containers()->attach($containerData);

            logActivity('إنشاء فاتورة تخزين', "تم إنشاء فاتورة تخزين رقم " . $invoice->code . " للعميل " . $invoice->customer->name, null, $invoice->toArray());

            return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.unified.details', $invoice).'">عرض الفاتورة</a>');
        });
    }

    public function invoiceDetails(Invoice $invoice) {
        $amountBeforeTax = 0;
        $services = 0;

        foreach($invoice->containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
            $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;
            if($storage_duration && $container->period > $storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $storage_duration)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $container->late_days = $days;
                $container->late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
            } else {
                $container->late_days = 'لا يوجد';
                $container->late_fee = 0;
            }
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $container->total_services = $services;
            $container->total = $container->storage_price + $container->late_fee + $services;
            $amountBeforeTax += $container->total;  
        }

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('pages.invoices.invoice_details', compact(
            'invoice', 
            'hatching_total', 
            'qrCode',
        ));
    }

    // ----------------------- Service Invoices -----------------------

    public function storeServiceInvoice(InvoiceRequest $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $containerIds = $request->input('container_ids', []);
        $containers = Container::with(['services'])->whereIn('id', $containerIds)->get();

        $amountBeforeTax = 0;
        $containerData = [];

        foreach($containers as $container) {
            $services = $container->services->sum('pivot.price');
            $amountBeforeTax += $services;
            $containerData[$container->id] = ['amount' => $services];
        }

        if($amountBeforeTax <= 0 && $request->invoice_type != 'مسودة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة بقيمة صفرية، يرجى التأكد من أسعار الخدمات.');
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate_percent = $request->input('tax_rate', 15);
        $tax = $amountAfterDiscount * ($tax_rate_percent / 100);
        $totalAmount = $amountAfterDiscount + $tax;

        return DB::transaction(function () use ($request, $amountBeforeTax, $amountAfterDiscount, $tax, $totalAmount, $tax_rate_percent, $containerData) {
            $validated = $request->validated();
            if($request->invoice_type == 'مسودة') {
                $validated['status'] = 'مسودة';
            } else {
                $validated['status'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
            }
            
            $invoice = Invoice::create(array_merge($validated, [
                'amount_before_tax' => $amountBeforeTax,
                'tax_rate' => $tax_rate_percent,
                'tax' => $tax,
                'amount_after_discount' => $amountAfterDiscount,
                'total_amount' => $totalAmount,
            ]));

            $invoice->containers()->attach($containerData);

            logActivity('إنشاء فاتورة خدمات', "تم إنشاء فاتورة خدمات رقم " . $invoice->code . " للعميل " . $invoice->customer->name, null, $invoice->toArray());

            return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.unified.details', $invoice).'">عرض الفاتورة</a>');
        });
    }

    public function invoiceServicesDetails(Invoice $invoice) {
        
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        $invoice->load('payments.voucher');

        return view('pages.invoices.invoice_services_details', compact(
            'invoice', 
            'hatching_total', 
            'qrCode',
        ));
    }

    // ----------------------- Clearance Invoices -----------------------

    public function previewClearanceInvoice(Transaction $transaction) {
        $previewItems = collect();
        $existingItems = $transaction->items;

        // Add existing items to preview
        foreach($existingItems as $item) {
            $previewItems->push([
                'description' => $item->description,
                'amount' => $item->amount,
                'tax' => $item->tax,
                'total' => $item->total,
                'source' => 'existing',
            ]);
        }

        // Calculate contract-based items
        if($transaction->customer->contract) {
            $containers_count = $transaction->containers->count();
            $contractServices = collect($transaction->customer->contract->services->pluck('description')->toArray());

            // Clearance fees
            if($contractServices->contains('اجور تخليص') && !$existingItems->contains('description', 'اجور تخليص - CLEARANCE FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور تخليص')->first()->pivot->price * $containers_count;
                $previewItems->push([
                    'description' => 'اجور تخليص - CLEARANCE FEES',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                    'source' => 'contract',
                ]);
            }

            // Transport fees
            if($contractServices->contains(function($service) {
                return str_starts_with($service, 'اجور نقل');
            }) && !$existingItems->contains('description', 'اجور نقل - TRANSPORT FEES')) {
                $price = 0;

                foreach($transaction->containers as $container) {
                    $transportOrder = $container->transportOrders()->where('transaction_id', $transaction->id)->first();
                    $from = $transportOrder ? $transportOrder->from : null;
                    $to = $transportOrder ? $transportOrder->to : null;
                    
                    if ($container->containerType->name == 'حاوية 20' || $container->containerType->name == 'حاوية 40') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية فئة 20/40 من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'وزن زائد') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية وزن زائد من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'حاوية مبرده') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية مبردة من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'طرود LCL') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل طرود LCL من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    }
                }

                if($price > 0) {
                    $previewItems->push([
                        'description' => 'اجور نقل - TRANSPORT FEES',
                        'amount' => $price,
                        'tax' => $price * 0.15,
                        'total' => $price * 1.15,
                        'source' => 'contract',
                    ]);
                }
            }

            // Labour fees
            if($contractServices->contains('اجور عمال') && !$existingItems->contains('description', 'اجور عمال - LABOUR')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور عمال')->first()->pivot->price * $containers_count;
                $previewItems->push([
                    'description' => 'اجور عمال - LABOUR',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                    'source' => 'contract',
                ]);
            }

            // Saber fees
            if($contractServices->contains('خدمات سابر') && !$existingItems->contains('description', 'خدمات سابر - SABER FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'خدمات سابر')->first()->pivot->price * $containers_count;
                $previewItems->push([
                    'description' => 'خدمات سابر - SABER FEES',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                    'source' => 'contract',
                ]);
            }
        }

        // Calculate storage fees
        $storage_price_before_tax = 0;
        foreach($transaction->containers as $container) {
            if($container->invoices->isEmpty() 
                && $container->policies->where('type', 'تخزين')->isNotEmpty() 
                && $container->policies->where('type', 'تخزين')->first()->customer_id == $transaction->customer_id  // make sure the policy is linked to the customer, this is for the case when there are multiple policies on the container and one of them is linked to the customer but not the one that is currently being checked in the loop
                && $container->status == 'تم التسليم') {
                $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
                $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;

                if($storage_duration && $period > $storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $storage_duration)
                        ->diffInDays(Carbon::parse($container->exit_date));
                    $late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
                } else {
                    $late_fee = 0;
                }
                $services = 0;
                foreach($container->services as $service) {
                    $services += $service->pivot->price;
                }
                $storage_price_before_tax += $storage_price + $late_fee + $services;
            }
        }

        if($storage_price_before_tax > 0 && !$existingItems->contains('description', 'رسوم تخزين - STORAGE FEES')) {
            $previewItems->push([
                'description' => 'رسوم تخزين - STORAGE FEES',
                'amount' => $storage_price_before_tax,
                'tax' => $storage_price_before_tax * 0.15,
                'total' => $storage_price_before_tax * 1.15,
                'source' => 'storage',
            ]);
        }

        // Group items by description
        $grouped = $previewItems
            ->groupBy('description')
            ->map(function ($group) {
                return [
                    'description' => $group->first()['description'],
                    'amount' => $group->sum('amount'),
                    'tax' => $group->sum('tax'),
                    'total' => $group->sum('total'),
                    'source' => $group->first()['source'],
                ];
            })
            ->values();

        $totalAmount = $grouped->sum('amount');
        $totalTax = $grouped->sum('tax');
        $totalWithTax = $grouped->sum('total');

        return response()->json([
            'items' => $grouped,
            'totals' => [
                'amount' => $totalAmount,
                'tax' => $totalTax,
                'total' => $totalWithTax,
            ]
        ]);
    }

    public function storeClearanceInvoice(InvoiceRequest $request, Transaction $transaction) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }
        if($transaction->status == 'معلقة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة تخليص على معاملة معلقة');
        }

        $newItems = [];
        $containers_count = $transaction->containers->count();

        if($transaction->customer->contract) {
            $contractServices = collect($transaction->customer->contract->services->pluck('description')->toArray());

            if($contractServices->contains('اجور تخليص') && !$transaction->items->contains('description', 'اجور تخليص - CLEARANCE FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور تخليص')->first()->pivot->price * $containers_count;
                $newItems[] = [
                    'description' => 'اجور تخليص - CLEARANCE FEES',
                    'type' => 'ايراد تخليص',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ];
            }

            if($contractServices->contains(function($service) {
                return str_starts_with($service, 'اجور نقل');
            }) && !$transaction->items->contains('description', 'اجور نقل - TRANSPORT FEES')) {
                $price = 0;

                foreach($transaction->containers as $container) {
                    $transportOrder = $container->transportOrders()->where('transaction_id', $transaction->id)->first();
                    $from = $transportOrder ? $transportOrder->from : null;
                    $to = $transportOrder ? $transportOrder->to : null;
                    
                    if ($container->containerType->name == 'حاوية 20' || $container->containerType->name == 'حاوية 40') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية فئة 20/40 من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'وزن زائد') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية وزن زائد من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'حاوية مبرده') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل حاوية مبردة من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    } elseif ($container->containerType->name == 'طرود LCL') {
                        $transportService = $transaction->customer->contract->services->where("description", "اجور نقل طرود LCL من $from الى $to")->first();
                        if($transportService) {
                            $price += $transportService->pivot->price;
                        }
                    }
                }

                if($price > 0) {
                    $newItems[] = [
                        'description' => 'اجور نقل - TRANSPORT FEES',
                        'type' => 'ايراد نقل',
                        'amount' => $price,
                        'tax' => $price * 0.15,
                        'total' => $price * 1.15,
                    ];
                }
            }

            if($contractServices->contains('اجور عمال') && !$transaction->items->contains('description', 'اجور عمال - LABOUR')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور عمال')->first()->pivot->price * $containers_count;
                $newItems[] = [
                    'description' => 'اجور عمال - LABOUR',
                    'type' => 'ايراد عمال',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ];
            }

            if($contractServices->contains('خدمات سابر') && !$transaction->items->contains('description', 'خدمات سابر - SABER FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'خدمات سابر')->first()->pivot->price * $containers_count;
                $newItems[] = [
                    'description' => 'خدمات سابر - SABER FEES',
                    'type' => 'ايراد سابر',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ];
            }
        }

        // Calculate storage fees
        $storage_price_before_tax = 0;
        $containerData = [];

        foreach($transaction->containers as $container) {
            $containerStorageAmount = 0;
            if($container->invoices->isEmpty() 
                && $container->policies->where('type', 'تخزين')->isNotEmpty() 
                && $container->policies->where('type', 'تخزين')->first()->customer_id == $transaction->customer_id // make sure the policy is linked to the customer, this is for the case when there are multiple policies on the container and one of them is linked to the customer but not the one that is currently being checked in the loop
                && $container->status == 'تم التسليم') {
                $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
                $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;
                if($storage_duration && $period > $storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $storage_duration)
                        ->diffInDays(Carbon::parse($container->exit_date));
                    $late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
                } else {
                    $late_fee = 0;
                }
                $services = $container->services->sum('pivot.price');
                $containerStorageAmount = $storage_price + $late_fee + $services;
                $storage_price_before_tax += $containerStorageAmount;
            }
            $containerData[$container->id] = ['amount' => $containerStorageAmount];
        }

        if($storage_price_before_tax > 0 && !$transaction->items->contains('description', 'رسوم تخزين - STORAGE FEES')) {
            $newItems[] = [
                'description' => 'رسوم تخزين - STORAGE FEES',
                'type' => 'ايراد تخزين',
                'amount' => $storage_price_before_tax,
                'tax' => $storage_price_before_tax * 0.15,
                'total' => $storage_price_before_tax * 1.15,
            ];
        }

        // Combine existing items with new items for calculation
        $allItems = collect($transaction->items->toArray())->merge($newItems);

        $amountBeforeTax = $allItems->sum('amount');
        $tax = $allItems->sum('tax');
        $totalAmount = $allItems->sum('total');

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;

        return DB::transaction(function () use ($request, $transaction, $newItems, $containerData, $amountBeforeTax, $amountAfterDiscount, $tax, $totalAmount) {
            $itemNumber = $transaction->items()->count();
            foreach($newItems as $item) {
                $itemNumber++;
                $transaction->items()->create(array_merge($item, ['number' => $itemNumber]));
            }

            $validated = $request->validated();
            if($request->invoice_type == 'مسودة') {
                $validated['status'] = 'مسودة';
            } else {
                $validated['status'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
            }

            $invoice = Invoice::create(array_merge($validated, [
                'amount_before_tax' => $amountBeforeTax,
                'tax' => $tax,
                'amount_after_discount' => $amountAfterDiscount,
                'total_amount' => $totalAmount,
            ]));

            $invoice->containers()->attach($containerData);

            $transaction->refresh();

            $grouped = $transaction->items
                ->groupBy('description')
                ->map(function ($group) {
                    return [
                        'number' => $group->min('number'),
                        'description' => $group->first()->description,
                        'amount' => $group->sum('amount'),
                        'tax' => $group->sum('tax'),
                        'total' => $group->sum('total'),
                    ];
                })
                ->sortBy('number')
                ->values();         

            foreach ($grouped as $item) {
                $invoice->clearanceInvoiceItems()->create([
                    'number' => $item['number'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'tax' => $item['tax'],
                    'total' => $item['total'],
                ]);
            }

            logActivity('إنشاء فاتورة تخليص', "تم إنشاء فاتورة تخليص رقم " . $invoice->code . " للعميل " . $invoice->customer->name, null, $invoice->toArray());

            return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.unified.details', $invoice).'">عرض الفاتورة</a>');
        });
    }

    public function clearanceInvoiceDetails(Invoice $invoice) {
        $transaction = Transaction::where('customer_id', $invoice->customer_id)
            ->whereHas('containers', function ($query) use ($invoice) {
                $containerIds = $invoice->containers->pluck('id')->toArray();
                $query->whereIn('container_id', $containerIds);
            })
            ->first();

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('pages.invoices.clearance_invoice_details', compact(
            'invoice', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function updateClearanceInvoiceItems(Request $request, Invoice $invoice) {
        if(Gate::denies('تعديل فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل الفواتير');
        }

        $itemsData = $request->input('items', []);
        $existingItemIds = collect($itemsData)->pluck('id')->filter()->toArray();
        $totalAmountBeforeTax = 0;
        $totalTax = 0;

        $invoice->clearanceInvoiceItems()->whereNotIn('id', $existingItemIds)->delete();

        foreach($itemsData as $itemData) {
            if (!empty($itemData['id'])) {
                $item = $invoice->clearanceInvoiceItems()->find($itemData['id']);
                if ($item) {
                    $item->update([
                        'number' => $itemData['number'],
                        'description' => $itemData['description'],
                        'amount' => $itemData['amount'],
                        'tax' => $itemData['tax'],
                        'total' => $itemData['total'],
                    ]);
                }
            } else {
                // Create new item
                $invoice->clearanceInvoiceItems()->create([
                    'number' => $itemData['number'],
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'tax' => $itemData['tax'],
                    'total' => $itemData['total'],
                ]);
            }
            $totalAmountBeforeTax += $itemData['amount'];
            $totalTax += $itemData['tax'];
        }

        $discountValue = ($invoice->discount ?? 0) / 100 * $totalAmountBeforeTax;
        $amountAfterDiscount = $totalAmountBeforeTax - $discountValue;
        $totalAmount = $amountAfterDiscount + $totalTax;

        $invoice->update([
            'amount_before_tax' => $totalAmountBeforeTax,
            'tax' => $totalTax,
            'amount_after_discount' => $amountAfterDiscount,
            'total_amount' => $totalAmount,
        ]);

        logActivity('تعديل بنود فاتورة تخليص', "تم تعديل بنود فاتورة تخليص رقم " . $invoice->code, null, $invoice->toArray());

        return redirect()->back()->with('success', 'تم تحديث بنود الفاتورة بنجاح.');
    }

    // ----------------------- Shipping Invoices -----------------------
    
    public function createShippingInvoice(Request $request) {
        $customers = Customer::all(); 
        $customer_id = $request->input('customer_id');
        $shippingPolicies = ShippingPolicy::where('is_received', true)->where('customer_id', $customer_id)->get();
        $shippingPolicies = $shippingPolicies->filter(function($policy) {
            return $policy->invoices->filter(function($invoice) {
                return $invoice->type == 'شحن';
            })->isEmpty();
        });

        return view('pages.invoices.create_shipping_invoice', compact('customers', 'shippingPolicies'));
    } 

    public function storeShippingInvoice(InvoiceRequest $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $policyIds = $request->input('shipping_policy_ids', []);
        $shippingPolicies = ShippingPolicy::whereIn('id', $policyIds)->get();

        $amountBeforeTax = 0;
        $shippingPolicyData = [];

        foreach($shippingPolicies as $policy) {
            $amountBeforeTax += $policy->total_cost;
            $policyData[$policy->id] = ['amount' => $policy->total_cost];
        }

        if($amountBeforeTax <= 0 && $request->invoice_type != 'مسودة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة بقيمة صفرية، يرجى التأكد من تكاليف الشحن.');
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate_percent = $request->input('tax_rate', 15);
        $tax = $amountAfterDiscount * ($tax_rate_percent / 100);
        $totalAmount = $amountAfterDiscount + $tax;

        return DB::transaction(function () use ($request, $amountBeforeTax, $amountAfterDiscount, $tax, $totalAmount, $tax_rate_percent, $policyData) {
            $validated = $request->validated();
            if($request->invoice_type == 'مسودة') {
                $validated['status'] = 'مسودة';
            } else {
                $validated['status'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
            }

            $invoice = Invoice::create(array_merge($validated, [
                'amount_before_tax' => $amountBeforeTax,
                'tax_rate' => $tax_rate_percent,
                'tax' => $tax,
                'amount_after_discount' => $amountAfterDiscount,
                'total_amount' => $totalAmount,
            ]));

            $invoice->shippingPolicies()->attach($policyData);

            logActivity('إنشاء فاتورة شحن', "تم إنشاء فاتورة شحن رقم " . $invoice->code . " للعميل " . $invoice->customer->name, null, $invoice->toArray());

            return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.unified.details', $invoice).'">عرض الفاتورة</a>');
        });  
    }

    public function shippingInvoiceDetails(Invoice $invoice) {
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('pages.invoices.shipping_invoice_details', compact(
            'invoice', 
            'hatching_total', 
            'qrCode',
        ));
    }

    // ----------------------- Unified Invoice Creation -----------------------

    public function createUnifiedInvoice(Request $request) {
        $customers = Customer::all(); 
        $invoiceType = $request->input('type');
        $customer_id = $request->input('customer_id');

        // Initialize arrays
        $containers = collect();
        $shippingPolicies = collect();
        $servicePolicies = collect();
        $transactions = collect();

        if ($customer_id) {
            // Get containers for storage invoices
            if ($invoiceType === 'تخزين' || $invoiceType === 'تخزين و شحن') {
                $containers = Container::where('status', 'تم التسليم')->where('customer_id', $customer_id)->get();
                $containers = $containers->filter(function($container) {
                    return $container->policies->where('type', 'تخزين')->first() && $container->invoices->filter(function($invoice) {
                        return $invoice->type == 'تخزين' || $invoice->type == 'تخليص' || $invoice->type == 'تخزين و شحن';
                    })->isEmpty();
                });

                // Add containers linked by policies
                $policies = Policy::where('customer_id', $customer_id)->where('type', 'تخزين')->get();
                foreach($policies as $policy) {
                    if($policy->containers()->where('status', 'تم التسليم')->exists() && !$policy->containers()->whereHas('invoices', function($q) {
                        $q->where('type', 'تخزين')->orWhere('type', 'تخزين و شحن');
                    })->exists()) {
                        $containers = $containers->merge($policy->containers()->where('status', 'تم التسليم')->get());
                    }
                }

                // Prepare container details
                foreach($containers as $container) {
                    $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                    $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price ?? 0.00;
                    $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration ?? 0.00;
                    if($storage_duration && $container->period > $storage_duration) {
                        $days = (int) Carbon::parse($container->date)
                            ->addDays((int) $storage_duration)
                            ->diffInDays(Carbon::parse($container->exit_date));
                        $container->late_days = $days;
                        $container->late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee ?? 0.00;
                    } else {
                        $container->late_days = 'لا يوجد';
                        $container->late_fee = 0;
                    }
                    $services = 0;
                    foreach($container->services as $service) {
                        $services += $service->pivot->price;
                    }
                    $container->total = $container->storage_price + $container->late_fee + $services;
                }
            }

            // Get shipping policies for shipping invoices
            if ($invoiceType === 'شحن' || $invoiceType === 'تخزين و شحن') {
                $shippingPolicies = ShippingPolicy::where('is_received', true)->where('customer_id', $customer_id)->get();
                $shippingPolicies = $shippingPolicies->filter(function($policy) {
                    return $policy->invoices->filter(function($invoice) {
                        return $invoice->type == 'شحن' || $invoice->type == 'تخزين و شحن';
                    })->isEmpty();
                });
            }

            // Get service policies details for service invoices
            if ($invoiceType == 'خدمات') {
                $servicePolicies = Policy::where('customer_id', $customer_id)->where('type', 'خدمات')->with('containers.services')->get();
                $servicePolicies = $servicePolicies->filter(function($policy) {
                    return $policy->containers->filter(function($container) {
                        return $container->services->isNotEmpty() && $container->invoices->filter(function($invoice) {
                            return $invoice->type == 'خدمات';
                        })->isEmpty();
                    })->isNotEmpty();
                });
            }

            // Get clearance transactions data for clearance invoices
            if ($invoiceType == 'تخليص') {
                $transactions = Transaction::where('customer_id', $customer_id)->where('status', 'مغلقة')->with(['containers', 'items'])->get();
                $transactions = $transactions->filter(function($transaction) {
                    return $transaction->containers->filter(function($container) {
                        return $container->invoices->filter(function($invoice) {
                            return $invoice->type == 'تخليص';
                        })->isEmpty();
                    })->isNotEmpty();
                });
            }
        }

        return view('pages.invoices.create_unified_invoice', compact(
            'customers', 
            'containers', 
            'shippingPolicies', 
            'invoiceType', 
            'servicePolicies',
            'transactions'
        ));
    }

    public function storeUnifiedInvoice(InvoiceRequest $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $invoiceType = $request->input('type');
        $amountBeforeTax = 0;
        $containerData = [];
        $shippingPolicyData = [];

        // Handle storage invoices
        if ($invoiceType === 'تخزين' || $invoiceType === 'تخزين و شحن') {
            $containerIds = $request->input('container_ids', []);
            $containers = Container::with(['transactions', 'policies', 'services'])->whereIn('id', $containerIds)->get();

            foreach($containers as $container) {
                $policy = $container->policies->where('type', 'تخزين')->first();
                if (!$policy) continue;

                $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $storage_price = $policy->storage_price;
                
                $late_fee = 0;
                if($period > $policy->storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $policy->storage_duration)
                        ->diffInDays(Carbon::parse($container->exit_date));
                    $late_fee = $days * $policy->late_fee;
                }

                $servicesSum = $container->services->sum('pivot.price');
                $totalForThisContainer = $storage_price + $late_fee + $servicesSum;

                $amountBeforeTax += $totalForThisContainer;
                $containerData[$container->id] = ['amount' => $totalForThisContainer];
            }
        }

        // Handle shipping invoices
        if ($invoiceType === 'شحن' || $invoiceType === 'تخزين و شحن') {
            $policyIds = $request->input('shipping_policy_ids', []);
            $shippingPolicies = ShippingPolicy::whereIn('id', $policyIds)->get();

            foreach($shippingPolicies as $policy) {
                $amountBeforeTax += $policy->total_cost;
                $shippingPolicyData[$policy->id] = ['amount' => $policy->total_cost];
            }
        }

        // Handle service invoices
        if($invoiceType == 'خدمات') {
            $containerIds = $request->input('container_ids', []);
            $containers = Container::with(['services'])->whereIn('id', $containerIds)->get();

            foreach($containers as $container) {
                $services = $container->services->sum('pivot.price');
                $amountBeforeTax += $services;
                $containerData[$container->id] = ['amount' => $services];
            }
        }

        if($amountBeforeTax <= 0 && $request->invoice_type != 'مسودة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة بقيمة صفرية، يرجى التأكد من أسعار التخزين والشحن والخدمات.');
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate_percent = $request->input('tax_rate', 15);
        $tax = $amountAfterDiscount * ($tax_rate_percent / 100);
        $totalAmount = $amountAfterDiscount + $tax;

        return DB::transaction(function () use ($request, $amountBeforeTax, $amountAfterDiscount, $tax, $totalAmount, $tax_rate_percent, $containerData, $shippingPolicyData, $invoiceType) {
            $validated = $request->validated();
            if($request->invoice_type == 'مسودة') {
                $validated['status'] = 'مسودة';
            } else {
                $validated['status'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
            }

            $invoice = Invoice::create(array_merge($validated, [
                'type' => $invoiceType,
                'amount_before_tax' => $amountBeforeTax,
                'tax_rate' => $tax_rate_percent,
                'tax' => $tax,
                'amount_after_discount' => $amountAfterDiscount,
                'total_amount' => $totalAmount,
            ]));

            // Attach containers if applicable
            if (!empty($containerData)) {
                $invoice->containers()->attach($containerData);
            }

            // Attach shipping policies if applicable
            if (!empty($shippingPolicyData)) {
                $invoice->shippingPolicies()->attach($shippingPolicyData);
            }

            $typeLabel = $invoiceType === 'تخزين و شحن' ? 'فاتورة مدمجة (تخزين و شحن)' : ($invoiceType === 'تخزين' ? 'فاتورة تخزين' : 'فاتورة شحن');
            logActivity('إنشاء فاتورة', "تم إنشاء $typeLabel رقم " . $invoice->code . " للعميل " . $invoice->customer->name, null, $invoice->toArray());

            // Determine appropriate route based on invoice type
            $routeName = ($invoiceType === 'تخزين') ? 'invoices.unified.details' : (($invoiceType === 'شحن') ? 'invoices.unified.details' : 'invoices.unified.details');

            return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route($routeName, $invoice).'">عرض الفاتورة</a>');
        });  
    }

    public function unifiedInvoiceDetails(Invoice $invoice) {
        if($invoice->type == 'تخزين' || $invoice->type == 'تخزين و شحن') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services');

            $storage_amount = 0;
            $services = 0;

            foreach($invoice->containers as $container) {
                $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
                $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;
                if($storage_duration && $container->period > $storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $storage_duration)
                        ->diffInDays(Carbon::parse($container->exit_date));
                    $container->late_days = $days;
                    $container->late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
                } else {
                    $container->late_days = 'لا يوجد';
                    $container->late_fee = 0;
                }
                $services = 0;
                foreach($container->services as $service) {
                    $services += $service->pivot->price;
                }
                $container->total_services = $services;
                $container->total = $container->storage_price + $container->late_fee + $services;
                $storage_amount += $container->total;  
            }

            $invoice->storage_amount = $storage_amount;
        }

        if($invoice->type == 'شحن' || $invoice->type == 'تخزين و شحن') {
            $invoice->load('customer', 'shippingPolicies');
            $shipping_amount = $invoice->shippingPolicies->sum('total_cost');
            $invoice->shipping_amount = $shipping_amount;
        } 
        
        if($invoice->type == 'خدمات') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services');
        }
        
        if($invoice->type == 'تخليص') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services', 'containers.transactions.items');
        }

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('pages.invoices.unified_invoice_details', compact(
            'invoice', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function printUnifiedInvoice(Invoice $invoice) {
        if($invoice->type == 'تخزين' || $invoice->type == 'تخزين و شحن') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services');

            $amountBeforeTax = 0;
            $services = 0;

            foreach($invoice->containers as $container) {
                $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
                $storage_duration = $container->policies->where('type', 'تخزين')->first()->storage_duration;
                if($storage_duration && $container->period > $storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $storage_duration)
                        ->diffInDays(Carbon::parse($container->exit_date));
                    $container->late_days = $days;
                    $container->late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
                } else {
                    $container->late_days = 'لا يوجد';
                    $container->late_fee = 0;
                }
                $services = 0;
                foreach($container->services as $service) {
                    $services += $service->pivot->price;
                }
                $container->total_services = $services;
                $container->total = $container->storage_price + $container->late_fee + $services;
                $amountBeforeTax += $container->total;  
            }
        } elseif($invoice->type == 'شحن' || $invoice->type == 'تخزين و شحن') {
            $invoice->load('customer', 'shippingPolicies');
        } elseif($invoice->type == 'خدمات') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services');
        } elseif($invoice->type == 'تخليص') {
            $invoice->load('customer', 'containers.customer', 'containers.policies', 'containers.services', 'containers.transactions.items');
        }

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        logActivity('طباعة فاتورة', "تم طباعة فاتورة رقم " . $invoice->code);

        return view('pages.invoices.print_unified_invoice', compact('invoice', 'hatching_total', 'qrCode'));
    }

    // ----------------------- Posting Invoices -----------------------

    public function postInvoice(Invoice $invoice, Request $request) {
        if($invoice->is_posted) {
            return redirect()->back()->with('error', 'هذه الفاتورة تم ترحيلها مسبقاً');
        }

        $creditAccount = $invoice->customer->account; // مدين
        if($invoice->type == 'تخزين') {
            $incomeAccount = Account::where('name', 'ايرادات التخزين')->where('level', 5)->first();
        } elseif($invoice->type == 'تخليص') {
            $incomeAccount = Account::where('name', 'ايرادات تخليص جمركي')->where('level', 5)->first();
        } elseif($invoice->type == 'شحن') {
            $incomeAccount = Account::where('name', 'ايرادات النقليات')->where('level', 5)->first();
        } elseif($invoice->type == 'تخزين و شحن') {
            $incomeAccount1 = Account::where('name', 'ايرادات التخزين')->where('level', 5)->first();
            $incomeAccount2 = Account::where('name', 'ايرادات النقليات')->where('level', 5)->first();
        } else {
            $incomeAccount = Account::where('name', 'ايرادات متنوعة')->where('level', 5)->first();
        }
        $taxAccount = Account::where('name', 'ضريبة القيمة المضافة من الايرادات')->where('level', 5)->first();

        $journal = JournalEntry::create([
            'date' => $invoice->date,
            'totalDebit' => $invoice->total_amount,
            'totalCredit' => $invoice->total_amount,
            'user_id' => Auth::user()->id,
            'invoice_id' => $invoice->id,
        ]);

        if($invoice->type == 'تخزين و شحن') {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $incomeAccount1->id,
                'debit' => 0.00,
                'credit' => $request->storage_amount ?? 0.00,
                'description' => 'ايرادات تخزين فاتورة رقم '. $invoice->code
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $incomeAccount2->id,
                'debit' => 0.00,
                'credit' => $request->shipping_amount ?? 0.00,
                'description' => 'ايرادات شحن فاتورة رقم ' . $invoice->code
            ]);
        } else {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $incomeAccount->id,
                'debit' => 0.00,
                'credit' => $invoice->amount_after_discount,
                'description' => 'ايرادات ' . ($invoice->type == 'خدمات' ? 'متنوعة' : $invoice->type) . ' فاتورة رقم ' . $invoice->code
            ]);
        }

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $taxAccount->id,
            'debit' => 0.00,
            'credit' => $invoice->tax,
            'description' => 'قيمة مضافة فاتورة ' . $invoice->type . ' رقم ' . $invoice->code
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $creditAccount->id,
            'debit' => $invoice->total_amount,
            'credit' => 0.00,
            'description' => 'استحقاق فاتورة ' . $invoice->type . ' رقم ' . $invoice->code
        ]);

        $invoice->is_posted = true;
        $invoice->save();

        $new = $journal->load('lines')->toArray();
        logActivity('ترحيل فاتورة', "تم ترحيل الفاتورة رقم " . $invoice->code . " إلى القيد رقم " . $journal->code, null, $new);

        return redirect()->back()->with('success', "تم ترحيل الفاتورة بنجاح <a class='text-white fw-bold' href='".route('journal.details', $journal)."'>عرض القيد</a>");
    }

    public function postClearanceInvoice(Invoice $invoice) {
        if($invoice->is_posted) {
            return redirect()->back()->with('error', 'هذه الفاتورة تم ترحيلها مسبقاً');
        }
        foreach($invoice->containers()->first()->transactions()->first()->items as $item) {
            if($item->type == 'مصروف' && !$item->is_posted) {
                return redirect()->back()->with('error', 'لا يمكن ترحيل فاتورة تخليص قبل ترحيل جميع بنود المعاملة المرتبطة بها');
            }
        }

        $creditAccount = $invoice->customer->account; // مدين
        
        $clearance_revenue_Account = Account::where('name', 'ايرادات تخليص جمركي')->where('level', 5)->first();
        $transport_revenue_Account = Account::where('name', 'ايرادات النقليات')->where('level', 5)->first();
        $labor_revenue_Account = Account::where('name', 'ايرادات اجور عمال')->where('level', 5)->first();
        $saber_revenue_Account = Account::where('name', 'ايرادات خدمات سابر')->where('level', 5)->first();
        $storage_revenue_Account = Account::where('name', 'ايرادات التخزين')->where('level', 5)->first();
        $taxAccount = Account::where('name', 'ضريبة القيمة المضافة من الايرادات')->where('level', 5)->first();

        $journal = JournalEntry::create([
            'date' => $invoice->date,
            'totalDebit' => $invoice->total_amount,
            'totalCredit' => $invoice->total_amount,
            'user_id' => Auth::user()->id,
            'invoice_id' => $invoice->id,
        ]);

        $clearance_revenue = 0;
        $transport_revenue = 0;
        $labor_revenue = 0;
        $saber_revenue = 0;
        $storage_revenue = 0;

        foreach($invoice->containers->first()->transactions->first()->items->sortBy('number') as $item) {
            if($item->type == 'مصروف') {
                $itemDescription = explode(' - ', $item->description)[0];

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $item->debitAccount->id,
                    'debit' => 0.00,
                    'credit' => $item->amount,
                    'description' => 'مصروف ' . $itemDescription . ' على معاملة ' . $item->transaction->code . ' فاتورة رقم ' . $invoice->code
                ]);
            } elseif($item->type == 'ايراد تخليص') {
                $clearance_revenue += $item->amount;
            } elseif($item->type == 'ايراد نقل') {
                $transport_revenue += $item->amount;
            } elseif($item->type == 'ايراد عمال') {
                $labor_revenue += $item->amount;
            } elseif($item->type == 'ايراد سابر') {
                $saber_revenue += $item->amount;
            } elseif($item->type == 'ايراد تخزين') {
                $storage_revenue += $item->amount;
            }
        }

        if($clearance_revenue > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $clearance_revenue_Account->id,
                'debit' => 0.00,
                'credit' => $clearance_revenue,
                'description' => 'ايرادات تخليص فاتورة رقم ' . $invoice->code
            ]);
        }
        if($transport_revenue > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $transport_revenue_Account->id,
                'debit' => 0.00,
                'credit' => $transport_revenue,
                'description' => 'ايرادات نقل فاتورة رقم ' . $invoice->code
            ]);
        }
        if($labor_revenue > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $labor_revenue_Account->id,
                'debit' => 0.00,
                'credit' => $labor_revenue,
                'description' => 'ايرادات اجور عمال فاتورة رقم ' . $invoice->code
            ]);
        }
        if($saber_revenue > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $saber_revenue_Account->id,
                'debit' => 0.00,
                'credit' => $saber_revenue,
                'description' => 'ايرادات خدمات سابر فاتورة رقم ' . $invoice->code
            ]);
        }
        if($storage_revenue > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $storage_revenue_Account->id,
                'debit' => 0.00,
                'credit' => $storage_revenue,
                'description' => 'ايرادات تخزين فاتورة رقم ' . $invoice->code
            ]);
        }

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $taxAccount->id,
            'debit' => 0.00,
            'credit' => $invoice->tax,
            'description' => 'قيمة مضافة فاتورة ' . $invoice->type . ' رقم ' . $invoice->code
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $creditAccount->id,
            'debit' => $invoice->total_amount,
            'credit' => 0.00,
            'description' => 'استحقاق فاتورة ' . $invoice->type . ' رقم ' . $invoice->code
        ]);

        $invoice->is_posted = true;
        $invoice->save();

        $new = $journal->load('lines')->toArray();
        logActivity('ترحيل فاتورة', "تم ترحيل الفاتورة رقم " . $invoice->code . " إلى القيد رقم " . $journal->code, null, $new);

        return redirect()->back()->with('success', "تم ترحيل الفاتورة بنجاح <a class='text-white fw-bold' href='".route('journal.details', $journal)."'>عرض القيد</a>");
    }

    // ----------------------- Invoice Statements -----------------------

    public function invoiceStatements(Request $request) {
        $invoiceStatements = InvoiceStatement::orderBy('id', 'desc')->get();

        $methodFilter = request()->query('paymentMethod');
        if ($methodFilter && $methodFilter !== 'all') {
            $invoiceStatements = $invoiceStatements->filter(function ($statement) use ($methodFilter) {
                return $statement->payment_method === $methodFilter;
            });
        }

        $search = $request->input('search', null);
        if($search) {
            $invoiceStatements = $invoiceStatements->filter(function($statement) use($search) {
                return stripos($statement->code, $search) !== false 
                    || stripos($statement->customer->name, $search) !== false
                    || stripos($statement->date, $search) !== false;
            });
        }

        return view('pages.invoices.statements', compact('invoiceStatements'));
    }

    public function createInvoiceStatement(Request $request) {
        $customers = Customer::all();
        $customer_id = $request->input('customer_id');
        $invoices = Invoice::where('customer_id', $customer_id)->where('status', 'لم يتم الدفع')->get();

        return view('pages.invoices.create_statement', compact('customers', 'invoices'));
    }

    public function storeInvoiceStatement(Request $request) {
        $invoices = [];
        foreach($request->input('invoice_ids', []) as $invoiceId) {
            $invoices[] = Invoice::findOrFail($invoiceId);
        }
        
        $subtotal = array_sum(array_map(fn($invoice) => $invoice->amount_after_discount, $invoices));
        $tax = array_sum(array_map(fn($invoice) => $invoice->tax, $invoices));
        $amount = array_sum(array_map(fn($invoice) => $invoice->total_amount, $invoices));

        $request->merge([
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'amount' => number_format($amount, 2, '.', ''),
        ]);
        
        $invoiceStatement = InvoiceStatement::create($request->all());

        $invoiceStatement->invoices()->attach(array_map(fn($invoice) => $invoice->id, $invoices));

        foreach($invoices as $invoice) {
            $invoice->status = 'تم الدفع';
            $invoice->save();
        }

        $new = $invoiceStatement->toArray();
        $statementInvoices = implode(', ', array_map(fn($invoice) => $invoice->code, $invoices));
        logActivity('إنشاء مطالبة فاتورة', "تم إنشاء مطالبة فاتورة رقم " . $invoiceStatement->code . "للعميل " . $invoiceStatement->customer->name . "للفواتير " . $statementInvoices, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء مطالبة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.statements.details', $invoiceStatement).'">عرض المطالبة</a>');
    }

    public function invoiceStatementDetails(InvoiceStatement $invoiceStatement) {
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoiceStatement->amount, 2));
        return view('pages.invoices.statement_details', compact('invoiceStatement', 'hatching_total'));
    }

    // ----------------------- Extra Methods -----------------------

    public function invoicesReports(Request $request) {
        $invoices = Invoice::query();
        $customers = Customer::all();
        $types = [];

        if(Auth::user()->company->hasModule('تخزين')) {
            $types[] = 'تخزين';
            $types[] = 'خدمات';
        }
        if(Auth::user()->company->hasModule('تخليص')) {
            $types[] = 'تخليص';
        }
        if(Auth::user()->company->hasModule('نقل')) {
            $types[] = 'شحن';
        }

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $payment_method = $request->input('payment_method', 'all');
        $is_posted = $request->input('is_posted', 'all');
        $status = $request->input('status', 'all');
        $perPage = $request->input('per_page', 100);
        $search = $request->input('search', null);

        if($customer !== 'all') {
            $invoices->where('customer_id', $customer);
        }
        if($from) {
            $invoices->where('date', '>=', $from);
        }
        if($to) {
            $to = Carbon::parse($to)->endOfDay();
            $invoices->where('date', '<=', $to);
        }
        if($type !== 'all') {
            $invoices->where('type', 'LIKE', "%$type%");
        }
        if($payment_method !== 'all') {
            $invoices->where('payment_method', $payment_method);
        }
        if($is_posted !== 'all') {
            $invoices->where('is_posted', $is_posted == 'true' ? true : false);
        }
        if($status !== 'all') {
            $invoices->where('status', $status);
        }
        if($search) {
            $invoices->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })->orWhere('date', 'like', "%$search%");
            });
        }

        $invoices = $invoices->with(['customer', 'made_by'])->orderBy('code')->paginate($perPage)->onEachSide(1)->withQueryString();

        return view('pages.invoices.reports', compact(
            'invoices', 
            'customers',
            'types',
            'perPage'
        ));
    }

    public function deleteInvoice(Invoice $invoice) {
        if($invoice->is_posted) {
            return redirect()->back()->with('error', 'لا يمكن حذف فاتورة تم ترحيلها');
        }

        $name = $invoice->code;

        $invoice->containers()->detach();
        $invoice->clearanceInvoiceItems()->delete();
        $invoice->shippingPolicies()->detach();
        $invoice->delete();

        logActivity('حذف فاتورة', "تم حذف الفاتورة رقم " . $name, null, $invoice->toArray());

        return redirect()->route('invoices')->with('success', 'تم حذف الفاتورة ' . $name . ' بنجاح');
    }

    public function updateInvoice(Request $request, Invoice $invoice) {
        if(Gate::denies('تعديل فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل الفواتير');
        }

        $old = $invoice->toArray();
        $invoice->update($request->all());
        $new = $invoice->toArray();

        logActivity('تعديل فاتورة', "تم تعديل بيانات الفاتورة رقم " . $invoice->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }

    public function approveInvoice(Invoice $invoice) {
        // if(Gate::denies('اعتماد فاتورة')) {
        //     return redirect()->back()->with('error', 'ليس لديك الصلاحية لاعتماد الفواتير');
        // }

        $currentYear = Carbon::now()->year;
        $lastInvoice = Invoice::whereYear('date', $currentYear)->orderBy('code', 'desc')->first();
        $prefix = $currentYear . 'IN';

        if ($lastInvoice && strpos($lastInvoice->code, $prefix) === 0) {
            $lastNumber = (int) substr($lastInvoice->code, strlen($prefix));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        $invoice->code = $prefix . $newNumber;
        $invoice->save();

        $invoice->status = 'لم يتم الدفع';
        $invoice->date = Carbon::now();
        $invoice->save();

        $new = $invoice->toArray();
        logActivity('اعتماد فاتورة', "تم اعتماد الفاتورة رقم " . $invoice->code, null, $new);

        return redirect()->back()->with('success', 'تم اعتماد الفاتورة بنجاح');
    }

    public function attachFile(Request $request, Invoice $invoice) {
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments/invoices/' . $invoice->id, $fileName, 'public');
            
            $invoice->attachments()->create([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'user_id' => Auth::user()->id,
            ]);

            logActivity('إرفاق ملف بالفاتورة', "تم إرفاق مستند " . $fileName . " إلى الفاتورة رقم: " . $invoice->code);

            return redirect()->back()->with('success', 'تم إرفاق الملف بنجاح');
        }

        return redirect()->back()->with('error', 'لم يتم إرفاق أي ملف' );
    }

    public function deleteAttachment(Attachment $attachment) {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();

        logActivity('حذف ملف من الفاتورة', "تم حذف الملف " . $attachment->original_name . " من الفاتورة رقم " . $attachment->attachable->code);

        return redirect()->back()->with('success', 'تم حذف الملف بنجاح');
    }
}
