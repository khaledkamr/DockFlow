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
use App\Models\Attachment;
use App\Models\Company;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountingController extends Controller
{
    public function tree() {
        $accounts = Account::where('parent_id', null)->get();
        return view('pages.accounting.tree.parents', compact('accounts'));
    }

    public function createRoot(RootRequest $request) {
        if(Gate::denies('إنشاء مستوى حساب')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء مستويات حساب');
        }

        $validated = $request->validated();
        $name = $validated['name'];
        $new = Account::create($validated);

        logActivity('إنشاء حساب', "تم إنشاء حساب جديد بإسم: " . $name . " بمستوى " . $validated['level'] . " في دليل الحسابات", null, $new->toArray());
        return redirect()->back()->with('success', "تم إنشاء الفرع '$name' بنجاح");
    }

    public function updateRoot(Request $request, $id) {
        if(Gate::denies('تعديل او حذف مستوى حساب')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل مستويات حساب');
        }

        $root = Account::findOrFail($id);
        $old = $root->toArray();
        $name = $request->input('name');
        $root->update($request->all());
        $new = $root->toArray();

        logActivity('تعديل حساب', "تم تعديل الحساب '" . $name . "' في دليل الحسابات", $old, $new);
        return redirect()->back()->with('success', "تم تعديل المستوى '$name' بنجاح");
    }

    public function deleteRoot($id) {
        if(Gate::denies('تعديل او حذف مستوى حساب')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف مستويات حساب');
        }

        $root = Account::findOrFail($id);
        if($root->children()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المستوى لوجود مستويات فرعية مرتبطة به');
        }
        if($root->journalLines()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المستوى لوجود قيود مرتبطة به');
        }

        $old = $root->toArray();
        $name = $root->name;
        $root->delete();

        logActivity('حذف حساب', "تم حذف الحساب '" . $name . "' من دليل الحسابات", $old, null);
        return redirect()->back()->with('success', "تم حذف المستوى '$name' بنجاح");
    }

    public function entries(Request $request) {
        $company = Company::first();
        $accounts = Account::where('level', 5)->get();
        $vouchers = Voucher::all();
        $journals = JournalEntry::orderBy('code', 'desc')->get();

        $journalSearch = $request->query('journal_search');
        if($journalSearch) {
            $journals = $journals->filter(function($journal) use($journalSearch) {
                return str_contains((string)$journal->code, $journalSearch) || str_contains($journal->date, $journalSearch);
            });
        }

        if($request->query('journal_type') && $request->query('journal_type') != 'all') {
            if($request->query('journal_type') == 'all_journals') {
                $journals = $journals->filter(function($journal) {
                    return $journal->type == 'قيد يومية';
                });
            } elseif($request->query('journal_type') == 'all_receipts') {
                $journals = $journals->filter(function($journal) {
                    return str_starts_with($journal->type, 'سند قبض');
                });
            } elseif($request->query('journal_type') == 'all_payments') {
                $journals = $journals->filter(function($journal) {
                    return str_starts_with($journal->type, 'سند صرف');
                });
            }
        }

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
        
        if(request()->query('view') == 'سندات قبض') {
            $vouchers = $vouchers->filter(function($voucher) {
                return str_starts_with($voucher->type, 'سند قبض');
            });
        }
        elseif(request()->query('view') == 'سندات صرف') {
            $vouchers = $vouchers->filter(function($voucher) {
                return str_starts_with($voucher->type, 'سند صرف');
            });
        }
        elseif(request()->query('view') == 'الصندوق') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'سند قبض نقدي' || $voucher->type == 'سند صرف نقدي';
            });
        }

        if($request->query('voucher_type') && $request->query('voucher_type') != 'all') {
            $vouchers = $vouchers->filter(function($voucher) use($request) {
                return $voucher->type == $request->query('voucher_type');
            });
        }
        if($request->query('voucher_search')) {
            $vouchers = $vouchers->filter(function($voucher) use($request) {
                return str_contains((string)$voucher->code, $request->query('voucher_search')) || str_contains($voucher->date, $request->query('voucher_search'));
            });
        }
        
        return view('pages.accounting.entries', compact(
            'accounts', 
            'vouchers', 
            'balance', 
            'journals', 
            'balanceArray', 
            'company'
        ));
    }

    public function createJournal() {
        $accounts = Account::where('level', 5)->get();
        return view('pages.accounting.journal_entries.create_journal', compact('accounts'));
    }

    public function storeJournal(JournalRequest $request) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء قيود');
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($request->account_id as $index => $accountId) {
            if (!$accountId) {
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

        if($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments/journal_entries/' . $journalEntry->id, $fileName, 'public');

            $attachment = $journalEntry->attachments()->create([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'user_id'   => Auth::user()->id,
            ]);

            logActivity('إضافة مرفق', "تم إضافة مرفق جديد للقيد رقم " . $journalEntry->code, null, $attachment->toArray());
        }

        $new = $journalEntry->load('lines')->toArray();
        logActivity('إنشاء قيد', "تم إنشاء قيد جديد برقم " . $journalEntry->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء قيد جديد بنجاح, <a class="text-white fw-bold" href="'.route('journal.details', $journalEntry).'">عرض القيد</a>');
    }

    public function createClosingJournal() {
        $accounts = Account::where('level', 5)->get();
        return view('pages.accounting.journal_entries.closing_journal', compact('accounts'));
    }

    public function getClosingJournalData(Request $request) {
        $year = $request->query('year');
        
        if (!$year) {
            return response()->json(['error' => 'السنة مطلوبة'], 400);
        }

        $company_id = Auth::user()->company_id;
        
        // Get المصاريف (Expenses) parent account and all its children
        $expensesParent = Account::where('name', 'المصاريف')
            ->where('company_id', $company_id)
            ->first();
        
        // Get الإيرادات (Revenues) parent account and all its children  
        $revenuesParent = Account::where('name', 'الإيرادات')
            ->where('company_id', $company_id)
            ->first();

        $expenses = [];
        $revenues = [];

        if ($expensesParent) {
            $expenseAccountIds = $expensesParent->getAllChildrenIds();
            $expenseAccountIds[] = $expensesParent->id;

            // Get level 5 expense accounts with balances for the year
            $expenseAccounts = Account::whereIn('id', $expenseAccountIds)
                ->where('level', 5)
                ->get();

            foreach ($expenseAccounts as $account) {
                $balance = $this->getAccountBalanceForYear($account, $year);
                if ($balance > 0) {
                    $expenses[] = [
                        'account_id' => $account->id,
                        'account_name' => $account->name,
                        'account_code' => $account->code,
                        'balance' => number_format($balance, 2, '.', '')
                    ];
                }
            }
        }

        if ($revenuesParent) {
            $revenueAccountIds = $revenuesParent->getAllChildrenIds();
            $revenueAccountIds[] = $revenuesParent->id;

            // Get level 5 revenue accounts with balances for the year
            $revenueAccounts = Account::whereIn('id', $revenueAccountIds)
                ->where('level', 5)
                ->get();

            foreach ($revenueAccounts as $account) {
                $balance = $this->getAccountBalanceForYear($account, $year);
                if ($balance > 0) {
                    $revenues[] = [
                        'account_id' => $account->id,
                        'account_name' => $account->name,
                        'account_code' => $account->code,
                        'balance' => number_format($balance, 2, '.', '')
                    ];
                }
            }
        }

        // Calculate profit or loss
        $totalRevenues = array_sum(array_column($revenues, 'balance'));
        $totalExpenses = array_sum(array_column($expenses, 'balance'));
        $profitLoss = null;

        $difference = $totalRevenues - $totalExpenses;
        
        // Get retained earnings / profit & loss account (أرباح وخسائر محتجزة or similar)
        $profitLossAccount = Account::where('company_id', $company_id)
            ->where('level', 5)
            ->where(function($query) {
                $query->where('name', 'like', '%أرباح%')
                    ->orWhere('name', 'like', '%خسائر%')
                    ->orWhere('name', 'like', '%صافي الربح%');
            })
            ->first();

        if ($difference != 0 && $profitLossAccount) {
            $profitLoss = [
                'account_id' => $profitLossAccount->id,
                'account_name' => $profitLossAccount->name,
                'amount' => number_format(abs($difference), 2, '.', ''),
                'type' => $difference > 0 ? 'profit' : 'loss'
            ];
        }

        return response()->json([
            'expenses' => $expenses,
            'revenues' => $revenues,
            'profit_loss' => $profitLoss,
            'total_revenues' => number_format($totalRevenues, 2, '.', ''),
            'total_expenses' => number_format($totalExpenses, 2, '.', '')
        ]);
    }

    private function getAccountBalanceForYear(Account $account, $year) {
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        $result = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('journal', function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        // For expenses: balance = debit - credit (normally debit balance)
        // For revenues: balance = credit - debit (normally credit balance)
        $accountType = $account->type_id;
        
        // Expenses (type_id = 3) - debit balance
        if ($accountType == 3) {
            return max(0, $result->total_debit - $result->total_credit);
        }
        // Revenues (type_id = 4) - credit balance
        if ($accountType == 4) {
            return max(0, $result->total_credit - $result->total_debit);
        }

        return 0;
    }

    public function storeClosingJournal(Request $request) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء قيود');
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($request->account_id as $index => $accountId) {
            if (!$accountId) {
                return redirect()->back()->with('error', 'جميع الحسابات مطلوبة');
            }

            $debit = floatval($request->debit[$index] ?? 0);
            $credit = floatval($request->credit[$index] ?? 0);

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return redirect()->back()->with('error', 'مجموع المدين يجب أن يساوي مجموع الدائن');
        }

        $journalEntry = JournalEntry::create([
            'type' => 'قيد إقفال',
            'date' => $request->date,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
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

        $new = $journalEntry->load('lines')->toArray();
        logActivity('إنشاء قيد إقفال', "تم إنشاء قيد إقفال جديد برقم " . $journalEntry->code . " لسنة " . $request->year, null, $new);

        return redirect()->route('journal.details', $journalEntry)->with('success', 'تم إنشاء قيد الإقفال بنجاح');
    }

    public function editJournal(JournalEntry $journal) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل القيود');
        }

        $accounts = Account::where('level', 5)->get();

        return view('pages.accounting.journal_entries.edit_journal', compact('journal', 'accounts'));
    }

    public function updateJournal(Request $request, JournalEntry $journal) {
        $old = $journal->load('lines')->toArray();
        
        $journal->lines()->delete();

        $journal->update([
            'code' => $request->code,
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

        $new = $journal->load('lines')->toArray();
        logActivity('تعديل قيد', "تم تعديل القيد رقم " . $journal->code, $old, $new);

        return redirect()->back()->with('success', 'تم تعديل القيد بنجاح');
    }

    public function journalDetails(JournalEntry $journal) {
        return view('pages.accounting.journal_entries.journal_details', compact(
            'journal'
        ));
    }
    
    public function duplicateJournal(JournalEntry $journal) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتكرار القيود');
        }

        $newJournal = $journal->replicate();
        $newJournal->uuid = null;
        $newJournal->code = null;
        $newJournal->date = Carbon::now();
        $newJournal->modifier_id = null;
        $newJournal->save();

        foreach ($journal->lines as $line) {
            $newLine = $line->replicate();
            $newLine->journal_entry_id = $newJournal->id;
            $newLine->save();
        }

        $new = $newJournal->load('lines')->toArray();
        logActivity('تكرار قيد', "تم تكرار القيد رقم " . $journal->code . " إلى القيد رقم " . $newJournal->code, null, $new);

        return redirect()->route('journal.edit', $newJournal)->with('success', 'تم تكرار القيد بنجاح, يمكنك الآن تعديل البيانات حسب الحاجة.');
    }

    public function deleteJournal(JournalEntry $journal) {
        $old = $journal->load('lines')->toArray();

        $journal->lines()->delete();
        $journal->delete();
        
        logActivity('حذف قيد', "تم حذف القيد رقم " . $journal->code, $old, null);
        return redirect()->route('money.entries')->with('success', 'تم حذف القيد بنجاح');
    }

    public function attachFileToJournal(Request $request, JournalEntry $journal) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإضافة مرفقات للقيود');
        }

        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,xls,xlsx|max:5120', // 5MB max
        ]);

        $file = $request->file('attachment');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('attachments/journal_entries/' . $journal->id, $fileName, 'public');

        $attachment = $journal->attachments()->create([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $file->getClientMimeType(),
            'user_id'   => Auth::user()->id,
        ]);

        logActivity('إضافة مرفق', "تم إضافة مرفق جديد للقيد رقم " . $journal->code, null, $attachment->toArray());

        return redirect()->back()->with('success', 'تم إضافة المرفق بنجاح');
    }

    public function deleteJournalAttachment(Attachment $attachment) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف مرفقات القيود');
        }

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        logActivity('حذف مرفق', "تم حذف المرفق " . $attachment->file_name . " من القيد" . $attachment->attachable->code);

        return redirect()->back()->with('success', 'تم حذف المرفق بنجاح');
    }

    public function createPaymentVoucher() {
        $accounts = Account::where('level', 5)->with('customer')->get();
        return view('pages.accounting.vouchers.create_payment_voucher', compact('accounts'));
    }

    public function createReceiptVoucher() {
        $accounts = Account::where('level', 5)->with('customer')->get();
        return view('pages.accounting.vouchers.create_receipt_voucher', compact('accounts'));
    }

    public function storeVoucher(VoucherRequest $request) {
        // return $request;
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء سندات');
        }

        $validated = $request->validated();
        $new = Voucher::create($validated);

        if($request->invoice_id) {
            $invoice = Invoice::findOrFail($request->invoice_id);
            $invoice->isPaid = 'تم الدفع';
            $invoice->save();
        }

        logActivity('إنشاء سند', "تم إنشاء سند جديد برقم " . $new->code, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إنشاء سند جديد بنجاح');
    }

    public function voucherDetails(Voucher $voucher) {
        return view('pages.accounting.vouchers.voucher_details', compact('voucher'));
    }

    public function printVoucher(Voucher $voucher) {
        return view('pages.accounting.vouchers.printed_voucher', compact('voucher'));
    }

    public function deleteVoucher($id) {
        $voucher = Voucher::findOrFail($id);
        $old = $voucher->toArray();
        $voucher->delete();
        logActivity('حذف سند', "تم حذف السند رقم " . $voucher->code, $old, null);
        return redirect()->back()->with('success', 'تم حذف السند بنجاح');
    }

    public function postVoucherToJournal(Voucher $voucher) {
        if(Gate::denies('إنشاء قيود وسندات')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء قيود');
        }

        $journal = JournalEntry::create([
            'date' => Carbon::now(),
            'totalDebit' => $voucher->amount,
            'totalCredit' => $voucher->amount,
            'user_id' => Auth::user()->id,
            'voucher_id' => $voucher->id
        ]);

        $journal->lines()->createMany([
            [
                'account_id' => $voucher->debit_account_id,
                'debit' => $voucher->amount,
                'credit' => 0.00,
                'description' => $voucher->description
            ], 
            [
                'account_id' => $voucher->credit_account_id,
                'debit' => 0.00,
                'credit' => $voucher->amount,
                'description' => $voucher->description
            ]
        ]);

        $voucher->update([
            'is_posted' => true
        ]);

        $new = $journal->load('lines')->toArray();
        logActivity('ترحيل سند إلى قيد', "تم ترحيل السند رقم " . $voucher->code . " إلى القيد رقم " . $journal->code, null, $new);

        return redirect()->back()->with('success', 'تم ترحيل السند الى قيد بنجاح, <a class="text-white fw-bold" href="'.route('journal.details', $journal).'">عرض القيد</a>');
    }   

    public function reports(Request $request) {
        if(Gate::denies('تقارير القيود')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        $view = $request->query('view', 'تقارير القيود');
        
        if($view == 'تقارير القيود') {
            $type = $request->input('type');
            $from = $request->input('from');
            $to = $request->input('to');
            $entries = JournalEntry::orderBy('date')->get();

            if($request->query('journal_type') && $request->query('journal_type') != 'all') {
                if($request->query('journal_type') == 'all_journals') {
                    $entries = $entries->filter(function($journal) {
                        return $journal->type == 'قيد يومية';
                    });
                } elseif($request->query('journal_type') == 'all_receipts') {
                    $entries = $entries->filter(function($journal) {
                        return str_starts_with($journal->type, 'سند قبض');
                    });
                } elseif($request->query('journal_type') == 'all_payments') {
                    $entries = $entries->filter(function($journal) {
                        return str_starts_with($journal->type, 'سند صرف');
                    });
                }
            }

            if($from && $to) {
                $entries = $entries->whereBetween('date', [$from, $to]);
            }

            return view('pages.accounting.reports', compact('entries'));
        } elseif($view == 'كشف حساب') {
            $accounts = Account::where('level', 5)->get();
            $from = $request->input('from');
            $to = $request->input('to');
            $account = $request->input('account', null);

            $statement = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->select('journal_entry_lines.*')
                ->where('account_id', $account)
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

            return view('pages.accounting.reports', compact(
                'accounts',
                'statement',
                'opening_balance'
            ));
        } elseif($view == 'ميزان مراجعة') {
            $accounts = Account::whereIn('level', [1, 2, 3, 4])->get();
            if($request->query('type') && $request->query('type') != 'all') {
                $trialBalance = Account::where('name', $request->query('type'))
                    ->where('level', $request->query('type_level'))->get();
            } else {
                $trialBalance = Account::where('level', 1)->get();
            }
            
            return view('pages.accounting.reports', compact('accounts', 'trialBalance'));
        }
    }
}
