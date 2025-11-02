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
                return $invoice->type == 'تخزين';
            })->isEmpty();
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

        return view('pages.invoices.invoiceServicesDetails', compact(
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

        $validated = $request->validated();
        $validated['isPaid'] = $request->payment_method == 'آجل' ? 'لم يتم الدفع' : 'تم الدفع';
        $invoice = Invoice::create($validated);

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

        return view('pages.invoices.clearanceInvoiceDetails', compact(
            'invoice', 
            'transaction',
            'discountValue', 
            'hatching_total', 
            'qrCode',
        ));
    }

    public function storeInvoice(InvoiceRequest $request) {
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

        return view('pages.invoices.invoiceDetails', compact(
            'invoice', 
            'services', 
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
        } else {
            $incomeAccount = Account::where('name', 'ايرادات متنوعة')->where('level', 5)->first();
        }
        $taxAccount = Account::where('name', 'ضريبة القيمة المضافة من المصروفات')->where('level', 5)->first();
        

        $journal = JournalEntry::create([
            'date' => Carbon::now(),
            'totalDebit' => $invoice->total_amount,
            'totalCredit' => $invoice->total_amount,
            'user_id' => Auth::user()->id,
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $incomeAccount->id,
            'debit' => 0.00,
            'credit' => $invoice->amount_after_discount,
            'description' => 'ايرادات ' . ($invoice->type == 'تخزين' || $invoice->type == 'تخليص' ? $invoice->type : 'متنوعة') . ' فاتورة رقم ' . $invoice->code
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

        return redirect()->back()->with('success', "تم ترحيل الفاتورة بنجاح <a class='text-white fw-bold' href='".route('admin.journal.details', $journal)."'>عرض القيد</a>");
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

        return view('pages.invoices.createStatement', compact('customers', 'invoices'));
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

        return redirect()->back()->with('success', 'تم إنشاء مطالبة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('invoices.statements.details', $invoiceStatement).'">عرض المطالبة</a>');
    }

    public function invoiceStatementDetails(InvoiceStatement $invoiceStatement) {
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoiceStatement->amount, 2));
        return view('pages.invoices.statementDetails', compact('invoiceStatement', 'hatching_total'));
    }
}
