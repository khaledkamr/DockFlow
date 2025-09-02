<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            return view('reports.containers', compact('company', 'containers', 'from', 'to', 'status', 'type', 'customer'));
        } elseif ($reportType == 'entry_permission') {
            $policyContainers = [];
            foreach($request->containers as $container) {
                $policyContainers[] = Container::findOrFail($container);
            }
            return view('reports.entry_permission', compact('company', 'policyContainers'));
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
}
