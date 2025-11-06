<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingRequest;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\ShippingPolicy;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function policies(Request $request) {
        $policies = ShippingPolicy::all();

        $search = $request->input('search', null);
        if ($search) {
            $policies = $policies->filter(function ($policy) use ($search) {
                $matchCode = stripos($policy->code, $search) !== false;
                $matchCustomer = stripos($policy->customer->name, $search) !== false;
                $matchDate = stripos($policy->date, $search) !== false;

                return $matchCode || $matchCustomer || $matchDate;
            });
        }

        $type = $request->input('type', 'all');
        if ($type && $type != 'all') {
            $policies = $policies->where('type', $type);
        }

        $is_received = $request->input('is_received', 'all');
        if ($is_received && $is_received != 'all') {
            if ($is_received == 'تم التسليم') {
                $policies = $policies->where('is_received', true);
            } elseif ($is_received == 'في الانتظار') {
                $policies = $policies->where('is_received', false);
            }
        }

        return view('pages.shipping.policies', compact('policies'));
    }

    public function createPolicy() {
        $customers = Customer::all();
        $suppliers = Supplier::all();
        $drivers = Driver::with('vehicle')->get();

        return view('pages.shipping.create_policy', compact('customers', 'suppliers', 'drivers'));
    }

    public function storePolicy(ShippingRequest $request) {
        $validated = $request->validated();
        $policy = ShippingPolicy::create($validated);

        $policy->goods()->createMany($request['goods']);

        return redirect()->back()->with('success', 'تم إنشاء بوليصة شحن جديدة, <a class="text-white fw-bold" href="'.route('shipping.policies.details', $policy).'">عرض البوليصة؟</a>');
    } 

    public function policyDetails(ShippingPolicy $policy) {
        return view('pages.shipping.policy_details', compact('policy'));
    }

    public function toggleReceiveStatus(ShippingPolicy $policy) {
        $policy->is_received = !$policy->is_received;
        $policy->save();

        return redirect()->back()->with('success', 'تم تحديث حالة الاستلام بنجاح');
    }

    public function reports(Request $request) {
        $policies = ShippingPolicy::all();
        $customers = Customer::all();
        return view('pages.shipping.reports', compact('policies', 'customers'));
    }
}
