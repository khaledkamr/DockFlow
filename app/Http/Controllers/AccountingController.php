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
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
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

        logActivity('إنشاء سند', "تم إنشاء سند جديد برقم " . $new->code, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إنشاء سند جديد بنجاح');
    }

    public function deleteVoucher($id) {
        $voucher = Voucher::findOrFail($id);
        $old = $voucher->toArray();
        $voucher->delete();
        logActivity('حذف سند', "تم حذف السند رقم " . $voucher->code, $old, null);
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

        $new = $journal->load('lines')->toArray();
        logActivity('ترحيل سند إلى قيد', "تم ترحيل السند رقم " . $voucher->code . " إلى القيد رقم " . $journal->code, null, $new);

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
