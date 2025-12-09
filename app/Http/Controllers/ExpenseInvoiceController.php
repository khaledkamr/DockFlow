<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicNumberConverter;
use App\Http\Requests\ExpenseInvoiceRequest;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\CostCenter;
use App\Models\ExpenseInvoice;
use App\Models\JournalEntry;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseInvoiceController extends Controller
{
    public function invoices() {
        $expenseInvoices = ExpenseInvoice::orderBy('code', 'desc')->get();
        return view('pages.expense_invoices.invoices', compact('expenseInvoices'));
    }

    public function createInvoice() {
        $suppliers = Supplier::all();
        $accounts = Account::where('level', 5)->get();
        $costCenters = CostCenter::doesntHave('children')->get();
        $parentAccount = Account::where('name', 'النقدية والبنوك')->first();
        $expenseAccounts = $parentAccount ? $parentAccount->getLeafAccounts() : collect();
        $payment_methods = ['آجل', 'كاش', 'شيك', 'تحويل بنكي'];

        return view('pages.expense_invoices.create_invoices', compact(
            'suppliers', 
            'accounts', 
            'costCenters',
            'payment_methods',
            'expenseAccounts' 
        ));
    }

    public function storeInvoice(ExpenseInvoiceRequest $request) {
        $validated = $request->validated();
        if($validated['payment_method'] !== 'آجل') {
            $validated['is_paid'] = true;
        } else {
            $validated['is_paid'] = false;
        }

        $invoice = ExpenseInvoice::create($validated);

        foreach($request->items as $item) {
            $invoice->items()->create([
                'account_id' => $item['account_id'],
                'description' => $item['description'] ?? null,
                'cost_center_id' => $item['cost_center_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'amount' => $item['amount'],
                'tax' => $item['tax'],
                'total_amount' => $item['total_amount'],
            ]);
        }

        $invoice->fresh();
        $new = $invoice->load('items')->toArray();
        logActivity('إنشاء فاتورة مصاريف', 'تم إنشاء فاتورة مصاريف جديدة برقم ' . $invoice->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء فاتورة المصاريف بنجاح. <a href="' . route('expense.invoices.details', $invoice) . '" class="text-white fw-bold">عرض الفاتورة</a>');
    }

    public function invoiceDetails(ExpenseInvoice $invoice) {
        $invoice->load('items', 'attachments', 'supplier', 'expense_account');
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));
        $tax_account = Account::where('name', 'ضريبة القيمة المضافة من المصروفات')->where('level', 5)->first();
        return view('pages.expense_invoices.invoice_details', compact(
            'invoice', 
            'hatching_total',
            'tax_account' 
        ));
    }

    public function updateInvoiceStatus(Request $request, ExpenseInvoice $invoice) {
        $request->validate([
            'is_paid' => 'required|in:0,1',
        ]);

        $old = $invoice->toArray();
        $invoice->is_paid = $request->input('is_paid');
        $invoice->save();
        $invoice->fresh();
        $new = $invoice->toArray();
        logActivity('تحديث حالة فاتورة مصاريف', 'تم تحديث حالة فاتورة المصاريف برقم ' . $invoice->code, $old, $new);

        return redirect()->route('expense.invoices.details', $invoice)->with('success', 'تم تحديث حالة فاتورة المصاريف بنجاح');
    }

    public function updateInvoiceNotes(Request $request, ExpenseInvoice $invoice) {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $old = $invoice->toArray();
        $invoice->notes = $request->input('notes');
        $invoice->save();
        $invoice->fresh();
        $new = $invoice->toArray();
        logActivity('تحديث ملاحظات فاتورة مصاريف', 'تم تحديث ملاحظات فاتورة المصاريف برقم ' . $invoice->code, $old, $new);

        return redirect()->route('expense.invoices.details', $invoice)->with('success', 'تم تحديث ملاحظات فاتورة المصاريف بنجاح');
    }

    public function postInvoice(ExpenseInvoice $invoice) {
        if($invoice->is_posted) {
            return redirect()->back()->with('error', 'هذه الفاتورة تم ترحيلها مسبقاً');
        }

        $tax_account = Account::where('name', 'ضريبة القيمة المضافة من المصروفات')->where('level', 5)->first();
        if($invoice->tax > 0 && !$tax_account) {
            return redirect()->back()->with('error', 'لا يوجد حساب ضريبة القيمة المضافة من المصروفات. يرجى إنشاء الحساب أولاً');
        }

        $totalDebit = 0;
        $totalCredit = 0;

        $journal = JournalEntry::create([
            'date' => $invoice->date,
            'totalDebit' => 0,
            'totalCredit' => 0,
            'user_id' => Auth::user()->id,
        ]);

        foreach($invoice->items as $item) {
            $journal->lines()->create([
                'account_id' => $item->account_id,
                'debit' => $item->amount,
                'credit' => 0,
                'description' => 'بند فاتورة مصاريف رقم ' . $invoice->code,
            ]);
            $totalDebit += $item->amount;
        }

        if($invoice->tax > 0 && $tax_account) {
            $journal->lines()->create([
                'account_id' => $tax_account->id,
                'debit' => $invoice->tax,
                'credit' => 0,
                'description' => 'ضريبة قيمة مضافة على فاتورة مصاريف رقم ' . $invoice->code,
            ]);
            $totalDebit += $invoice->tax;
        }

        $journal->lines()->create([
            'account_id' => $invoice->supplier->account_id,
            'debit' => 0,
            'credit' => $invoice->total_amount,
            'description' => 'فاتورة مصاريف رقم ' . $invoice->code,
        ]);
        $totalCredit += $invoice->total_amount;

        if($invoice->payment_method !== 'آجل') {
            $journal->lines()->create([
                'account_id' => $invoice->supplier->account_id,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'description' => 'سداد فاتورة مصاريف رقم ' . $invoice->code,
            ]);
            $totalDebit += $invoice->total_amount;
            
            $journal->lines()->create([
                'account_id' => $invoice->expense_account_id,
                'debit' => 0,
                'credit' => $invoice->total_amount,
                'description' => 'سداد فاتورة مصاريف رقم ' . $invoice->code,
            ]);
            $totalCredit += $invoice->total_amount;
        }

        $journal->totalDebit = $totalDebit;
        $journal->totalCredit = $totalCredit;
        $journal->save();

        $invoice->is_posted = true;
        $invoice->save();

        logActivity('ترحيل فاتورة مصاريف', 'تم ترحيل فاتورة المصاريف برقم ' . $invoice->code . ' إلى قيد اليومية رقم ' . $journal->code);

        return redirect()->route('expense.invoices.details', $invoice)->with('success', "تم ترحيل فاتورة المصاريف بنجاح <a class='text-white fw-bold' href='".route('journal.details', $journal)."'>عرض القيد</a>");
    }

    public function deleteInvoice(ExpenseInvoice $invoice) {
        $invoice->delete();
        return redirect()->route('expense.invoices')->with('success', 'تم حذف فاتورة المصاريف بنجاح');
    }

    public function reports(Request $request) {
        $invoices = ExpenseInvoice::orderBy('code')->get();
        $suppliers = Supplier::all();
        $costCenters = CostCenter::doesntHave('children')->get();

        $supplier = $request->input('supplier', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $is_posted = $request->input('is_posted', 'all');
        $costCenter = $request->input('cost_center', 'all');

        if($supplier !== 'all') {
            $invoices = $invoices->where('supplier_id', $supplier);
        }
        if($from) {
            $invoices = $invoices->where('date', '>=', $from);
        }
        if($to) {
            $invoices = $invoices->where('date', '<=', $to);
        }
        if($is_posted !== 'all') {
            $invoices = $invoices->where('is_posted', $is_posted);
        }
        if($costCenter !== 'all') {
            $invoices = $invoices->filter(function($invoice) use ($costCenter) {
                foreach($invoice->items as $item) {
                    return $item->cost_center_id == $costCenter;
                }
            });
        }

        $perPage = $request->input('per_page', 100);
        $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $invoices->forPage(request()->get('page', 1), $perPage),
            $invoices->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.expense_invoices.reports', compact('invoices', 'suppliers', 'costCenters', 'perPage'));
    }

    public function attachFile(Request $request, ExpenseInvoice $invoice) {
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments/expense_invoices/' . $invoice->id, $fileName, 'public');
            
            $invoice->attachments()->create([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'user_id' => Auth::user()->id,
            ]);

            logActivity('إرفاق ملف بالفاتورة', "تم إرفاق مستند " . $fileName . " إلى فاتورة مصاريف رقم: " . $invoice->code);

            return redirect()->back()->with('success', 'تم إرفاق الملف بنجاح');
        }

        return redirect()->back()->with('error', 'لم يتم إرفاق أي ملف' );
    }

    public function deleteAttachment(Attachment $attachment) {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();

        logActivity('حذف ملف من الفاتورة', "تم حذف الملف " . $attachment->original_name . " من الفاتورة رقم " . $attachment->attachable->code);

        return redirect()->back()->with('success', 'تم حذف الملف بنجاح');
    }
}
