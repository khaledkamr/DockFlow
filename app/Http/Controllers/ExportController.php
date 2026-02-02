<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\AgingReportExport;
use App\Exports\ContainersExport;
use App\Exports\InvoicesExport;
use App\Exports\JournalEntryExport;
use App\Exports\PoliciesExport;
use App\Exports\ShippingPoliciesExport;
use App\Exports\TransactionsExport;
use App\Exports\TransportOrdersExport;
use App\Exports\TrialBalanceExport;
use App\Exports\UserActivityExport;
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
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\ExpenseInvoice;
use App\Models\InvoiceStatement;
use App\Models\Policy;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\UserLog;
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
        
        if($reportType == 'journal_entries') {
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

    public function printAccountStatement(Request $request) {
        $from = $request->input('from');
        $to = $request->input('to');
        $account = $request->input('account', null);
        $costCenter = $request->input('cost_center', null);
        if(!$account && !$costCenter) {
            $statement = collect();
        } else {
            $statement = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->select('journal_entry_lines.*')
                ->when($account, function($query, $account) {
                    return $query->where('account_id', $account);
                })
                ->when($costCenter, function($query, $costCenter) {
                    return $query->where('cost_center_id', $costCenter);
                })
                ->with(['journal', 'account', 'costCenter'])
                ->orderBy('journal_entries.date')
                ->orderBy('journal_entries.code')
                ->get();
        }

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

        $opening_balance = number_format($opening_balance, 2, '.', '');

        if($from && $to) {
            $statement = $statement->filter(function($line) use($from, $to) {
                $date = Carbon::parse($line->journal->date);
                return $date->between($from, $to);
            });
        }
        
        return view('reports.account_statement', compact(
            'from',
            'to',
            'statement',
            'opening_balance'
        ));
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

    public function printContainersReport(Request $request) {
        $containers = Container::query();

        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');
        $customer = $request->input('customer', 'all');
        $invoiced = $request->input('invoiced', 'all');
        $search = $request->input('search', null);

        if($to && $from) {
            $containers->whereBetween('date', [$from, $to]);
        }
        if($status !== 'all') {
            if($status == 'متأخر') {
                $containers->where('status', 'في الساحة')
                    ->whereHas('policies', function ($q) {
                        $q->where('type', 'تخزين')
                            ->whereRaw('DATEDIFF(NOW(), containers.date) > policies.storage_duration');
                    });
            } else {
                $containers->where('status', $status);
            }
        }
        if($type !== 'all') {
            $containers->where('container_type_id', $type);
        }
        if($customer !== 'all') {
            $containers->where('customer_id', $customer);
        }
        if($invoiced !== 'all') {
            if($invoiced == 'مع فاتورة') {
                $containers->whereHas('invoices');
            } elseif($invoiced == 'بدون فاتورة') {
                $containers->whereDoesntHave('invoices');
            }
        }
        if($search) {
            $containers->where(function($query) use ($search) {
                $query->where('code', 'like', "%$search%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })->orWhere('location', 'like', "%$search%");
            });
        }

        $containers = $containers->with(['customer', 'containerType', 'invoices'])->orderBy('date')->get();
        $filters = $request->all();
        logActivity('طباعة تقرير حاويات', "تم طباعة تقرير الحاويات بتصفية: ", $filters);

        return view('reports.containers', compact('containers', 'from', 'to'));
    }

    public function printPoliciesReport(Request $request) {
        $types = ['تخزين', 'خدمات'];
        $policies = Policy::query()->whereIn('type', $types);

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $invoiced = $request->input('invoiced', 'all');
        $search = $request->input('search', null);

        if($from && $to) {
            $policies->whereBetween('date', [$from, $to]);
        }
        if($customer != 'all') {
            $policies->where('customer_id', $customer);
        }
        if($type && $type != 'all') {
            $policies->where('type', $type);
        }
        if($invoiced && $invoiced != 'all') {
            if($invoiced == 'invoiced') {
                $policies->whereHas('containers.invoices', function($query) {
                    $query->whereIn('type', ['تخزين', 'خدمات']);
                });
            } elseif($invoiced == 'not_invoiced') {
                $policies->whereDoesntHave('containers.invoices', function($query) {
                    $query->whereIn('type', ['تخزين', 'خدمات']);
                });
            }
        }
        if($search) {
            $policies->where(function($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('containers', function($q) use ($search) {
                        $q->where('code', 'like', '%' . $search . '%');
                    })
                    ->orWhere('reference_number', 'like', $search)
                    ->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        $policies = $policies->with('customer', 'containers.invoices')->orderBy('code')->get();
        $filters = $request->all();
        logActivity('طباعة تقرير بوالص الشحن', "تم طباعة تقرير بوالص الشحن بتصفية: ", $filters);

        return view('reports.policies_report', compact('policies', 'from', 'to'));
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
        $policies = ShippingPolicy::query();

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
        $search = $request->input('search', null);

        if($customer && $customer != 'all') {
            $policies->where('customer_id', $customer);
        }
        if($from) {
            $policies->where('date', '>=', $from);
        }
        if($to) {
            $policies->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $policies->where('type', $type);
        }
        if($status && $status != 'all') {
            $policies->where('is_received', $status == 'تم التسليم' ? true : false);
        }
        if($invoice_status && $invoice_status != 'all') {
            if($invoice_status == 'with_invoice') {
                $policies->whereHas('invoices');
            } elseif($invoice_status == 'without_invoice') {
                $policies->whereDoesntHave('invoices');
            }
        }
        if($supplier && $supplier != 'all') {
            $policies->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $policies->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $policies->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $policies->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $policies->where('to', $delivery_location);
        }
        if($search) {
            $policies->where(function($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('goods', function($q) use ($search) {
                        $q->where('description', 'like', '%' . $search . '%');
                    });
            });
        }

        $policies = $policies->with(['customer', 'made_by'])->orderBy('code')->get();

        $filters = $request->all();
        logActivity('طباعة تقرير بوالص الشحن', "تم طباعة تقرير بوالص الشحن بتصفية: ", $filters);

        return view('reports.shipping_report', compact('policies', 'from', 'to'));
    }

    public function printTransactionReports(Request $request) {
        $transactions = Transaction::orderBy('code', 'asc')->get();
        $company = Auth::user()->company;

        $customer_id = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');
        $search = $request->input('search', null);

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
        if($search) {
            $transactions->where('code', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('customs_declaration', 'like', '%' . $search . '%')
                ->orWhere('policy_number', 'like', '%' . $search . '%')
                ->orWhereHas('containers', function ($query) use ($search) {
                    $query->where('code', 'like', '%' . $search . '%');
                })
                ->orWhereDate('date', 'like', '%' . $search . '%');
        }

        $filters = $request->all();
        logActivity('طباعة تقرير معاملات التخليص', "تم طباعة تقرير معاملات التخليص بتصفية: ", $filters);

        return view('reports.transaction_report', compact('company', 'transactions', 'from', 'to'));
    }

    public function printTransportOrderReports(Request $request) {
        $transportOrders = TransportOrder::query();

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $supplier = $request->input('supplier', 'all');
        $driver = $request->input('driver', 'all');
        $vehicle = $request->input('vehicle', 'all');
        $loading_location = $request->input('loading_location', 'all');
        $delivery_location = $request->input('delivery_location', 'all');
        $search = $request->input('search', null);

        if($customer && $customer != 'all') {
            $transportOrders->where('customer_id', $customer);
        }
        if($from) {
            $transportOrders->where('date', '>=', $from);
        }
        if($to) {
            $transportOrders->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $transportOrders->where('type', $type);
        }
        if($supplier && $supplier != 'all') {
            $transportOrders->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $transportOrders->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $transportOrders->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $transportOrders->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $transportOrders->where('to', $delivery_location);
        }
        if($search) {
            $transportOrders->where('code', 'like', '%' . $search . '%')
                ->orWhereHas('transaction', function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%');
                })
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('containers', function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%');
                })
                ->orWhere('date', 'like', '%' . $search . '%');
        }

        $transportOrders = $transportOrders->with(['customer', 'driver', 'vehicle', 'supplier', 'made_by'])->orderBy('code')->get();

        $filters = $request->all();
        logActivity('طباعة تقرير اشعارات النقل', "تم طباعة تقرير اشعارات النقل بتصفية: ", $filters);

        return view('reports.transport_order_report', compact('transportOrders', 'from', 'to'));
    }

    public function printInvoiceReports(Request $request) {
        $invoices = Invoice::query();

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $payment_method = $request->input('payment_method', 'all');
        $search = $request->input('search', null);

        if($customer && $customer != 'all') {
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
            $invoices->where('type', $type);
        }
        if($payment_method !== 'all') {
            $invoices->where('payment_method', $payment_method);
        }
        if($search) {
            $invoices->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  })->orWhere('date', 'like', "%$search%");
            });
        }
        
        $invoices = $invoices->with(['customer', 'made_by'])->orderBy('code')->get();

        $filters = $request->all();
        logActivity('طباعة تقرير الفواتير', "تم طباعة تقرير الفواتير بتصفية: ", $filters);

        return view('reports.invoice_report', compact('invoices', 'from', 'to'));
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

    public function printUserActivityReport(Request $request) {
        $activities = [];

        $user = $request->input('user', 'all');
        $action = $request->input('action', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);

        $activitiesQuery = UserLog::query();

        if($user != 'all') {
            $activitiesQuery->where('user_id', $user);
        }
        if($action != 'all') {
            $activitiesQuery->where('action', 'like', '%' . $action . '%');
        }
        if($from) {
            $activitiesQuery->whereDate('created_at', '>=', $from);
        }
        if($to) {
            $activitiesQuery->whereDate('created_at', '<=', $to);
        }

        $activities = $activitiesQuery->with('user')->get();

        $filters = $request->all();
        logActivity('طباعة تقرير نشاط المستخدمين', "تم طباعة تقرير نشاط المستخدمين بتصفية: ", $filters);

        return view('reports.user_activity_report', compact('activities', 'from', 'to'));
    }

    public function printAgingReport(Request $request) {
        $customers = Customer::all();
        $from = $request->input('from', null);
        $to = $request->input('to', null);

        $filters = $request->all();
        logActivity('طباعة تقرير أعمار الذمم', "تم طباعة تقرير أعمار الذمم بتصفية: ", $filters);

        return view('reports.aging_report', compact('customers', 'from', 'to'));
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
        } elseif($reportType == 'policies_report') {
            $filters = $request->all();
            logActivity('تصدير تقرير بوالص التخزين الى اكسيل', "تم تصدير تقرير بوالص التخزين الى اكسيل بتصفية: ", $filters);
            return Excel::download(new PoliciesExport($filters), 'تقرير بوالص التخزين.xlsx');
        } elseif($reportType == 'user_activity') {
            $filters = $request->all();
            logActivity('تصدير تقرير نشاط المستخدمين الى اكسيل', "تم تصدير تقرير نشاط المستخدمين الى اكسيل بتصفية: ", $filters);
            return Excel::download(new UserActivityExport($filters), 'تقرير نشاط المستخدمين.xlsx');
        } elseif($reportType == 'aging_report') {
            $filters = $request->all();
            logActivity('تصدير تقرير أعمار الذمم الى اكسيل', "تم تصدير تقرير أعمار الذمم الى اكسيل بتصفية: ", $filters);
            return Excel::download(new AgingReportExport($filters), 'تقرير أعمار الذمم.xlsx');
        }

        abort(404);
    }
}
