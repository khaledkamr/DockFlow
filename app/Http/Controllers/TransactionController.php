<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\Procedure;
use App\Models\TransactionProcedure;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function transactions(Request $request) {
        $transactions = Transaction::query();

        $search = $request->input('search', null);

        if ($search) {
            $transactions->where('code', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('customs_declaration', 'like', '%' . $search . '%')
                ->orWhere('policy_number', 'like', '%' . $search . '%')
                ->orWhereHas('containers', function ($query) use ($search) {
                    $query->where('code', 'like', '%' . $search . '%');
                })
                ->orWhereDate('date', 'like', '%' . $search . '%');
        }

        $transactions = $transactions->with(['customer', 'containers', 'items'])->orderBy('code', 'desc')->paginate(100)->withQueryString();

        return view('pages.transactions.transactions', compact('transactions'));
    } 

    public function createTransaction() {
        $company = Auth::user()->company;
        $customers = Customer::all();
        $containerTypes = Container_type::all();

        return view('pages.transactions.create_transaction', compact(
            'company', 
            'customers', 
            'containerTypes'
        ));
    }

    public function storeTransaction(TransactionRequest $request) {
        $transaction_containers = [];
        foreach($request->containers as $container) {
            $container = Container::create([
                'customer_id' => $request->customer_id,
                'code' => $container['code'],
                'status' => 'في الميناء',
                'container_type_id' => $container['container_type_id'],
                'notes' => $container['notes'],
                'user_id' => Auth::user()->id,
            ]);
            $transaction_containers[] = $container;
        }

        $validated = $request->validated();
        $transaction = Transaction::create($validated);
        $transaction->containers()->attach($transaction_containers);

        $new = $transaction->load('containers')->toArray();
        logActivity('إنشاء معاملة تخليص', "تم إنشاء معاملة تخليص جديدة برقم " . $transaction->code . "للعميل " . $transaction->customer->name, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء معاملة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('transactions.details', $transaction).'">عرض المعاملة؟</a>');
    }

    public function updateTransaction(Request $request, Transaction $transaction) {
        if(Gate::denies('تعديل معاملة تخليص')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات المعاملة');
        }
        if($request->customer_id != $transaction->customer_id && $transaction->containers->first()->invoices && $transaction->containers->first()->invoices->where('type', 'تخليص')->first()) {
            return redirect()->back()->with('error', 'لا يمكن تغيير العميل للمعاملة بعد إصدار فاتورة لها');
        }
        
        $old = $transaction->toArray();

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'policy_number' => 'required|string',
            'customs_declaration' => 'nullable|string',
            'customs_declaration_date' => 'nullable|date',
            'status' => 'required',
        ]);

        $transaction->update($validated);

        foreach($transaction->containers as $container) {
            $container->customer_id = $validated['customer_id'];
            $container->save();
        }

        $new = $transaction->toArray();
        logActivity('تعديل معاملة تخليص', "تم تعديل بيانات المعاملة برقم " . $transaction->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات المعاملة بنجاح');
    }

    public function transactionDetails(Transaction $transaction) {
        $customers = Customer::all();
        $creditAccounts = Account::where('level', 5)->get();

        $items = Item::with('debitAccount')->orderBy('type', 'desc')->orderBy('id')->get();

        $procedures = Procedure::all();

        return view('pages.transactions.transaction_details', compact(
            'transaction', 
            'items', 
            'procedures',
            'customers',
            'creditAccounts'
        ));
    }

    public function deleteTransaction(Transaction $transaction) {
        if($transaction->containers->first()->invoices->where('type', 'تخليص')->first()) {
            return redirect()->back()->with('error', 'لا يمكن حذف معاملة تم إصدار فاتورة لها');
        }

        foreach($transaction->containers as $container) {
            $container->delete();
        }

        $old = $transaction->toArray();
        $transaction->items()->delete();
        $transaction->procedures()->delete();
        $transaction->delete();
        logActivity('حذف معاملة تخليص', "تم حذف معاملة تخليص برقم " . $transaction->code, $old, null);

        return redirect()->route('transactions')->with('success', 'تم حذف المعاملة بنجاح');
    }

    public function addTransactionItem(ItemRequest $request) {
        if(Gate::denies('إضافة بند الى المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء بند في المعاملة');
        }

        $validated = $request->validated();
        $new = TransactionItem::create($validated);
        logActivity('إنشاء بند في المعاملة', "تم إضافة بند جديد للمعاملة رقم " . $new->transaction->code, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إضافة بند جديد للمعاملة');
    }

    public function updateTransactionItem(ItemRequest $request, TransactionItem $item) {
        if(Gate::denies('تعديل بند في المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بند في المعاملة');
        }
        if($item->transaction->containers->first()->invoices->where('type', 'تخليص')->first()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل بند من معاملة تم إصدار فاتورة لها');
        }
        if($item->is_posted) {
            return redirect()->back()->with('error', 'لا يمكن تعديل بند تم ترحيله مسبقاً');
        }

        $old = $item->toArray();
        $validated = $request->validated();
        $item->update($validated);
        $new = $item->toArray();
        logActivity('تعديل بند في المعاملة', "تم تعديل بند في المعاملة رقم " . $item->transaction->code, $old, $new);

        return redirect()->back()->with('success', 'تم تعديل بيانات البند');
    }

    public function postTransactionItem(TransactionItem $item) {
        if($item->debit_account_id == null) {
            return redirect()->back()->with('error', 'الحساب المدين للبند غير موجود, يرجى تعيين حساب مدين صالح للبند قبل الترحيل');
        }
        if($item->credit_account_id == null) {
            return redirect()->back()->with('error', 'الحساب الدائن للبند غير موجود, يرجى تعيين حساب دائن صالح للبند قبل الترحيل');
        }
        if($item->is_posted) {
            return redirect()->back()->with('error', 'هذا البند تم ترحيله مسبقاً');
        }

        $journal = JournalEntry::create([
            'date' => Carbon::now(),
            'totalDebit' => $item->amount,
            'totalCredit' => $item->amount,
            'user_id' => Auth::user()->id,
        ]);

        $itemDescription = explode(' - ', $item->description)[0];

        $journal->lines()->createMany([
            [
                'account_id' => $item->debit_account_id,
                'debit' => $item->amount,
                'credit' => 0,
                'description' => 'مصروف ' . $itemDescription . ' من معاملة ' . $item->transaction->code,
            ],
            [
                'account_id' => $item->credit_account_id,
                'debit' => 0,
                'credit' => $item->amount,
                'description' => 'صرف ' . $itemDescription . ' من معاملة ' . $item->transaction->code,
            ],
        ]);

        $item->is_posted = true;
        $item->save();

        logActivity('ترحيل بند في المعاملة', "تم ترحيل بند في المعاملة رقم " . $item->transaction->code, null, $item->toArray());

        return redirect()->back()->with('success', 'تم ترحيل البند الى قيد بنجاح, <a class="text-white fw-bold" href="'.route('journal.details', $journal).'">عرض القيد</a>');
    }

    public function deleteTransactionItem(TransactionItem $item) {
        if(Gate::denies('حذف بند من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف بند من المعاملة');
        }
        if($item->transaction->containers->first()->invoices->where('type', 'تخليص')->first()) {
            return redirect()->back()->with('error', 'لا يمكن حذف بند من معاملة تم إصدار فاتورة لها');
        }
        if($item->is_posted) {
            return redirect()->back()->with('error', 'لا يمكن حذف بند تم ترحيله مسبقاً');
        }

        $old = $item->toArray();
        $item->delete();
        logActivity('حذف بند من المعاملة', "تم حذف بند من المعاملة رقم " . $item->transaction->code, $old, null);
        return redirect()->back()->with('success', 'تم حذف البند بنجاح');
    }

    public function addTransactionProcedure(Request $request, Transaction $transaction) {
        if(Gate::denies('إضافة إجراء للمعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إضافة إجراء إلى المعاملة');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $transaction->procedures()->create([
            'name' => $request->name,
        ]);

        logActivity('إضافة إجراء للمعاملة', "تم إضافة إجراء جديد للمعاملة رقم " . $transaction->code . ": " . $request->name);

        return redirect()->back()->with('success', 'تم إضافة إجراء جديد للمعاملة');
    }

    public function deleteTransactionProcedure($procedureId) {
        if(Gate::denies('حذف إجراء من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف إجراء من المعاملة');
        }

        $procedure = TransactionProcedure::findOrFail($procedureId);
        $old = $procedure->toArray();
        $procedure->delete();
        logActivity('حذف إجراء من المعاملة', "تم حذف إجراء من المعاملة رقم " . $procedure->transaction->code . ": " . $procedure->name, $old, null);

        return redirect()->back()->with('success', 'تم حذف الإجراء بنجاح');
    }

    public function itemsAndProcedures() {
        $items = Item::with('debitAccount')->get();
        $procedures = Procedure::all();
        $accounts = Account::where('level', 5)->get();

        return view('pages.transactions.items_and_procedures', compact(
            'items',
            'procedures',
            'accounts', 
        ));
    }

    public function storeItem(Request $request) {
        if(Gate::denies('إنشاء بند جديد')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء بند جديد');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'debit_account_id' => 'nullable|exists:accounts,id',
        ]);

        Item::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء بند جديد بنجاح');
    }

    public function updateItem(Request $request, Item $item) {
        if(Gate::denies('تعديل بند')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بند');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'debit_account_id' => 'nullable|exists:accounts,id',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات البند بنجاح');
    }

    public function deleteItem(Item $item) {
        if(Gate::denies('حذف بند')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف بند');
        }

        $item->delete();
        return redirect()->back()->with('success', 'تم حذف البند بنجاح');
    }

    public function storeProcedure(Request $request) {
        if(Gate::denies('إنشاء إجراء جديد')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء إجراء جديد');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Procedure::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء إجراء جديد بنجاح');
    }

    public function updateProcedure(Request $request, Procedure $procedure) {
        if(Gate::denies('تعديل إجراء')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل إجراء');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $procedure->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات الإجراء بنجاح');
    }

    public function deleteProcedure(Procedure $procedure) {
        if(Gate::denies('حذف إجراء')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف إجراء');
        }
        
        $procedure->delete();
        return redirect()->back()->with('success', 'تم حذف الإجراء بنجاح');
    }
    
    public function reports(Request $request) {
        $transactions = Transaction::query();
        $customers = Customer::all();

        $customer_id = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');
        $perPage = $request->input('per_page', 100);

        if($customer_id !== 'all') {
            $transactions->where('customer_id', $customer_id);
        }
        if($from) {
            $transactions->where('date', '>=', $from);
        }
        if($to) {
            $transactions->where('date', '<=', $to);
        }
        if($status !== 'all') {
            $transactions->where('status', $status);
        }
        if($invoice_status !== 'all') {
            if($invoice_status == 'with_invoice') {
                $transactions->whereHas('containers.invoices', function($query) {
                    $query->where('type', 'تخليص');
                });
            } elseif($invoice_status == 'without_invoice') {
                $transactions->whereDoesntHave('containers.invoices', function($query) {
                    $query->where('type', 'تخليص');
                });
            }
        }

        $transactions = $transactions->with(['customer', 'containers', 'items'])->orderBy('code')->paginate(100)->withQueryString();

        return view('pages.transactions.reports', compact(
            'transactions',
            'customers',
            'perPage'
        ));
    }
}
