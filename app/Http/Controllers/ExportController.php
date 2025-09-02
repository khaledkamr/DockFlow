<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\ContainersExport;
use App\Exports\JournalEntryExport;
use App\Models\Account;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
