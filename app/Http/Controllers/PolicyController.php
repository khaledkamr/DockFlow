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
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function policies(Request $request) {
        $policies = Policy::orderBy('id', 'desc')->get();
        $customers = Customer::all();
        $policyFilter = request()->query('type');
        if ($policyFilter && $policyFilter !== 'all') {
            $policies = $policies->filter(function ($policy) use ($policyFilter) {
                return $policy->type === $policyFilter;
            });
        }
        $search = $request->input('search', null);
        if($search) {
            $policies = $policies->filter(function($policy) use($search) {
                return stripos($policy->id, $search) !== false 
                    || stripos($policy->customer->name, $search) !== false
                    || stripos($policy->date, $search) !== false;
            });
        }
        $policies = new \Illuminate\Pagination\LengthAwarePaginator(
            $policies->forPage(request()->get('page', 1), 50),
            $policies->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.policies.policies', compact('policies', 'customers'));
    }

    public function storagePolicy(Request $request) {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        return view('admin.policies.storagePolicy', compact('company', 'customers', 'containerTypes'));
    }

    public function storeStoragePolicy(PolicyRequest $request) {
        $policy_containers = [];
        foreach($request->containers as $container) {
            $container = Container::create([
                'customer_id' => $request->customer_id,
                'code' => $container['code'],
                'container_type_id' => $container['container_type_id'],
                'location' => $container['location'],
                'received_by' => $request->driver_name,
                'date' => Carbon::now(),
                'notes' => $container['notes']
            ]);
            $policy_containers[] = $container;
        }
        $count = count($request->containers);
        session()->flash('yard', 'تم إضافة ' . $count . ' حاويات جديدة للساحة بنجاح');
        $validated = $request->validated();
        $policy = Policy::create($validated);
        $policy->containers()->attach($policy_containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية جديدة بنجاح, <a class="text-white fw-bold" href="'.route('policies.storage.details', $policy->id).'">عرض الاتفاقية؟</a>');
    }
    
    public function createReceivePolicy() {
        $company = Company::first();
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        return view('admin.policies.receivePolicy', compact('company', 'customers'));
    }

    public function storeReceivePolicy(PolicyRequest $request) {
        $selected_containers = $request->selected_containers;
        $containers = [];
        foreach($selected_containers as $id) {
            $container = Container::findOrFail($id);
            $container->status = 'تم التسليم';
            $container->delivered_by = $request->driver_name;
            $container->exit_date = Carbon::now()->format('Y-m-d');
            $container->save();
            $containers[] = $container;
        }
        $validated = $request->validated();

        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء إتفاقية جديدة بنجاح, <a class="text-white fw-bold" href="'.route('policies.receive.details', $policy->id).'">عرض الاتفاقية؟</a>');
    }

    public function storagePolicyDetails($id) {
        $services = Service::all();
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
            
        return view('admin.policies.storagePolicyDetails', compact(
            'policy', 
            'storage_price', 
            'late_fee', 
            'tax',
            'services'
        ));
    }

    public function receivePolicyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        return view('admin.policies.receivePolicyDetails', compact('policy'));
    }
}
