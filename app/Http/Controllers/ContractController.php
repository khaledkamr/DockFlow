<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function contracts() {
        $contracts = Contract::all();
        return view('admin.contracts', compact('contracts'));
    }

    public function createContract() {
        $company = Company::first();
        $customers = Customer::all();
        return view('admin.createContract', compact('company', 'customers'));
    }

    public function storeContract(ContractRequest $request) {
        $validated = $request->validated();
        Contract::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء العقد بنجاح');
    }

    public function contractDetails($id) {
        $contract = Contract::findOrFail($id);
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        // return $contract;
        return view('admin.contractDetails', compact('contract', 'months', 'days'));
    }
}
