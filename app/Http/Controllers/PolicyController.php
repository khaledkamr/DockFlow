<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContractRequest;
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
        $policies = Contract::orderBy('id', 'desc')->get();
        $customers = Customer::all();
        return view('admin.policies', compact('policies', 'customers'));
    }

    public function createPolicy(Request $request) {
        $customers = Customer::orderBy('name', 'asc')->get();
        $clientId = $request->input('customer_id', null);
        $client = [
            'id' => '',
            'name' => '',
            'CR' => '',
            'phone' => '',
        ];
        $containers = [
            [
                'type' => 'فئة 20',
                'price' => Container_type::where('name', 'فئة 20')->value('daily_price'),
                'count' => 0
            ],
            [
                'type' => 'فئة 30',
                'price' => Container_type::where('name', 'فئة 30')->value('daily_price'),
                'count' => 0
            ],
            [
                'type' => 'فئة 40',
                'price' => Container_type::where('name', 'فئة 40')->value('daily_price'),
                'count' => 0
            ],
        ];
        $price = 0;
        if($clientId) {
            $customer = Customer::find($clientId);
            $client = [
                'id' => $customer->id,
                'name' => $customer->name,
                'CR' => $customer->CR,
                'phone' => $customer->phone,
            ];
            $containers[0]['count'] = Container::where('customer_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة 20');
                                                })->count();
            $containers[1]['count'] = Container::where('customer_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة 30');
                                                })->count();
            $containers[2]['count'] = Container::where('customer_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة 40');
                                                })->count();
            foreach ($containers as $container) {
                $price += $container['count'] * $container['price'];
            }
        }

        return view('admin.createPolicy', compact('customers', 'client', 'containers', 'price'));
    }

    public function storePolicy(ContractRequest $request) {
        $validated = $request->validated();
        $days = Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['expected_end_date']));
        $validated['price'] = $validated['price'] * $days;

        $containers = Container::where('customer_id', $validated['customer_id'])
        ->where('status', 'في الإنتظار')->get();
        
        foreach($containers as $container) {
            $container->status = 'موجود';
            $container->save();
        }

        $contract = Contract::create($validated);
        $contract->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء عقد جديد بنجاح');
    }

    public function policyDetails($id) {
        $policy = Policy::with('containers.containerType')->findOrFail($id);
        $remainingDays = Carbon::now()->diffInDays(Carbon::parse($policy->expected_end_date));
        return view('admin.policyDetails', compact('policy', 'remainingDays'));
    }
}
