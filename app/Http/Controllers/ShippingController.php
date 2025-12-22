<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingRequest;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Places;
use App\Models\ShippingPolicy;
use App\Models\Supplier;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    public function policies(Request $request) {
        $policies = ShippingPolicy::orderBy('code', 'desc')->get();

        $search = $request->input('search', null);
        if ($search) {
            $policies = $policies->filter(function ($policy) use ($search) {
                $matchCode = stripos($policy->code, $search) !== false;
                $matchCustomer = stripos($policy->customer->name, $search) !== false;
                $matchDate = stripos($policy->date, $search) !== false;
                $matchContainer = $policy->goods->filter(function($good) use ($search) {
                    return stripos($good->description, $search) !== false;
                })->isNotEmpty();

                return $matchCode || $matchCustomer || $matchDate || $matchContainer;
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
        $destinations = Places::all();

        return view('pages.shipping.create_policy', compact('customers', 'suppliers', 'drivers', 'destinations'));
    }

    public function storePolicy(ShippingRequest $request) {
        $validated = $request->validated();
        $policy = ShippingPolicy::create($validated);

        $policy->goods()->createMany($request['goods']);

        if($policy->type == 'ناقل خارجي') {
            $supplierAccount = $policy->supplier ? $policy->supplier->account : null;
            $creditAccount = Account::where('name', 'ايجار شاحنات')->where('level', 5)->first();

            $journal = JournalEntry::create([
                'type' => 'قيد يومي',
                'date' => $policy->date,
                'totalDebit' => $policy->supplier_cost,
                'totalCredit' => $policy->supplier_cost,
                'user_id' => $policy->user_id
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $supplierAccount ? $supplierAccount->id : null,
                'debit' => 0,
                'credit' => $policy->supplier_cost,
                'description' => 'بوليصة شحن رقم '.$policy->code
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $creditAccount ? $creditAccount->id : null,
                'debit' => $policy->supplier_cost,
                'credit' => 0,
                'description' => 'بوليصة شحن رقم '.$policy->code
            ]);

            $new = $journal->load('lines')->toArray();
            logActivity('إنشاء قيد', "تم إنشاء قيد جديد برقم " . $journal->code, null, $new);
        }

        if($policy->from && $policy->to) {
            Places::firstOrCreate([
                'name' => $policy->from,
            ]);
            Places::firstOrCreate([
                'name' => $policy->to,
            ]);
        }

        $new = $policy->load('goods')->toArray();
        logActivity('إنشاء بوليصة شحن', "تم إنشاء بوليصة شحن جديدة برقم " . $policy->code, null, $new);

        return redirect()->back()->with('success', 'تم إنشاء بوليصة شحن جديدة, <a class="text-white fw-bold" href="'.route('shipping.policies.details', $policy).'">عرض البوليصة؟</a>');
    } 

    public function updateNotes(Request $request, ShippingPolicy $policy) {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $old = $policy->toArray();
        $policy->update($request->only('notes'));
        $new = $policy->toArray();
        logActivity('تحديث بيانات بوليصة شحن', "تم تحديث بيانات بوليصة الشحن برقم " . $policy->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات بوليصة الشحن بنجاح');
    }

    public function policyDetails(ShippingPolicy $policy) {
        $drivers = Driver::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        return view('pages.shipping.policy_details', compact(
            'policy',
            'drivers',
            'suppliers',
            'customers'
        ));
    }

    public function updatePolicy(Request $request, ShippingPolicy $policy) {
        $validated = $request->validate([
            'code' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'from' => 'required|string',
            'to' => 'required|string',
            'type' => 'required|string',
            'driver_id' => 'required_if:type,ناقل داخلي|nullable|exists:drivers,id',
            'vehicle_id' => 'required_if:type,ناقل داخلي|nullable|exists:vehicles,id',
            'supplier_id' => 'required_if:type,ناقل خارجي|nullable|exists:suppliers,id',
            'driver_name' => 'nullable|string',
            'driver_contact' => 'nullable|string',
            'vehicle_plate' => 'nullable|string',
            'client_cost' => 'required|numeric|min:0',
            'supplier_cost' => 'nullable|numeric|min:0',
            'diesel_cost' => 'nullable|numeric|min:0',
            'driver_wage' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
        ]);

        $old = $policy->toArray();
        $policy->update($validated);
        $new = $policy->toArray();
        logActivity('تحديث بوليصة الشحن', 'تم تحديث بيانات بوليصة شحن رقم ' . $policy->code, $old, $new);

        return redirect()->back()->with('success', 'تم تعديل بيانات بوليصة الشحن بنجاح');
    }

    public function toggleReceiveStatus(ShippingPolicy $policy) {
        $policy->is_received = !$policy->is_received;
        $policy->save();

        logActivity('تحديث حالة استلام بوليصة شحن', "تم تحديث حالة الاستلام لبوليصة الشحن برقم " . $policy->code . " إلى " . ($policy->is_received ? 'تم التسليم' : 'في الانتظار'));

        return redirect()->back()->with('success', 'تم تحديث حالة الاستلام بنجاح');
    }

    public function updateGoods(Request $request, ShippingPolicy $policy) {
        $request->validate([
            'goods' => 'required|array',
            'goods.*.description' => 'required|string|max:255',
            'goods.*.quantity' => 'nullable|numeric|min:0',
            'goods.*.weight' => 'nullable|numeric|min:0',
            'goods.*.notes' => 'nullable|string|max:500',
        ]);

        $old = $policy->load('goods')->toArray();
        
        $policy->goods()->delete();
        
        $policy->goods()->createMany($request->goods);
        
        $new = $policy->load('goods')->toArray();
        logActivity('تحديث بضائع بوليصة شحن', "تم تحديث بضائع بوليصة الشحن برقم " . $policy->code, $old, $new);
        
        return redirect()->back()->with('success', 'تم تحديث بيانات البضائع بنجاح');
    }

    public function deletePolicy(ShippingPolicy $policy) {
        $old = $policy->load('goods')->toArray();

        $policy->goods()->delete();
        $policy->delete();
        
        logActivity('حذف بوليصة شحن', "تم حذف بوليصة الشحن برقم " . $policy->code, $old, null);

        return redirect()->route('shipping.policies')->with('success', 'تم حذف بوليصة الشحن بنجاح');
    }

    public function reports(Request $request) {
        $policies = ShippingPolicy::orderBy('date')->get();
        $customers = Customer::all();
        $drivers = Driver::with('vehicle')->get();
        $vehicles = Vehicle::all();
        $suppliers = Supplier::all();
        $loadingLocations = $policies->pluck('from')->unique();
        $deliveryLocations = $policies->pluck('to')->unique();

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $status = $request->input('status', 'all');
        $invoice_status = $request->input('invoice_status', 'all');
        $supplier = $request->input('supplier', 'all');
        $driver = $request->input('driver', 'all');
        $vehicle = $request->input('vehicle', 'all');
        $loading_location = $request->input('loading_location', 'all');
        $delivery_location = $request->input('delivery_location', 'all');

        if($customer && $customer != 'all') {
            $policies = $policies->where('customer_id', $customer);
        }
        if($from) {
            $policies = $policies->where('date', '>=', $from);
        }
        if($to) {
            $policies = $policies->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $policies = $policies->where('type', $type);
        }
        if($status && $status != 'all') {
            $policies = $policies->where('is_received', $status == 'تم التسليم' ? true : false);
        }
        if($invoice_status && $invoice_status != 'all') {
            $policies = $policies->filter(function($policy) use ($invoice_status) {
                $invoice = $policy->invoices->filter(function($invoice) {
                    return $invoice->type == 'شحن';
                })->isEmpty();
                if($invoice_status == 'with_invoice') {
                    return $invoice == false;
                } elseif($invoice_status == 'without_invoice') {
                    return $invoice;
                }
            });
        }
        if($supplier && $supplier != 'all') {
            $policies = $policies->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $policies = $policies->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $policies = $policies->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $policies = $policies->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $policies = $policies->where('to', $delivery_location);
        }

        $perPage = $request->input('per_page', 100);
        $policies = new \Illuminate\Pagination\LengthAwarePaginator(
            $policies->forPage(request()->get('page', 1), $perPage),
            $policies->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.shipping.reports', compact(
            'policies', 
            'customers', 
            'drivers', 
            'vehicles', 
            'suppliers', 
            'loadingLocations', 
            'deliveryLocations',
            'perPage'
        ));
    }
}
