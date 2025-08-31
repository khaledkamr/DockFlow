<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Http\Requests\RootRequest;
use App\Http\Requests\JournalRequest;
use App\Http\Requests\VoucherRequest;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    public function tree() {
        $accounts = Account::where('parent_id', null)->get();
        return view('admin.accounting.tree', compact('accounts'));
    }

    public function createRoot(RootRequest $request) {
        $validated = $request->validated();
        $name = $validated['name'];
        Account::create($validated);
        return redirect()->back()->with('success', "تم إنشاء الفرع '$name' بنجاح");
    }

    public function deleteRoot($id) {
        $root = Account::findOrFail($id);
        $name = $root->name;
        $root->delete();
        return redirect()->back()->with('success', "تم حذف المستوى '$name' بنجاح");
    }

    public function entries() {
        $company = Company::first();
        $accounts = Account::where('level', 5)->get();
        $vouchers = Voucher::all();
        $journals = JournalEntry::all();

        $balance = 0;
        $balanceArray = [];
        $vouchersBox = $vouchers->filter(function($voucher) {
            return $voucher->type == 'سند قبض نقدي' || $voucher->type == 'سند صرف نقدي';
        });
        foreach($vouchersBox as $voucher) {
            if($voucher->type == 'سند قبض نقدي') {
                $balance += $voucher->amount;
                $balanceArray[] = $balance;
            } else {
                $balance -= $voucher->amount;
                $balanceArray[] = $balance;
            }
        }
        
        if(request()->query('view') == 'سند قبض نقدي') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند قبض نقدي';
            });
        }
        elseif(request()->query('view') == 'سند قبض بشيك') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند قبض بشيك';
            });
        }
        elseif(request()->query('view') == 'سند صرف نقدي') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند صرف نقدي';
            });
        }
        elseif(request()->query('view') == 'سند صرف بشيك') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند صرف بشيك';
            });
        }
        elseif(request()->query('view') == 'الصندوق') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند قبض نقدي' || $voucher->type == 'سند صرف نقدي';
            });
        }
        
        return view('admin.accounting.entries', compact('accounts', 'vouchers', 'balance', 'journals', 'balanceArray', 'company'));
    }

    public function createJournal(JournalRequest $request) {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($request->account_id as $index => $accountId) {
            if (!$accountId || !$request->account_code[$index]) {
                return redirect()->back()->with('error', 'جميع الحسابات مطلوبة');
            }

            $debit = floatval($request->debit[$index] ?? 0);
            $credit = floatval($request->credit[$index] ?? 0);

            $totalDebit += $debit;
            $totalCredit += $credit;

            if ($debit > 0 && $credit > 0) {
                return redirect()->back()->with('error', 'لا يمكن إدخال مدين ودائن في نفس السطر');
            }
        }

        if ($totalDebit != $totalCredit) {
            return redirect()->back()->with('error', 'القيد غير متزن يجب أن يتساوى مجموع المدين مع مجموع الدائن');
        }
        
        $journalEntry = JournalEntry::create([
            'code' => $request->code,
            'date' => $request->date,
            'made_by' => Auth::user()->name,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalDebit,
        ]);

        foreach ($request->account_id as $index => $accountId) {
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id'       => $accountId,
                'debit'            => $request->debit[$index] ?? 0,
                'credit'           => $request->credit[$index] ?? 0,
                'description'      => $request->description[$index],
            ]);
        }

        return redirect()->back()->with('success', 'تم إضافة القيد بنجاح');
    }

    public function journalDetails($id) {
        $journal = JournalEntry::with('lines')->findOrFail($id);
        return view('admin.accounting.vouchers.journalDetails', compact(
            'journal'
        ));
    }

    public function createVoucher(VoucherRequest $request) {
        $code = $request->account_code;
        $validated = $request->validated();
        $account = Account::where('code', $code)->first();
        $validated['account_id'] = $account->id;
        Voucher::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء سند بنجاح');
    }

    public function deleteVoucher($id) {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return redirect()->back()->with('success', 'تم حذف السند بنجاح');
    }

    public function convertToJournal($id) {
        $voucher = Voucher::findOrFail($id);
        if($voucher->type == 'سند صرف نقدي') {
            $debitAccount = Account::findOrFail($voucher->account_id);
            $creditAccount = Account::where('name', 'صندوق الساحة')->first();
        } elseif($voucher->type == 'سند صرف بشيك') {
            $debitAccount = Account::findOrFail($voucher->account_id);
            $creditAccount = Account::where('name', 'البنك الاهلي')->first();
        } elseif($voucher->type == 'سند قبض بشيك') {
            $debitAccount = Account::where('name', 'البنك الاهلي')->first();
            $creditAccount = Account::findOrFail($voucher->account_id);
        } elseif ($voucher->type == 'سند قبض نقدي') {
            $debitAccount = Account::where('name', 'صندوق الساحة')->first();
            $creditAccount = Account::findOrFail($voucher->account_id);
        }

        $lastJournalCode = JournalEntry::latest('id')->first()->code;

        $journal = JournalEntry::create([
            'code' => $lastJournalCode + 1,
            'date' => Carbon::now()->format('Y-m-d'),
            'totalDebit' => $voucher->amount,
            'totalCredit' => $voucher->amount,
            'made_by' => Auth::user()->name,
            'voucher_id' => $voucher->id
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $debitAccount->id,
            'debit' => $voucher->amount,
            'credit' => 0.00,
            'description' => $voucher->description
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $creditAccount->id,
            'debit' => 0.00,
            'credit' => $voucher->amount,
            'description' => $voucher->description
        ]);

        return redirect()->back()->with('success', 'تم ترحيل السند الى قيد بنجاح');
    }   

    public function reports(Request $request) {
        $accounts = Account::where('level', 5)->get();
        $entries = JournalEntry::all();
        $account = $request->input('account', null);
        $statement = JournalEntryLine::where('account_id', $account)->get();
        return view('admin.accounting.reports', compact('accounts', 'entries', 'statement'));
    }
}
