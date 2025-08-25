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

    public function storagePolicy(Request $request) {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        return view('admin.storagePolicy', compact('company', 'customers'));
    }

    public function storeStoragePolicy(PolicyRequest $request) {
        $validated = $request->validated();
        $containers = Container::where('customer_id', $validated['customer_id'])
            ->where('status', 'في الإنتظار')->get();
        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية تخزين جديدة بنجاح');
    }
    
    public function createReceivePolicy() {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        return view('admin.receivePolicy', compact('company', 'customers'));
    }

    public function storeReceivePolicy(PolicyRequest $request) {
        $selected_containers = $request->selected_containers;
        $containers = [];
        foreach($selected_containers as $container) {
            $containers[] = Container::findOrFail($container);
        }
        $validated = $request->validated();

        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية إستلام جديدة بنجاح');
    }

    public function policyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        if($policy->type == 'إستلام') {
            $storage_price = 0;
            foreach($policy->containers as $container) {
                $days = Carbon::parse($container->created_at)->diffInDays(Carbon::parse($policy->contract->storage_date));
                $storage_price += $policy->contract->container_storage_price * $days;
            }
            $late_fee = $days > $policy->contract->late_fee_period ? $policy->late_fee * ($days - $policy->contract->late_fee_period) : 0;
            $tax = 'غير معفي';
        } else {
            $storage_price = $policy->storage_price;
            $late_fee = $policy->late_fee;
            $tax = $policy->tax;
        }
            
        return view('admin.policyDetails', compact('policy', 'storage_price', 'late_fee', 'tax'));
    }
}
