<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\Procedure;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function transactions(Request $request) {
        $transactions = Transaction::orderBy('code', 'desc')->get();

        $search = $request->input('search', null);
        if ($search) {
            $transactions = $transactions->filter(function ($transaction) use ($search) {
                $matchCode = stripos($transaction->code, $search) !== false;
                $matchCustomer = stripos($transaction->customer->name, $search) !== false;
                $matchDate = stripos($transaction->date, $search) !== false;
                $matchCustomsDeclaration = stripos($transaction->customs_declaration, $search) !== false;
                $matchPolicyNumber = stripos($transaction->policy_number, $search) !== false;
                $matchContainer = $transaction->containers->contains(function ($container) use ($search) {
                    return stripos($container->code, $search) !== false;
                });

                return $matchCode || $matchCustomer || $matchDate || $matchCustomsDeclaration || $matchPolicyNumber || $matchContainer;
            });
        }

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage(request()->get('page', 1), 100),
            $transactions->count(),
            100,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        $transaction->update([
            'customer_id' => $validated['customer_id'],
            'policy_number' => $validated['policy_number'],
            'customs_declaration' => $validated['customs_declaration'],
            'customs_declaration_date' => $validated['customs_declaration_date'],
            'status' => $validated['status'],
        ]);

        $new = $transaction->toArray();
        logActivity('تعديل معاملة تخليص', "تم تعديل بيانات المعاملة برقم " . $transaction->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات المعاملة بنجاح');
    }

    public function transactionDetails(Transaction $transaction) {
        $customers = Customer::all();
        $creditAccounts = Account::where('level', 5)->get();

        $items = [
            [
                'name' => 'رسوم اذن التسليم - DELIVERY ORDER',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم اذن التسليم - تحت التسوية')->first()->id ?? null,
            ],
            [   
                'name' => 'رسوم مواني - PORT CHARGE',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم مواني - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'رسوم الشركه السعوديه للمواني - SAUDI GLOBAL PORT FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم الشركه السعوديه للمواني - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'اجور السكه الحديد - DRY PORT FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'اجور السكه الحديد - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'رسوم موعد فسح - APPOINTMENT FASAH',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم موعد فسح - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'رسوم سابر - SABER FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم سابر - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'رسوم جمركية - CUSTOMS FEES',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'رسوم جمركية - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'ارضيات - DEMURRAGE CHARGE',
                'type' => 'مصروف',
                'debit_account_id' => Account::where('name', 'ارضيات - تحت التسوية')->first()->id ?? null,
            ],
            [
                'name' => 'اجور تخليص - CLEARANCE FEES',
                'type' => 'ايراد تخليص',
                'debit_account_id' => null,
            ],
            [
                'name' => 'اجور نقل - TRANSPORT FEES',
                'type' => 'ايراد نقل',
                'debit_account_id' => null,
            ],
            [
                'name' => 'اجور عمال - LABOUR',
                'type' => 'ايراد عمال',
                'debit_account_id' => null,
            ],
            [
                'name' => 'خدمات سابر - SABER FEES',
                'type' => 'ايراد سابر',
                'debit_account_id' => null,
            ],
            [
                'name' => 'رسوم تخزين - STORAGE FEES',
                'type' => 'ايراد تخزين',
                'debit_account_id' => null,
            ]
        ];

        $procedures = [
            ['name' => 'تم فتح معامله - Open Transaction'],
            ['name' => 'تم استلام اذن التسليم - The delivery order has been received'],
            ['name' => 'تم طباعة البيان - The import declaration has been received'],
            ['name' => 'تم السداد على معامله - Payment is made'],
            ['name' => 'لم تصل الباخرة - The steamer did not arrive'],
            ['name' => 'مطلوب تفويض - Authorization required'],
            ['name' => 'مطلوب فسح للمواد المقيدة - Approval Required'],
            ['name' => 'تم عمل اشعار نقل - Create transport notification'],
            ['name' => 'تم عمل فاتورة - Create invoice'],
            ['name' => 'لم تفرغ الحاوية فى الايداع - The container was not emptied at the deposit'],
        ];

        return view('pages.transactions.transaction_details', compact(
            'transaction', 
            'items', 
            'procedures',
            'customers',
            'creditAccounts'
        ));
    }

    public function storeItem(ItemRequest $request) {
        if(Gate::denies('إضافة بند الى المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء بند في المعاملة');
        }

        $validated = $request->validated();
        $new = TransactionItem::create($validated);
        logActivity('إنشاء بند في المعاملة', "تم إضافة بند جديد للمعاملة رقم " . $new->transaction->code, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إضافة بند جديد للمعاملة');
    }

    public function updateItem(ItemRequest $request, TransactionItem $item) {
        if(Gate::denies('تعديل بند في المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بند في المعاملة');
        }

        $old = $item->toArray();
        $validated = $request->validated();
        $item->update($validated);
        $new = $item->toArray();
        logActivity('تعديل بند في المعاملة', "تم تعديل بند في المعاملة رقم " . $item->transaction->code, $old, $new);

        return redirect()->back()->with('success', 'تم تعديل بيانات البند');
    }

    public function postItem(TransactionItem $item) {
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

    public function deleteItem(TransactionItem $item) {
        if(Gate::denies('حذف بند من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف بند من المعاملة');
        }
        $old = $item->toArray();
        $item->delete();
        logActivity('حذف بند من المعاملة', "تم حذف بند من المعاملة رقم " . $item->transaction->code, $old, null);
        return redirect()->back()->with('success', 'تم حذف البند بنجاح');
    }

    public function addProcedure(Request $request, Transaction $transaction) {
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

    public function deleteProcedure($procedureId) {
        if(Gate::denies('حذف إجراء من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف إجراء من المعاملة');
        }

        $procedure = Procedure::findOrFail($procedureId);
        $old = $procedure->toArray();
        $procedure->delete();
        logActivity('حذف إجراء من المعاملة', "تم حذف إجراء من المعاملة رقم " . $procedure->transaction->code . ": " . $procedure->name, $old, null);

        return redirect()->back()->with('success', 'تم حذف الإجراء بنجاح');
    }
    
    public function reports(Request $request) {
        $transactions = Transaction::orderBy('code', 'asc')->get();
        $customers = Customer::all();

        $customer_id = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');

        if($customer_id !== 'all') {
            $transactions = $transactions->where('customer_id', $customer_id);
        }
        if($from) {
            $transactions = $transactions->where('date', '>=', $from);
        }
        if($to) {
            $transactions = $transactions->where('date', '<=', $to);
        }
        if($status !== 'all') {
            $transactions = $transactions->where('status', $status);
        }
        if($invoice_status !== 'all') {
            if($invoice_status == 'with_invoice') {
                $transactions = $transactions->filter(function($transaction) {
                    return !$transaction->containers->every(function($container) {
                        return $container->invoices->where('type', 'تخليص')->isEmpty();
                    });
                });
            } elseif($invoice_status == 'without_invoice') {
                $transactions = $transactions->filter(function($transaction) {
                    return $transaction->containers->every(function($container) {
                        return $container->invoices->where('type', 'تخليص')->isEmpty();
                    });
                });
            }
        }

        $perPage = $request->input('per_page', 100);
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage(request()->get('page', 1), $perPage),
            $transactions->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.transactions.reports', compact(
            'transactions',
            'customers',
            'perPage'
        ));
    }
}
