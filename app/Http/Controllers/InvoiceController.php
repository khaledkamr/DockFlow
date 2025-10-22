<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicNumberConverter;
use App\Helpers\QrHelper;
use App\Http\Requests\ClaimRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Account;
use App\Models\Claim;
use App\Models\Container;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceStatement;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    public function invoices(Request $request) {
        $invoices = Invoice::orderBy('id', 'desc')->get();
        $methodFilter = request()->query('paymentMethod');
        if ($methodFilter && $methodFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($methodFilter) {
                return $invoice->payment_method === $methodFilter;
            });
        }
        $paymentFilter = request()->query('payment');
        if ($paymentFilter && $paymentFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($paymentFilter) {
                return $invoice->payment === $paymentFilter;
            });
        }
        $search = $request->input('search', null);
        if($search) {
            $invoices = $invoices->filter(function($invoice) use($search) {
                return stripos($invoice->id, $search) !== false 
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
            return $container->invoices->count() == 0;
        });

        foreach($containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->first()->contract->services[0]->pivot->price;
            if($container->period > $container->policies->first()->contract->services[0]->pivot->unit) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays($container->policies->first()->contract->services[0]->pivot->unit)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $container->late_days = $days;
                $container->late_fee = $days * $container->policies->first()->contract->services[1]->pivot->price;
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

        return view('pages.invoices.createInvoice', compact('customers', 'containers'));
    }

    public function storeServiceInvoice(Request $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $invoice = Invoice::create([
            'type' => 'خدمات',
            'customer_id' => $request->customer_id,
            'user_id' => $request->user_id,
            'payment_method' => $request->payment_method,
            'discount' => $request->discount ?? 0,
            'date' => Carbon::now(),
            'payment' => $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع',
        ]);

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
        $tax = $amountAfterDiscount * 0.15;
        $amount = $amountAfterDiscount + $tax;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $amount;
        $invoice->save();

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

        $accounts = collect();
        $moneyAccount = Account::where('name', 'النقدية')->first();
        $bankAccount = Account::where('name', 'البنوك')->first();

        $accounts = $accounts->merge($moneyAccount->children);
        $accounts = $accounts->merge($bankAccount->children);

        return view('pages.invoices.invoiceServicesDetails', compact(
            'invoice', 
            'discountValue', 
            'hatching_total', 
            'qrCode',
            'accounts'
        ));
    }

    public function storeClearanceInvoice(Request $request, Transaction $transaction) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }

        $invoice = Invoice::create([
            'type' => 'تخليص',
            'customer_id' => $request->customer_id,
            'user_id' => $request->user_id,
            'payment_method' => $request->payment_method,
            'discount' => $request->discount ?? 0,
            'date' => Carbon::now(),
            'payment' => $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع',
        ]);

        foreach($transaction->containers as $container) {
            $invoice->containers()->attach($container->id, ['amount' => 0]);
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

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.clearance.details', $invoice).'">عرض الفاتورة</a>');
    }

    public function clearanceInvoiceDetails(Invoice $invoice) {
        $transaction = Transaction::where('customer_id', $invoice->customer_id)
            ->whereHas('containers', function ($query) use ($invoice) {
                $containerIds = $invoice->containers->pluck('id')->toArray();
                $query->whereIn('container_id', $containerIds);
            })
            ->first();

        $amountBeforeTax = 0;

        foreach($transaction->items as $item) {
            $amountBeforeTax += $item->total;
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

        $accounts = collect();
        $moneyAccount = Account::where('name', 'النقدية')->first();
        $bankAccount = Account::where('name', 'البنوك')->first();

        $accounts = $accounts->merge($moneyAccount->children);
        $accounts = $accounts->merge($bankAccount->children);

        return view('pages.invoices.clearanceInvoiceDetails', compact(
            'invoice', 
            'transaction',
            'discountValue', 
            'hatching_total', 
            'qrCode',
            'accounts'
        ));
    }

    public function storeInvoice(Request $request) {
        if(Gate::denies('إنشاء فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء فواتير');
        }
        
        $invoice = Invoice::create([
            'type' => 'تخزين',
            'customer_id' => $request->customer_id,
            'user_id' => $request->user_id,
            'discount' => $request->discount ?? 0,
            'payment_method' => $request->payment_method,
            'date' => Carbon::now(),
            'payment' => $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع',
        ]);

        $containerIds = $request->input('container_ids', []);
        $containers = Container::whereIn('id', $containerIds)->get();
        $amountBeforeTax = 0;

        foreach($containers as $container) {
            $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $storage_price = $container->policies->first()->contract->services[0]->pivot->price;
            if($period > $container->policies->first()->contract->services[0]->pivot->unit) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays($container->policies->first()->contract->services[0]->pivot->unit)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $late_fee = $days * $container->policies->first()->contract->services[1]->pivot->price;
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
        $tax = $amountAfterDiscount * 0.15;
        $amount = $amountAfterDiscount + $tax;

        $invoice->amount_before_tax = $amountBeforeTax;
        $invoice->tax = $tax;
        $invoice->amount_after_discount = $amountAfterDiscount;
        $invoice->total_amount = $amount;
        $invoice->save();

        return redirect()->back()->with('success', 'تم إنشاء فاتورة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.details', $invoice).'">عرض الفاتورة</a>');
    } 

    public function invoiceDetails(Invoice $invoice) {
        $amountBeforeTax = 0;

        foreach($invoice->containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($container->exit_date));
            $container->storage_price = $container->policies->first()->contract->services[0]->pivot->price;
            if($container->period > $container->policies->first()->contract->services[0]->pivot->unit) {
                $days = (int) Carbon::parse($container->date)
                    ->addDays($container->policies->first()->contract->services[0]->pivot->unit)
                    ->diffInDays(Carbon::parse($container->exit_date));
                $container->late_days = $days;
                $container->late_fee = $days * $container->policies->first()->contract->services[1]->pivot->price;
            } else {
                $container->late_days = 'لا يوجد';
                $container->late_fee = 0;
            }
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
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

        $accounts = collect();
        $moneyAccount = Account::where('name', 'النقدية')->first();
        $bankAccount = Account::where('name', 'البنوك')->first();

        $accounts = $accounts->merge($moneyAccount->children);
        $accounts = $accounts->merge($bankAccount->children);

        return view('pages.invoices.invoiceDetails', compact(
            'invoice', 
            'services', 
            'discountValue', 
            'hatching_total', 
            'qrCode',
            'accounts'
        ));
    }

    public function updateInvoice(Request $request, Invoice $invoice) {
        if(Gate::denies('تعديل فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل الفواتير');
        }
        $invoice->payment = $request->payment;
        $invoice->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }

    public function postInvoice(Request $request, Invoice $invoice) {
        if($invoice->is_posted) {
            return redirect()->back()->with('error', 'هذه الفاتورة تم ترحيلها مسبقاً');
        }

        $debitAccount = Account::findOrFail($request->debit_account);
        $creditAccount = $invoice->customer->account;

        $journal = JournalEntry::create([
            'date' => Carbon::now(),
            'totalDebit' => $invoice->amount,
            'totalCredit' => $invoice->amount,
            'user_id' => Auth::user()->id,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $debitAccount->id,
            'debit' => $invoice->amount,
            'credit' => 0.00,
            'description' => 'ترحيل فاتورة رقم ' . $invoice->code
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $creditAccount->id,
            'debit' => 0.00,
            'credit' => $invoice->amount,
            'description' => 'ترحيل فاتورة رقم ' . $invoice->code
        ]);

        $invoice->is_posted = true;
        $invoice->save();

        return redirect()->back()->with('success', "تم ترحيل الفاتورة بنجاح <a class='text-white fw-bold' href='".route('admin.journal.details', $journal)."'>عرض القيد</a>");
    }

    public function invoiceStatements() {
        $invoiceStatements = InvoiceStatement::orderBy('id', 'desc')->get();
        return view('pages.invoices.statements', compact('invoiceStatements'));
    }

    public function createInvoiceStatement(Request $request) {
        $customers = Customer::all();
        $customer_id = $request->input('customer_id');
        $invoices = Invoice::where('customer_id', $customer_id)->where('payment', 'لم يتم الدفع')->get();

        return view('pages.invoices.createStatement', compact('customers', 'invoices'));
    }

    public function storeInvoiceStatement(Request $request) {
        return $request;
        InvoiceStatement::create($request->validated());
        return redirect()->back()->with('success', 'تم إنشاء بيان فاتورة جديدة بنجاح');
    }

    public function invoiceStatementDetails(InvoiceStatement $invoiceStatement) {
        return view('pages.invoices.statementDetails', compact('invoiceStatement'));
    }
}
