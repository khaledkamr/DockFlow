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
use Illuminate\Support\Facades\Gate;

class AccountingController extends Controller
{
    public function tree() {
        $accounts = Account::where('parent_id', null)->get();
        return view('pages.accounting.tree', compact('accounts'));
    }

    public function createRoot(RootRequest $request) {
        if(Gate::denies('إنشاء مستوى حساب')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء مستويات حساب');
        }
        $validated = $request->validated();
        $name = $validated['name'];
        Account::create($validated);
        return redirect()->back()->with('success', "تم إنشاء الفرع '$name' بنجاح");
    }

    public function updateRoot(Request $request, $id) {
        // if(Gate::denies('تعديل مستوى حساب')) {
        //     return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل مستويات حساب');
        // }

        $root = Account::findOrFail($id);
        $name = $request->input('name');
        $root->update($request->all());

        return redirect()->back()->with('success', "تم تعديل المستوى '$name' بنجاح");
    }

    public function deleteRoot($id) {
        if(Gate::denies('حذف مستوى حساب')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف مستويات حساب');
        }

        $root = Account::findOrFail($id);
        if($root->children()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المستوى لوجود مستويات فرعية مرتبطة به');
        }
        if($root->journalLines()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المستوى لوجود قيود مرتبطة به');
        }

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
            } elseif($voucher->type == 'سند صرف نقدي') {
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
        
        return view('pages.accounting.entries', compact('accounts', 'vouchers', 'balance', 'journals', 'balanceArray', 'company'));
    }

    public function createJournal(JournalRequest $request) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء قيود');
        }

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
            'date' => $request->date,
            'user_id' => Auth::user()->id,
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

    public function editJournal(JournalEntry $journal) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل القيود');
        }

        $accounts = Account::where('level', 5)->get();

        return view('pages.accounting.vouchers.editJournal', compact('journal', 'accounts'));
    }

    public function updateJournal(Request $request, JournalEntry $journal) {
        $journal->lines()->delete();

        $journal->update([
            'date' => $request->date,
            'totalDebit' => $request->debitSum,
            'totalCredit' => $request->creditSum,
            'modifier_id' => Auth::user()->id,
        ]);

        foreach ($request->account_id as $index => $accountId) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $accountId,
                'debit'            => $request->debit[$index] ?? 0,
                'credit'           => $request->credit[$index] ?? 0,
                'description'      => $request->description[$index],
            ]);
        }

        return redirect()->back()->with('success', 'تم تعديل القيد بنجاح');
    }

    public function journalDetails(JournalEntry $journal) {
        return view('pages.accounting.vouchers.journalDetails', compact(
            'journal'
        ));
    }

    public function createVoucher(VoucherRequest $request) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء سندات');
        }

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
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء قيود');
        }

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

        $journal = JournalEntry::create([
            'date' => Carbon::now(),
            'totalDebit' => $voucher->amount,
            'totalCredit' => $voucher->amount,
            'user_id' => Auth::user()->id,
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
        if(Gate::denies('تقارير القيود')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }
        
        $accountsLevel5 = collect();
        $accounts = collect();
        $entries = collect();
        $statement = collect();
        $trialBalance = collect();

        if($request->view == 'تقارير القيود') {
            $type = $request->input('type');
            $from = $request->input('from');
            $to = $request->input('to');
            $entries = JournalEntry::all();

            if($type && $type !== 'all') {
                $entries = $entries->filter(function($entry) use($type) {
                    return ($entry->voucher->type ?? 'قيد يومي') == $type;
                });
            }

            if($from && $to) {
                $entries = $entries->whereBetween('date', [$from, $to]);
            }
        } elseif($request->view == 'كشف حساب') {
            $accountsLevel5 = Account::where('level', 5)->get();
            $type = $request->input('type');
            $from = $request->input('from');
            $to = $request->input('to');

            $account = $request->input('account', null);
            $statement = JournalEntryLine::where('account_id', $account)->get();
            if($from && $to) {
                $statement = $statement->filter(function($line) use($from, $to) {
                    return $line->journal->date >= $from && $line->journal->date <= $to;
                });
            }
        } elseif($request->view == 'ميزان مراجعة') {
            $accounts = Account::all();
            $trialBalance = Account::where('level', 1)->get();
        }

        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $totalBalance = [];
        
        foreach($trialBalance as $account) {
            $balance = $account->calculateBalance($from, $to);
            $totalBalance[] = (object)[
                'account' => $account->name,
                'balance' => $balance->balance['credit']
            ];
        }

        // return $totalBalance;

        return view('pages.accounting.reports', compact(
            'accountsLevel5', 
            'entries', 
            'statement',
            'accounts',
            'trialBalance'
        ));
    }
}
