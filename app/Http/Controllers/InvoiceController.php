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
use App\Models\InvoiceStatement;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function invoices(Request $request) {
        $invoices = Invoice::orderBy('code', 'desc')->get();

        $methodFilter = request()->query('paymentMethod');
        if ($methodFilter && $methodFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($methodFilter) {
                return $invoice->payment_method === $methodFilter;
            });
        }

        $paymentFilter = request()->query('isPaid');
        if ($paymentFilter && $paymentFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($paymentFilter) {
                return $invoice->isPaid === $paymentFilter;
            });
        }

        $search = $request->input('search', null);
        if($search) {
            $invoices = $invoices->filter(function($invoice) use($search) {
                return stripos($invoice->code, $search) !== false 
                    || stripos($invoice->customer->name, $search) !== false
                    || stripos($invoice->date, $search) !== false;
            });
        }

        $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $invoices->forPage(request()->get('page', 1), 50),
            $invoices->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.invoices.invoices', compact('invoices'));
    }

    public function createInvoice(Request $request) {
        $customers = Customer::all(); 
        $customer_id = $request->input('customer_id');
        $containers = Container::where('status', 'تم التسليم')->where('customer_id', $customer_id)->get();
        $containers = $containers->filter(function($container) {
            return $container->invoices->filter(function($invoice) {
                return $invoice->type == 'تخزين' || $invoice->type == 'تخليص';
            })->isEmpty();
        });

        foreach($containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
            if($container->period > $container->policies->where('type', 'تخزين')->first()->storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $container->policies->where('type', 'تخزين')->first()->storage_duration)
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
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $containerIds = $request->input('container_ids', []);
        $containers = Container::whereIn('id', $containerIds)->get();

        foreach($containers as $container) {
            if($container->transactions->first()) {
                return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة تخزين على حاوية مرتبطة بمعاملة تخليص');
            }
        }

        $validated = $request->validated();
        $validated['isPaid'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
        $invoice = Invoice::create($validated);

        
        $amountBeforeTax = 0;

        foreach($containers as $container) {
            $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
            if($period > $container->policies->where('type', 'تخزين')->first()->storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $container->policies->where('type', 'تخزين')->first()->storage_duration)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $late_fee = $days * $container->policies->where('type', 'تخزين')->first()->late_fee;
            } else {
                $late_fee = 0;
            }
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $amountBeforeTax += $storage_price + $late_fee + $services;
            $invoice->containers()->attach($container->id, ['amount' => $storage_price + $late_fee + $services]);
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate = $request->input('tax_rate', 15) / 100;
        $tax = $amountAfterDiscount * $tax_rate;
        $amount = $amountAfterDiscount + $tax;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax_rate = $request->input('tax_rate', 15);
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $amount;
        $invoice->save();

        logActivity('إنشاء فاتورة تخزين', "تم إنشاء فاتورة تخزين رقم " . $invoice->code . "للعميل " . $invoice->customer->name, null, $invoice->toArray());

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.details', $invoice).'">عرض الفاتورة</a>');
    } 
    
    public function invoiceDetails(Invoice $invoice) {
        $amountBeforeTax = 0;
        $services = 0;

        foreach($invoice->containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
            if($container->period > $container->policies->where('type', 'تخزين')->first()->storage_duration) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays((int) $container->policies->where('type', 'تخزين')->first()->storage_duration)
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

        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

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
            'services', 
            'discountValue', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function storeServiceInvoice(InvoiceRequest $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $validated = $request->validated();
        $validated['isPaid'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
        $invoice = Invoice::create($validated);

        $containerIds = $request->input('container_ids', []);
        $containers = Container::whereIn('id', $containerIds)->get();
        $amountBeforeTax = 0;

        foreach($containers as $container) {
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $amountBeforeTax += $services;
            $invoice->containers()->attach($container->id, ['amount' => $services]);
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate = $request->input('tax_rate', 15) / 100;
        $tax = $amountAfterDiscount * $tax_rate;
        $amount = $amountAfterDiscount + $tax;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax_rate = $request->input('tax_rate', 15);
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $amount;
        $invoice->save();

        logActivity('إنشاء فاتورة خدمات', "تم إنشاء فاتورة خدمات رقم " . $invoice->code . "للعميل " . $invoice->customer->name, null, $invoice->toArray());

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.services.details', $invoice).'">عرض الفاتورة</a>');
    }

    public function invoiceServicesDetails(Invoice $invoice) {
        $amountBeforeTax = 0;

        foreach($invoice->containers as $container) {
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $container->total = $services;
            $amountBeforeTax += $container->total;  
        }

        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('pages.invoices.invoice_services_details', compact(
            'invoice', 
            'discountValue', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function storeClearanceInvoice(InvoiceRequest $request, Transaction $transaction) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }
        if($transaction->status == 'معلقة') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء فاتورة تخليص على معاملة معلقة');
        }

        if($transaction->customer->contract) {
            $containers_count = $transaction->containers->count();

            $contractServices = collect($transaction->customer->contract->services->pluck('description')->toArray());

            if($contractServices->contains('اجور تخليص')  && !$transaction->items->contains('description', 'اجور تخليص - CLEARANCE FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور تخليص')->first()->pivot->price * $containers_count;
                $transaction->items()->create([
                    'number' => $transaction->items()->count() + 1,
                    'description' => 'اجور تخليص - CLEARANCE FEES',
                    'type' => 'ايراد تخليص',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ]);
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
                    $transaction->items()->create([
                        'number' => $transaction->items()->count() + 1,
                        'description' => 'اجور نقل - TRANSPORT FEES',
                        'type' => 'ايراد نقل',
                        'amount' => $price,
                        'tax' => $price * 0.15,
                        'total' => $price * 1.15,
                    ]);
                }
            }

            if($contractServices->contains('اجور عمال') && !$transaction->items->contains('description', 'اجور عمال - LABOUR')) {
                $price = $transaction->customer->contract->services->where('description', 'اجور عمال')->first()->pivot->price * $containers_count;
                $transaction->items()->create([
                    'number' => $transaction->items()->count() + 1,
                    'description' => 'اجور عمال - LABOUR',
                    'type' => 'ايراد عمال',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ]);
            }

            if($contractServices->contains('خدمات سابر') && !$transaction->items->contains('description', 'خدمات سابر - SABER FEES')) {
                $price = $transaction->customer->contract->services->where('description', 'خدمات سابر')->first()->pivot->price * $containers_count;
                $transaction->items()->create([
                    'number' => $transaction->items()->count() + 1,
                    'description' => 'خدمات سابر - SABER FEES',
                    'type' => 'ايراد سابر',
                    'amount' => $price,
                    'tax' => $price * 0.15,
                    'total' => $price * 1.15,
                ]);
            }
        }

        $validated = $request->validated();
        $validated['isPaid'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
        $invoice = Invoice::create($validated);

        $storage_price_before_tax = 0;

        foreach($transaction->containers as $container) {
            if($container->invoices->isEmpty() 
                && $container->policies->where('type', 'تخزين')->isNotEmpty() 
                && $container->status == 'تم التسليم') {
                $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
                $storage_price = $container->policies->where('type', 'تخزين')->first()->storage_price;
                if($period > $container->policies->where('type', 'تخزين')->first()->storage_duration) {
                    $days = (int) Carbon::parse($container->date)
                        ->addDays((int) $container->policies->where('type', 'تخزين')->first()->storage_duration)
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
            $invoice->containers()->attach($container->id, ['amount' => $storage_price_before_tax]);
        }

        if($storage_price_before_tax > 0 && !$transaction->items->contains('description', 'رسوم تخزين - STORAGE FEES')) {
            $transaction->items()->create([
                'number' => $transaction->items()->count() + 1,
                'description' => 'رسوم تخزين - STORAGE FEES',
                'type' => 'ايراد تخزين',
                'amount' => $storage_price_before_tax,
                'tax' => $storage_price_before_tax * 0.15,
                'total' => $storage_price_before_tax * 1.15,
            ]);
        }

        $transaction->refresh(); // to get latest items

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

        $amountBeforeTax = 0;
        $tax = 0;
        $totalAmount = 0;

        foreach($transaction->items as $item) {
            $amountBeforeTax += $item->amount;
            $tax += $item->tax;
            $totalAmount += $item->total;
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $totalAmount;
        $invoice->save();

        logActivity('إنشاء فاتورة تخليص', "تم إنشاء فاتورة تخليص رقم " . $invoice->code . "للعميل " . $invoice->customer->name, null, $invoice->toArray());

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.clearance.details', $invoice).'">عرض الفاتورة</a>');
    }

    public function clearanceInvoiceDetails(Invoice $invoice) {
        $transaction = Transaction::where('customer_id', $invoice->customer_id)
            ->whereHas('containers', function ($query) use ($invoice) {
                $containerIds = $invoice->containers->pluck('id')->toArray();
                $query->whereIn('container_id', $containerIds);
            })
            ->first();

        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

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
            'transaction',
            'discountValue', 
            'hatching_total', 
            'qrCode',
        ));
    }
    
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

        $validated = $request->validated();
        $validated['isPaid'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
        $invoice = Invoice::create($validated);

        $policyIds = $request->input('shipping_policy_ids', []);
        $shippingPolicies = ShippingPolicy::whereIn('id', $policyIds)->get();
        $amountBeforeTax = 0;

        foreach($shippingPolicies as $policy) {
            $amountBeforeTax += $policy->total_cost;
            $invoice->shippingPolicies()->attach($policy->id, ['amount' => $policy->total_cost]);
        }

        $discountValue = ($request->discount ?? 0) / 100 * $amountBeforeTax;
        $amountAfterDiscount = $amountBeforeTax - $discountValue;
        $tax_rate = $request->input('tax_rate', 15) / 100;
        $tax = $amountAfterDiscount * $tax_rate;
        $amount = $amountAfterDiscount + $tax;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax_rate = $request->input('tax_rate', 15);
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $amount;
        $invoice->save();

        logActivity('إنشاء فاتورة شحن', "تم إنشاء فاتورة شحن رقم " . $invoice->code . "للعميل " . $invoice->customer->name, null, $invoice->toArray());

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.shipping.details', $invoice).'">عرض الفاتورة</a>');  
    }

    public function shippingInvoiceDetails(Invoice $invoice) {
        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

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
            'discountValue', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function updateInvoice(Request $request, Invoice $invoice) {
        if(Gate::denies('تعديل فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل الفواتير');
        }
        $invoice->isPaid = $request->isPaid;
        $invoice->save();

        logActivity('تحديث فاتورة', "تم تحديث حالة الفاتورة رقم " . $invoice->code . " إلى " . $invoice->isPaid);
        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }

    public function postInvoice(Invoice $invoice) {
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
        } else {
            $incomeAccount = Account::where('name', 'ايرادات متنوعة')->where('level', 5)->first();
        }
        $taxAccount = Account::where('name', 'ضريبة القيمة المضافة من الايرادات')->where('level', 5)->first();

        $journal = JournalEntry::create([
            'date' => $invoice->date,
            'totalDebit' => $invoice->total_amount,
            'totalCredit' => $invoice->total_amount,
            'user_id' => Auth::user()->id,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $incomeAccount->id,
            'debit' => 0.00,
            'credit' => $invoice->amount_after_discount,
            'description' => 'ايرادات ' . ($invoice->type == 'تخزين' || $invoice->type == 'تخليص' || $invoice->type == 'شحن' ? $invoice->type : 'متنوعة') . ' فاتورة رقم ' . $invoice->code
        ]);

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
            if(!$item->is_posted) {
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
        $invoices = Invoice::where('customer_id', $customer_id)->where('isPaid', 'لم يتم الدفع')->get();

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
            $invoice->isPaid = 'تم الدفع';
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

    public function invoicesReports(Request $request) {
        $invoices = Invoice::orderBy('code', 'asc')->get();
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

        if($customer !== 'all') {
            $invoices = $invoices->where('customer_id', $customer);
        }
        if($from) {
            $invoices = $invoices->where('date', '>=', $from);
        }
        if($to) {
            $to = Carbon::parse($to)->endOfDay();
            $invoices = $invoices->where('date', '<=', $to);
        }
        if($type !== 'all') {
            $invoices = $invoices->where('type', $type);
        }
        if($payment_method !== 'all') {
            $invoices = $invoices->where('payment_method', $payment_method);
        }
        if($is_posted !== 'all') {
            $invoices = $invoices->where('is_posted', $is_posted == 'true' ? true : false);
        }

        $perPage = $request->input('per_page', 100);
        $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $invoices->forPage(request()->get('page', 1), $perPage),
            $invoices->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        return redirect()->back()->with('success', 'تم حذف الفاتورة ' . $name . ' بنجاح');
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
