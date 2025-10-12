<?php

namespace App\Http\Controllers;

use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transactions(Request $request) {
        $transactions = Transaction::all();

        $search = $request->input('search', null);
        if($search) {
            $transactions = $transactions->filter(function($transaction) use($search) {
                return stripos($transaction->id, $search) !== false 
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

    public function store(Request $request) {
        return $request;
    }
}
