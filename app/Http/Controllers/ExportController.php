<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\ContainersExport;
use App\Exports\InvoicesExport;
use App\Exports\JournalEntryExport;
use App\Exports\ShippingPoliciesExport;
use App\Exports\TransactionsExport;
use App\Exports\TransportOrdersExport;
use App\Exports\TrialBalanceExport;
use App\Models\Account;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Helpers\QrHelper;
use App\Helpers\ArabicNumberConverter;
use App\Models\ExpenseInvoice;
use App\Models\InvoiceStatement;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use App\Models\TransportOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class ExportController extends Controller
{
    public function print($reportType, Request $request) {
        $id = $request->input('account');
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');
        $company = Auth::user()->company;
        
        if ($reportType == 'account_statement') {
            $account = Account::findOrFail($id);
            $statement = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->select('journal_entry_lines.*')
                ->where('account_id', $account->id)
                ->orderBy('journal_entries.date')
                ->orderBy('journal_entries.code')
                ->get();

            $opening_balance = 0;
            if($from) {
                $opening_lines = $statement->filter(function($line) use($from) {
                    $date = Carbon::parse($line->journal->date);
                    return $date->lt(Carbon::parse($from));
                });
                foreach($opening_lines as $line) {
                    $opening_balance += $line->debit - $line->credit;
                }
            }

            if($from && $to) {
                $statement = $statement->filter(function($line) use($from, $to) {
                    $date = Carbon::parse($line->journal->date);
                    return $date->between($from, $to);
                });
            }

            $filters = $request->all();
            logActivity('طباعة كشف حساب', "تم طباعة كشف الحساب بتصفية: ", $filters);

            return view('reports.account_statement', compact(
                'statement', 
                'company', 
                'from', 
                'to', 
                'account',
                'opening_balance'
            ));
        } elseif($reportType == 'journal_entries') {
            $entries = JournalEntry::all();
            if($type && $type !== 'all') {
                $entries = $entries->filter(function($entry) use($type) {
                    return ($entry->voucher->type ?? 'قيد يومية') == $type;
                });
            }
            if($from && $to) {
                $entries = $entries->whereBetween('date', [$from, $to]);
            }
            $filters = $request->all();
            logActivity('طباعة قيود يومية', "تم طباعة قيود يومية بتصفية: ", $filters);
            return view('reports.journal_entries', compact('entries', 'company', 'from', 'to'));
        } elseif ($reportType == 'containers') {
            $status = $request->input('status');
            $customer = $request->input('customer');

            $containers = Container::orderBy('id', 'desc')->get();
            if($from && $to) {
                $containers = $containers->whereBetween('date', [$from, $to]);
            }
            if($status && $status !== 'all') {
                $containers = $containers->where('status', $status);
            }
            if($type && $type !== 'all') {
                $containers = $containers->where('container_type_id', $type);
            }
            if($customer && $customer !== 'all') {
                $containers = $containers->where('customer_id', $customer);
            }
            $filters = $request->all();
            logActivity('طباعة تقرير حاويات', "تم طباعة تقرير الحاويات بتصفية: ", $filters);
            return view('reports.containers', compact('company', 'containers', 'from', 'to', 'status', 'type', 'customer'));
        } elseif ($reportType == 'entry_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'تخزين')->first();
            $containers = implode(', ', array_map(function($c) {
                return $c->code;
            }, $policyContainers)); 
            logActivity('طباعة إذن دخول', "تم طباعة إذن الدخول للحاويات: " . $containers . " من البوليصة رقم " . $policy->code);
            return view('reports.entry_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'exit_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'تسليم')->first();
            $containers = implode(', ', array_map(function($c) {
                return $c->code;
            }, $policyContainers));
            logActivity('طباعة إذن خروج', "تم طباعة إذن الخروج للحاويات: " . $containers . " من البوليصة رقم " . $policy->code);
            return view('reports.exit_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'service_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'خدمات')->first();
            $containers = implode(', ', array_map(function($c) {
                return $c->code;
            }, $policyContainers));
            logActivity('طباعة إذن خدمات', "تم طباعة إذن الخدمات للحاويات: " . $containers . " من البوليصة رقم " . $policy->code);
            return view('reports.service_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'journal_entry') {
            $journal = JournalEntry::findOrFail($request->journal_id);
            logActivity('طباعة قيد يومية', "تم طباعة القيد اليومي رقم " . $journal->code);
            return view('reports.journal_entry', compact('company', 'journal'));
        }
    }

    public function printContract($id) {
        $contract = Contract::findOrFail($id);
        $company = $contract->company;
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        logActivity('طباعة عقد', "تم طباعة عقد رقم " . $contract->id);
        return view('reports.contract', compact('contract', 'company', 'months', 'days'));
    }

    public function printInvoice($code) {
        if(Gate::denies('طباعة فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        }
        
        $invoice = Invoice::with('containers')->where('code', $code)->first();
        $company = $invoice->company;

        $amountBeforeTax = 0;

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

        logActivity('طباعة فاتورة التخزين', "تم طباعة فاتورة التخزين رقم " . $invoice->code);

        return view('reports.invoice', compact('company', 'invoice', 'services', 'discountValue', 'qrCode', 'hatching_total'));
    }
    
    public function printInvoiceServices($code) {
        if(Gate::denies('طباعة فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        }
        
        $invoice = Invoice::with('containers')->where('code', $code)->first();
        $company = $invoice->company;

        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        logActivity('طباعة فاتورة خدمات', "تم طباعة فاتورة الخدمات رقم " . $invoice->code);

        return view('reports.invoiceServices', compact('company', 'invoice', 'discountValue', 'qrCode', 'hatching_total'));
    }

    public function printClearanceInvoice($code) {
        if(Gate::denies('طباعة فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        }
        
        $invoice = Invoice::with('containers')->where('code', $code)->first();
        $company = $invoice->company;

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

        logActivity('طباعة فاتورة تخليص', "تم طباعة فاتورة التخليص رقم " . $invoice->code);

        return view('reports.clearanceInvoice', compact('company', 'invoice', 'discountValue', 'qrCode', 'hatching_total'));
    }

    public function printShippingInvoice($code) {
        if(Gate::denies('طباعة فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        }
        
        $invoice = Invoice::with('shippingPolicies')->where('code', $code)->first();
        $company = $invoice->company;

        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->amount_before_tax;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total_amount, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        logActivity('طباعة فاتورة شحن', "تم طباعة فاتورة الشحن رقم " . $invoice->code);

        return view('reports.shipping_invoice', compact('company', 'invoice', 'discountValue', 'hatching_total', 'qrCode'));
    }

    public function printInvoiceStatement($code) {
        // if(Gate::denies('طباعة فاتورة')) {
        //     return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        // }
        
        $invoiceStatement = InvoiceStatement::where('code', $code)->first();
        $company = $invoiceStatement->company;
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoiceStatement->amount, 2));

        logActivity('طباعة مطالبة', "تم طباعة مطالبة رقم " . $invoiceStatement->code);

        return view('reports.invoiceStatement', compact('company', 'invoiceStatement', 'hatching_total'));
    }

    public function printTransportOrder(TransportOrder $transportOrder) {
        $company = $transportOrder->company;
        logActivity('طباعة اشعار نقل', "تم طباعة اشعار النقل رقم " . $transportOrder->code);
        return view('reports.transportOrder', compact('company', 'transportOrder'));
    }

    public function printShippingPolicy($policyId) {
        $policy = ShippingPolicy::with('goods')->findOrFail($policyId);
        $company = $policy->company;
        logActivity('طباعة بوليصة شحن', "تم طباعة بوليصة الشحن رقم " . $policy->code);
        return view('reports.shipping_policy', compact('company', 'policy'));
    }

    public function printShippingReports(Request $request) {
        $policies = ShippingPolicy::all();
        $company = Auth::user()->company;

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');
        $supplier = $request->input('supplier', 'all');
        $driver = $request->input('driver', 'all');
        $vehicle = $request->input('vehicle', 'all');
        $loading_location = $request->input('loading_location', 'all');
        $delivery_location = $request->input('delivery_location', 'all');

       if($customer && $customer != 'all') {
            $policies = $policies->where('customer_id', $customer);
        }
        if($from) {
            $policies = $policies->where('date', '>=', $from);
        }
        if($to) {
            $policies = $policies->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $policies = $policies->where('type', $type);
        }
        if($status && $status != 'all') {
            $policies = $policies->where('is_received', $status == 'تم التسليم' ? true : false);
        }
        if($invoice_status && $invoice_status != 'all') {
            $policies = $policies->filter(function($policy) use ($invoice_status) {
                $invoice = $policy->invoices->filter(function($invoice) {
                    return $invoice->type == 'شحن';
                })->isEmpty();
                if($invoice_status == 'with_invoice') {
                    return $invoice == false;
                } elseif($invoice_status == 'without_invoice') {
                    return $invoice;
                }
            });
        }
        if($supplier && $supplier != 'all') {
            $policies = $policies->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $policies = $policies->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $policies = $policies->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $policies = $policies->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $policies = $policies->where('to', $delivery_location);
        }

        $filters = $request->all();
        logActivity('طباعة تقرير بوالص الشحن', "تم طباعة تقرير بوالص الشحن بتصفية: ", $filters);

        return view('reports.shipping_report', compact('company', 'policies', 'from', 'to'));
    }

    public function printTransactionReports(Request $request) {
        $transactions = Transaction::orderBy('code', 'asc')->get();
        $company = Auth::user()->company;

        $customer_id = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');

        if($customer_id !== 'all') {
            $transactions = $transactions->where('customer_id', $customer_id);
        }
        if($from) {
            $transactions = $transactions->where('date', '>=', $from);
        }
        if($to) {
            $transactions = $transactions->where('date', '<=', $to);
        }
        if($status !== 'all') {
            $transactions = $transactions->where('status', $status);
        }
        if($invoice_status !== 'all') {
            if($invoice_status == 'with_invoice') {
                $transactions = $transactions->filter(function($transaction) {
                    return !$transaction->containers->every(function($container) {
                        return $container->invoices->where('type', 'تخليص')->isEmpty();
                    });
                });
            } elseif($invoice_status == 'without_invoice') {
                $transactions = $transactions->filter(function($transaction) {
                    return $transaction->containers->every(function($container) {
                        return $container->invoices->where('type', 'تخليص')->isEmpty();
                    });
                });
            }
        }

        $filters = $request->all();
        logActivity('طباعة تقرير معاملات التخليص', "تم طباعة تقرير معاملات التخليص بتصفية: ", $filters);

        return view('reports.transaction_report', compact('company', 'transactions', 'from', 'to'));
    }

    public function printTransportOrderReports(Request $request) {
        $transportOrders = TransportOrder::all();
        $company = Auth::user()->company;

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $supplier = $request->input('supplier', 'all');
        $driver = $request->input('driver', 'all');
        $vehicle = $request->input('vehicle', 'all');
        $loading_location = $request->input('loading_location', 'all');
        $delivery_location = $request->input('delivery_location', 'all');

        if($customer && $customer != 'all') {
            $transportOrders = $transportOrders->where('customer_id', $customer);
        }
        if($from) {
            $transportOrders = $transportOrders->where('date', '>=', $from);
        }
        if($to) {
            $transportOrders = $transportOrders->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $transportOrders = $transportOrders->where('type', $type);
        }
        if($supplier && $supplier != 'all') {
            $transportOrders = $transportOrders->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $transportOrders = $transportOrders->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $transportOrders = $transportOrders->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $transportOrders = $transportOrders->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $transportOrders = $transportOrders->where('to', $delivery_location);
        }

        $filters = $request->all();
        logActivity('طباعة تقرير اشعارات النقل', "تم طباعة تقرير اشعارات النقل بتصفية: ", $filters);

        return view('reports.transport_order_report', compact('company', 'transportOrders', 'from', 'to'));
    }

    public function printInvoiceReports(Request $request) {
        $invoices = Invoice::all();
        $company = Auth::user()->company;

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $payment_method = $request->input('payment_method', 'all');

        if($customer && $customer != 'all') {
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

        $filters = $request->all();
        logActivity('طباعة تقرير الفواتير', "تم طباعة تقرير الفواتير بتصفية: ", $filters);

        return view('reports.invoice_report', compact('company', 'invoices', 'from', 'to'));
    }

    public function printExpenseInvoice($code) {
        $invoice = ExpenseInvoice::where('code', $code)->first();
        $company = $invoice->company;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));

        logActivity('طباعة فاتورة مصروفات', "تم طباعة فاتورة المصروفات رقم " . $invoice->code);

        return view('reports.expense_invoice', compact('company', 'invoice', 'hatching_total'));
    }

    public function printTrialBalance(Request $request) {
        $company = Auth::user()->company;
        if($request->has('type') && $request->input('type') != 'all') {
            $trialBalance = Account::where('name', $request->input('type'))
                ->where('level', $request->input('type_level'))->get();
        } else {
            $trialBalance = Account::where('level', 1)->get();
        }

        $from = $request->input('from', now()->startOfYear()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfYear()->format('Y-m-d'));
        $debit_movements = $request->input('debit_movements', '1');
        $credit_movements = $request->input('credit_movements', '1');
        $zero_balances = $request->input('zero_balances', '1');
        $with_balances = $request->input('with_balances', '0');

        $filters = $request->all();
        logActivity('طباعة ميزان المراجعة', "تم طباعة ميزان المراجعة بتصفية: ", $filters);

        return view('reports.trial_balance', compact('company', 'trialBalance', 'from', 'to', 'debit_movements', 'credit_movements', 'zero_balances', 'with_balances'));
    }

    public function excel($reportType, Request $request) {
        if($reportType == 'containers') {
            $filters = $request->all();
            logActivity('تصدير تقرير الحاويات الى اكسيل', "تم تصدير تقرير الحاويات بتصفية: ", $filters);
            return Excel::download(new ContainersExport($filters), 'تقرير الحاويات.xlsx');
        } elseif($reportType == 'account_statement') {
            $filters = $request->all();
            logActivity('تصدير تقرير كشف الحساب الى اكسيل', "تم تصدير تقرير كشف الحساب بتصفية: ", $filters);
            return Excel::download(new AccountStatementExport($filters), 'تقرير كشف الحساب.xlsx');
        } elseif($reportType == 'journal_entries') {
            $filters = $request->all();
            logActivity('تصدير تقرير القيود اليومية الى اكسيل', "تم تصدير تقرير القيود اليومية الى اكسيل بتصفية: ", $filters);
            return Excel::download(new JournalEntryExport($filters), 'تقرير القيود اليومية.xlsx');
        } elseif($reportType == 'trial_balance') {
            $filters = $request->all();
            logActivity('تصدير تقرير ميزان المراجعة الى اكسيل', "تم تصدير تقرير ميزان المراجعة بتصفية: ", $filters);
            return Excel::download(new TrialBalanceExport($filters), 'تقرير ميزان المراجعة.xlsx');
        } elseif($reportType == 'shipping_policies') {
            $filters = $request->all();
            logActivity('تصدير تقرير بوالص الشحن الى اكسيل', "تم تصدير تقرير بوالص الشحن الى اكسيل بتصفية: ", $filters);
            return Excel::download(new ShippingPoliciesExport($filters), 'تقرير بوالص الشحن.xlsx');
        } elseif($reportType == 'transport_orders') {
            $filters = $request->all();
            logActivity('تصدير تقرير اشعارات النقل الى اكسيل', "تم تصدير تقرير اشعارات النقل الى اكسيل بتصفية: ", $filters);
            return Excel::download(new TransportOrdersExport($filters), 'تقرير اشعارات النقل.xlsx');
        } elseif($reportType == 'invoices') {
            $filters = $request->all();
            logActivity('تصدير تقرير الفواتير الى اكسيل', "تم تصدير تقرير الفواتير الى اكسيل بتصفية: ", $filters);
            return Excel::download(new InvoicesExport($filters), 'تقرير الفواتير.xlsx');
        } elseif($reportType == 'transactions') {
            $filters = $request->all();
            logActivity('تصدير تقرير معاملات التخليص الى اكسيل', "تم تصدير تقرير معاملات التخليص الى اكسيل بتصفية: ", $filters);
            return Excel::download(new TransactionsExport($filters), 'تقرير معاملات التخليص.xlsx');
        }

        abort(404);
    }
}
