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

        $policyFilter = request()->query('type');
        if ($policyFilter && $policyFilter !== 'all') {
            $policies = $policies->filter(function ($policy) use ($policyFilter) {
                return $policy->type === $policyFilter;
            });
        }
        return view('admin.policies.policies', compact('policies', 'customers'));
    }

    public function storagePolicy(Request $request) {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        return view('admin.policies.storagePolicy', compact('company', 'customers'));
    }

    public function storeStoragePolicy(PolicyRequest $request) {
        $selected_containers = $request->selected_containers;
        $containers = [];
        foreach($selected_containers as $container) {
            $containers[] = Container::findOrFail($container);
        }
        $validated = $request->validated();
        
        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية تخزين جديدة بنجاح');
    }
    
    public function createReceivePolicy() {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        return view('admin.policies.receivePolicy', compact('company', 'customers'));
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

    public function storagePolicyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        if($policy->type == 'إستلام') {
            $storage_price = 0;
            $late_fee = 0;
            foreach($policy->containers as $container) {
                $days = Carbon::parse($container->date)->diffInDays(Carbon::parse($policy->contract->storage_date));
                $storage_price += $policy->contract->container_storage_price * (int) $days;
            }
            $late_fee += $days > $policy->contract->container_storage_period ? $policy->late_fee * ( (int) $days - $policy->contract->container_storage_period) : 0;
            $tax = 'غير معفي';
        } else {
            $storage_price = $policy->storage_price;
            $late_fee = $policy->late_fee;
            $tax = $policy->tax;
        }
            
        return view('admin.policies.storagePolicyDetails', compact('policy', 'storage_price', 'late_fee', 'tax'));
    }

    public function receivePolicyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        foreach($policy->containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($policy->date));
            $container->storage_price = $policy->contract->container_storage_price;
            if($container->period > $policy->contract->container_storage_period) {
                $days = (int) Carbon::parse($container->date)->addDays($policy->contract->container_storage_period)->diffInDays(Carbon::parse($policy->date));
                $container->late_days = $days;
                $container->late_fee = $days * $policy->contract->late_fee;
            } else {
                $container->late_fee = 0;
            }
            $container->total = $container->storage_price + $container->late_fee;
        }
            
        return view('admin.policies.receivePolicyDetails', compact('policy'));
    }
}
