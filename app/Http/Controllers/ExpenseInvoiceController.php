<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicNumberConverter;
use App\Http\Requests\ExpenseInvoiceRequest;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\ExpenseInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseInvoiceController extends Controller
{
    public function invoices() {
        $expenseInvoices = ExpenseInvoice::all();
        return view('pages.expense_invoices.invoices', compact('expenseInvoices'));
    }

    public function createInvoice() {
        $suppliers = Supplier::all();
        $accounts = Account::where('level', 5)->get();
        $costCenters = Account::where('level', 5)->get();
        $payment_methods = ['آجل', 'كاش', 'شيك', 'تحويل بنكي'];

        return view('pages.expense_invoices.create_invoices', compact(
            'suppliers', 
            'accounts', 
            'costCenters',
            'payment_methods'    
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
        $hatching_total = ArabicNumberConverter::numberToArabicMoney(number_format($invoice->total_amount, 2));
        return view('pages.expense_invoices.invoice_details', compact('invoice', 'hatching_total'));
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

    public function deleteInvoice(ExpenseInvoice $invoice) {
        $invoice->delete();
        return redirect()->route('expense.invoices')->with('success', 'تم حذف فاتورة المصاريف بنجاح');
    }

    public function reports(Request $request) {
        $invoices = ExpenseInvoice::all();
        $suppliers = Supplier::all();
        $costCenters = Account::where('level', 5)->get();

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
            $invoices = $invoices->where('cost_center_id', $costCenter);
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
