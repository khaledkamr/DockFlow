<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\ContainersExport;
use App\Exports\JournalEntryExport;
use App\Models\Account;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Helpers\QrHelper;
use App\Helpers\ArabicNumberConverter;

class ExportController extends Controller
{
    public function print($reportType, Request $request) {
        $id = $request->input('account');
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');

        $company = Company::first();
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
            return view('reports.entry_permission', compact('company', 'policyContainers'));
        } elseif ($reportType == 'exit_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            return view('reports.exit_permission', compact('company', 'policyContainers'));
        }
    }

    public function printContract($id) {
        $company = Company::first();
        $contract = Contract::findOrFail($id);
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        return view('reports.contract', compact('contract', 'company', 'months', 'days'));
    }

    public function printInvoice($code) {
        $company = Company::first();
        $invoice = Invoice::with('containers')->where('code', $code)->first();

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
            $container->total = $container->storage_price + $container->late_fee;
            $amountBeforeTax += $container->total;  
        }

        $invoice->subtotal = $amountBeforeTax;
        $invoice->tax = $amountBeforeTax * 0.15;
        $invoice->discount = 0;
        $invoice->total = $amountBeforeTax + $invoice->tax;

        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total, 2));

        $qrCode = QrHelper::generateZatcaQr(
            $invoice->customer->name,
            $invoice->customer->CR,
            $invoice->created_at->toIso8601String(),
            number_format($invoice->total, 2, '.', ''),
            number_format($invoice->tax, 2, '.', '')
        );

        return view('reports.invoice', compact('company', 'invoice', 'qrCode', 'hatching_total'));
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
