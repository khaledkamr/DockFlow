<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContractRequest;
use App\Http\Requests\PolicyRequest;
use App\Models\Account;
use App\Models\Company;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PolicyController extends Controller
{
    public function policies(Request $request) {
        $policies = Policy::query();
        $customers = Customer::all();
        $policyFilter = request()->query('type');
        $search = $request->input('search', null);

        if ($policyFilter && $policyFilter !== 'all') {
            $policies->where('type', $policyFilter);
        }
        if($search) {
            $policies->where(function($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        $policies = $policies->with(['customer', 'made_by'])->orderBy('date', 'desc')->orderBy('code', 'desc')->paginate(100)->withQueryString();

        return view('pages.policies.policies', compact('policies', 'customers'));
    }

    public function storagePolicy(Request $request) {
        if(Gate::denies('إنشاء اتفاقية')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        $company = Company::first();
        $customers = Customer::with('contract.services')->orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        $containers = Container::where('date', null)->get();

        return view('pages.policies.storage_policy', compact(
            'company', 
            'customers', 
            'containerTypes', 
            'containers'
        ));
    }

    public function storeStoragePolicy(PolicyRequest $request) {
        $policy_containers = [];
        foreach($request->containers as $container) {
            $existContainer = Container::where('code', $container['code'])->first();
            if($existContainer && $existContainer->status == 'في الساحة') {
                return redirect()->back()->with('error', 'الحاوية موجوده في الساحة بالفعل');
            }

            if(isset($container['id']) && $container['id'] != '') {
                $existingContainer = Container::findOrFail($container['id']);
                $existingContainer->status = 'في الساحة';
                $existingContainer->location = $container['location'];
                $existingContainer->received_by = $request->driver_name;
                $existingContainer->date = Carbon::now();
                $existingContainer->save();
                $policy_containers[] = $existingContainer;
                continue;
            }
            $container = Container::create([
                'customer_id' => $request->customer_id,
                'code' => $container['code'],
                'status' => 'في الساحة',
                'container_type_id' => $container['container_type_id'],
                'location' => $container['location'],
                'received_by' => $request->driver_name,
                'date' => Carbon::now(),
                'notes' => $container['notes'],
                'user_id' => Auth::user()->id,
            ]);

            $policy_containers[] = $container;
        }

        $validated = $request->validated();
        $policy = Policy::create($validated);
        $policy->containers()->attach($policy_containers);

        $new = $policy->load('containers')->toArray();
        logActivity('إنشاء بوليصة تخزين', "تم إنشاء بوليصة تخزين جديدة برقم " . $policy->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء بوليصة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('policies.storage.details', $policy).'">عرض البوليصة؟</a>');
    }

    public function updateStoragePolicy(Request $request, Policy $policy) {
        $old = $policy->toArray();

        $validated = $request->validate([
            'date' => 'required|date',
            'tax_statement' => 'nullable',
            'customer_id' => 'required',
            'driver_name' => 'required',
            'driver_NID' => 'required',
            'car_code' => 'required',
            'storage_price' => 'nullable|numeric',
            'storage_duration' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
        ]);

        if($request->date != $policy->date) {
            foreach($policy->containers as $container) {
                $container->date = $request->date;
                $container->save();
            }
        }

        $policy->update([
            'date' => $validated['date'],
            'tax_statement' => $validated['tax_statement'],
            'customer_id' => $validated['customer_id'],
            'driver_name' => $validated['driver_name'],
            'driver_NID' => $validated['driver_NID'],
            'car_code' => $validated['car_code'],
            'storage_price' => $validated['storage_price'],
            'storage_duration' => $validated['storage_duration'],
            'late_fee' => $validated['late_fee'],
        ]);
        

        $new = $policy->toArray();
        logActivity('تعديل بوليصة تخزين', 'تم تعديل بيانات بوليصة التخزين رقم ' . $policy->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات البوليصة بنجاح');
    }

    public function storagePolicyDetails(Policy $policy) {
        $services = Service::all();
        $customers = Customer::all();

        return view('pages.policies.storage_policy_details', compact(
            'policy',
            'services',
            'customers',
        ));
    }
    
    public function createReceivePolicy() {
        if(Gate::denies('إنشاء اتفاقية')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        $company = Auth::user()->company;
        $customers = Customer::with('contract')->orderBy('name', 'asc')->get();
        
        return view('pages.policies.receive_policy', compact('company', 'customers'));
    }

    public function storeReceivePolicy(PolicyRequest $request) {
        $selected_containers = $request->selected_containers;
        $containers = [];
        
        foreach($selected_containers as $id) {
            $container = Container::findOrFail($id);
            $container->status = 'تم التسليم';
            $container->delivered_by = $request->driver_name;
            $container->exit_date = Carbon::now();
            $container->save();
            $containers[] = $container;
        }

        $validated = $request->validated();
        $policy = Policy::create($validated);
        $policy->containers()->attach($containers);

        $new = $policy->load('containers')->toArray();
        logActivity('إنشاء بوليصة استلام', "تم إنشاء بوليصة استلام جديدة برقم " . $policy->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء بوليصة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('policies.receive.details', $policy).'">عرض البوليصة؟</a>');
    }

    public function receivePolicyDetails(Policy $policy) {
        return view('pages.policies.receive_policy_details', compact('policy'));
    }

    public function servicePolicy() {
        if(Gate::denies('إنشاء اتفاقية')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        $company = Company::first();
        $customers = Customer::with('contract.services')->orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        $services = Service::all();

        return view('pages.policies.service_policy', compact('company', 'customers', 'containerTypes', 'services'));
    }

    public function storeServicePolicy(Request $request) {
        $validated = $request->validate([
            'date' => 'required|date',
            'company_id' => 'required',
            'user_id' => 'required',
            'type' => 'required',
            'driver_name' => 'required',
            'driver_NID' => 'required',
            'driver_car' => 'required',
            'car_code' => 'required',
            'driver_phone' => 'nullable|string',
            'tax_statement' => 'nullable|string',
        ]);

        if(!$request->customer_id) {
            $accountId = Account::where('name', 'عملاء التشغيل')->first()->id;
            $lastCustomer = Account::where('parent_id', $accountId)->latest('id')->first();
            if($lastCustomer) {
                $code = $lastCustomer->code + 1;
            } else {
                $code = Account::where('id', $accountId)->latest('id')->first()->code;
                $code = $code . '0001';
            }
            $account = Account::create([
                'name' => $request->customer_name,
                'code' => $code,
                'parent_id' => $accountId,
                'type_id' => 1,
                'level' => 5
            ]);

            $customer = Customer::create([
                'name' => $request->customer_name,
                'CR' => 'N/A',
                'TIN' => 'N/A',
                'vatNumber' => 'N/A',
                'national_address' => 'N/A',
                'account_id' => $account->id,
                'user_id' => Auth::user()->id,
                'company_id' => $request->company_id,
            ]);
        } else {
            $customer = Customer::findOrFail($request->customer_id);
        }

        $validated['customer_id'] = $request->customer_id ?? $customer->id;

        $policy_containers = [];
        foreach($request->containers as $container) {
            $containerDb = Container::create([
                'code' => $container['code'],
                'container_type_id' => $container['container_type_id'],
                'date' => Carbon::now(),
                'exit_date' => Carbon::now(),
                'status' => 'خدمات',
                'customer_id' => $customer->id,
                'user_id' => Auth::user()->id
            ]);

            $containerDb->services()->attach($container['service_id'], [
                'price' => $container['price'],
                'notes' => $container['notes'] ?? null,
            ]);

            $policy_containers[] = $containerDb;
        }

        $policy = Policy::create($validated);
        $policy->containers()->attach($policy_containers);

        $new = $policy->load('containers')->toArray();
        logActivity('إنشاء بوليصة خدمات', "تم إنشاء بوليصة خدمات جديدة برقم " . $policy->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء بوليصة جديدة بنجاح, <a class="text-white fw-bold" href="'.route('policies.services.details', $policy).'">عرض البوليصة؟</a>');
    }

    public function servicePolicyDetails(Policy $policy) {
        return view('pages.policies.service_policy_details', compact('policy'));
    }

    public function deletePolicy(Policy $policy) {
        $old = $policy->toArray();
        $policy->containers()->detach();
        $policy->delete();

        logActivity('حذف بوليصة', "تم حذف بوليصة برقم " . $policy->code, $old, null);

        return redirect()->route('policies')->with('success', 'تم حذف البوليصة بنجاح');
    }
}
