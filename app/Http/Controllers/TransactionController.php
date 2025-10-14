<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transactions(Request $request) {
        $transactions = Transaction::all();

        $search = $request->input('search', null);
        if($search) {
            $transactions = $transactions->filter(function($transaction) use($search) {
                return stripos($transaction->code, $search) !== false 
                    || stripos($transaction->customer->name, $search) !== false
                    || stripos($transaction->date, $search) !== false;
            });
        }

        return view('pages.transactions.transactions', compact('transactions'));
    } 

    public function create() {
        $company = Auth::user()->company;
        $customers = Customer::all();
        $containerTypes = Container_type::all();

        return view('pages.transactions.createTransaction', compact(
            'company', 
            'customers', 
            'containerTypes'
        ));
    }

    public function store(TransactionRequest $request) {
        $transaction_containers = [];
        foreach($request->containers as $container) {
            $container = Container::create([
                'customer_id' => $request->customer_id,
                'code' => $container['code'],
                'container_type_id' => $container['container_type_id'],
                'date' => Carbon::now(),
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

    public function details(Transaction $transaction) {
        return view('pages.transactions.transactionDetails', compact('transaction'));
    }

    public function storeItem(ItemRequest $request) {
        $validated = $request->validated();
        TransactionItem::create($validated);

        return redirect()->back()->with('success', 'تم إضافة بند جديد للمعاملة');
    }

    public function updateItem(ItemRequest $request, TransactionItem $item) {
        $validated = $request->validated();
        $item->update($validated);

        return redirect()->back()->with('success', 'تم تعديل بيانات البند');
    }

    public function deleteItem(TransactionItem $item) {
        $item->delete();
        return redirect()->back()->with('success', 'تم حذف البند بنجاح');
    }
}
