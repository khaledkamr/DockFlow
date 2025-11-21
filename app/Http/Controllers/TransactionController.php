<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
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
        $transactions = Transaction::all();

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

        return view('pages.transactions.transactions', compact('transactions'));
    } 

    public function createTransaction() {
        $company = Auth::user()->company;
        $customers = Customer::all();
        $containerTypes = Container_type::all();

        return view('pages.transactions.createTransaction', compact(
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

        return redirect()->back()->with('success', 'تم إنشاء معاملة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('transactions.details', $transaction).'">عرض المعاملة؟</a>');
    }

    public function transactionDetails(Transaction $transaction) {
        $items = [
            [
                'name' => 'رسوم اذن التسليم - DELIVERY ORDER',
                'type' => 'مصروف'
            ],
            [   
                'name' => 'رسوم مواني - PORT CHARGE',
                'type' => 'مصروف'
            ],
            [
                'name' => 'رسوم الشركه السعوديه للمواني - SAUDI GLOBAL PORT FEES',
                'type' => 'مصروف'
            ],
            [
                'name' => 'اجور السكه الحديد - DRY PORT FEES',
                'type' => 'مصروف'
            ],
            [
                'name' => 'رسوم موعد فسح - APPOINTMENT FASAH',
                'type' => 'مصروف'
            ],
            [
                'name' => 'رسوم سابر - SABER FEES',
                'type' => 'مصروف'
            ],
            [
                'name' => 'رسوم جمركية - CUSTOMS FEES',
                'type' => 'مصروف'
            ],
            [
                'name' => 'ارضيات - DEMURRAGE CHARGE',
                'type' => 'مصروف'
            ],
            [
                'name' => 'اجور تخليص - CLEARANCE FEES',
                'type' => 'ايراد تخليص'
            ],
            [
                'name' => 'اجور نقل - TRANSPORT FEES',
                'type' => 'ايراد نقل'
            ],
            [
                'name' => 'اجور عمال - LABOUR',
                'type' => 'ايراد عمال'
            ],
            [
                'name' => 'خدمات سابر - SABER FEES',
                'type' => 'ايراد سابر'
            ],
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

        return view('pages.transactions.transactionDetails', compact('transaction', 'items', 'procedures'));
    }

    public function storeItem(ItemRequest $request) {
        if(Gate::denies('إضافة بند الى المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء بند في المعاملة');
        }

        $validated = $request->validated();
        TransactionItem::create($validated);

        return redirect()->back()->with('success', 'تم إضافة بند جديد للمعاملة');
    }

    public function updateItem(ItemRequest $request, TransactionItem $item) {
        if(Gate::denies('تعديل بند في المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بند في المعاملة');
        }

        $validated = $request->validated();
        $item->update($validated);

        return redirect()->back()->with('success', 'تم تعديل بيانات البند');
    }

    public function deleteItem(TransactionItem $item) {
        if(Gate::denies('حذف بند من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف بند من المعاملة');
        }

        $item->delete();
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

        return redirect()->back()->with('success', 'تم إضافة إجراء جديد للمعاملة');
    }

    public function deleteProcedure($procedureId) {
        if(Gate::denies('حذف إجراء من المعاملة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية حذف إجراء من المعاملة');
        }

        $procedure = Procedure::findOrFail($procedureId);
        $procedure->delete();

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
