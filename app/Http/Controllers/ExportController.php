<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\ContainersExport;
use App\Exports\JournalEntryExport;
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
use App\Models\InvoiceStatement;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use App\Models\TransportOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ExportController extends Controller
{
    public function print($reportType, Request $request) {
        $id = $request->input('account');
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');
        $company = Auth::user()->company;
        
        if ($reportType == 'account_statement') {
            $statement = JournalEntryLine::where('account_id', $id)->get();
            if($from && $to) {
                $statement = $statement->filter(function($line) use($from, $to) {
                    return $line->journal->date >= $from && $line->journal->date <= $to;
                });
            }
            return view('reports.account_statement', compact('statement', 'company', 'from', 'to'));
        } elseif($reportType == 'journal_entries') {
            $entries = JournalEntry::all();
            if($type && $type !== 'all') {
                $entries = $entries->filter(function($entry) use($type) {
                    return ($entry->voucher->type ?? 'قيد يومي') == $type;
                });
            }
            if($from && $to) {
                $entries = $entries->whereBetween('date', [$from, $to]);
            }
            return view('reports.journal_entries', compact('entries', 'company', 'from', 'to'));
        } elseif ($reportType == 'containers') {
            $status = $request->input('status');
            $customer = $request->input('customer');

            $containers = Container::all();
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

            return view('reports.containers', compact('company', 'containers', 'from', 'to', 'status', 'type', 'customer'));
        } elseif ($reportType == 'entry_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'تخزين')->first();
            
            return view('reports.entry_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'exit_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'تسليم')->first();

            return view('reports.exit_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'service_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            $policy = $policyContainers[0]->policies->where('type', 'خدمات')->first();

            return view('reports.service_permission', compact('company', 'policyContainers', 'policy'));
        } elseif ($reportType == 'journal_entry') {
            $journal = JournalEntry::findOrFail($request->journal_id);
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
            $container->total = $container->storage_price + $container->late_fee + $services;
            $amountBeforeTax += $container->total;  
        }

        $invoice->subtotal = $amountBeforeTax;
        $invoice->tax = $amountBeforeTax * 0.15;
        $invoice->total = $amountBeforeTax + $invoice->tax;
        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->total;
        $invoice->total -= $discountValue;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('reports.invoice', compact('company', 'invoice', 'services', 'discountValue', 'qrCode', 'hatching_total'));
    }
    
    public function printInvoiceServices($code) {
        if(Gate::denies('طباعة فاتورة')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        }
        
        $invoice = Invoice::with('containers')->where('code', $code)->first();
        $company = $invoice->company;

        $amountBeforeTax = 0;

        foreach($invoice->containers as $container) {
            $services = 0;
            foreach($container->services as $service) {
                $services += $service->pivot->price;
            }
            $container->total = $services;
            $amountBeforeTax += $container->total;  
        }

        $invoice->subtotal = $amountBeforeTax;
        $invoice->tax = $amountBeforeTax * 0.15;
        $invoice->total = $amountBeforeTax + $invoice->tax;
        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->total;
        $invoice->total -= $discountValue;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('reports.invoiceServices', compact('company', 'invoice', 'services', 'discountValue', 'qrCode', 'hatching_total'));
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

        $amountBeforeTax = 0;

        foreach($transaction->items as $item) {
            $amountBeforeTax += $item->total;
        }

        $invoice->subtotal = $amountBeforeTax;
        $invoice->tax = $amountBeforeTax * 0.15;
        $invoice->total = $amountBeforeTax + $invoice->tax;
        $discountValue = ($invoice->discount ?? 0) / 100 * $invoice->total;
        $invoice->total -= $discountValue;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->company->name,
            $invoice->company->vatNumber,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

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

        return view('reports.shipping_invoice', compact('company', 'invoice', 'discountValue', 'hatching_total', 'qrCode'));
    }

    public function printInvoiceStatement($code) {
        // if(Gate::denies('طباعة فاتورة')) {
        //     return redirect()->back()->with('error', 'ليس لديك الصلاحية لطباعة الفواتير');
        // }
        
        $invoiceStatement = InvoiceStatement::where('code', $code)->first();
        $company = $invoiceStatement->company;
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoiceStatement->amount, 2));

        return view('reports.invoiceStatement', compact('company', 'invoiceStatement', 'hatching_total'));
    }

    public function printTransportOrder(TransportOrder $transportOrder) {
        $company = $transportOrder->company;
        return view('reports.transportOrder', compact('company', 'transportOrder'));
    }

    public function printShippingPolicy($policyId) {
        $policy = ShippingPolicy::with('goods')->findOrFail($policyId);
        $company = $policy->company;
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

        return view('reports.shipping_report', compact('company', 'policies', 'from', 'to'));
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
            $invoices = $invoices->where('date', '<=', $to);
        }
        if($type !== 'all') {
            $invoices = $invoices->where('type', $type);
        }
        if($payment_method !== 'all') {
            $invoices = $invoices->where('payment_method', $payment_method);
        }

        return view('reports.invoice_report', compact('company', 'invoices', 'from', 'to'));
    }

    public function excel($reportType, Request $request) {
        if($reportType == 'containers') {
            $filters = $request->all();
            return Excel::download(new ContainersExport($filters), 'تقرير الحاويات.xlsx');
        } elseif($reportType == 'account_statement') {
            $filters = $request->all();
            return Excel::download(new AccountStatementExport($filters), 'تقرير كشف الحساب.xlsx');
        } elseif($reportType == 'journal_entries') {
            $filters = $request->all();
            return Excel::download(new JournalEntryExport($filters), 'تقرير القيود اليومية.xlsx');
        }

        abort(404);
    }
}
