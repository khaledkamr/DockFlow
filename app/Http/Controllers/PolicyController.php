<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContractRequest;
use App\Http\Requests\PolicyRequest;
use App\Models\Company;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\User;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function policies() {
        $policies = Policy::orderBy('id', 'desc')->get();
        $customers = Customer::all();
        return view('admin.policies', compact('policies', 'customers'));
    }

    public function createPolicy(Request $request) {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        // return $customers;

        return view('admin.createPolicy', compact('company', 'customers'));
    }

    public function storePolicy(PolicyRequest $request) {
        $validated = $request->validated();
        
        $containers = Container::where('customer_id', $validated['customer_id'])
        ->where('status', 'في الإنتظار')->get();

        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية جديدة بنجاح');
    }

    public function policyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        return view('admin.policyDetails', compact('policy'));
    }
}
