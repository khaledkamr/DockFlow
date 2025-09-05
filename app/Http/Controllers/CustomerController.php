<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Account;

class CustomerController extends Controller
{
    public function customers(Request $request) {
        $customers = Customer::orderBy('id', 'desc')->get();
        $search = $request->input('search', null);
        if($search) {
            $customers = $customers->filter(function($customer) use($search) {
                return stripos($customer->id, $search) !== false 
                    || stripos($customer->name, $search) !== false;
            });
        }
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $customers->forPage(request()->get('page', 1), 50),
            $customers->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.users.customers', compact('customers'));
    }

    public function customerProfile($id) {
        $customer = Customer::findOrFail($id);
        // return $customer->contract->services;
        return view('admin.users.customerProfile', compact('customer'));
    }

    public function storeCustomer(CustomerRequest $request) {
        $accountId = Account::where('name', 'عملاء التشغيل')->first()->id;
        $lastCustomer = Account::where('parent_id', $accountId)->latest('id')->first();
        if($lastCustomer) {
            $code = $lastCustomer->code + 1;
        } else {
            $code = Account::where('id', $accountId)->latest('id')->first()->code;
            $code = $code . '0001';
        }
        $account = Account::create([
            'name' => $request->name,
            'code' => $code,
            'parent_id' => $accountId,
            'type_id' => 1,
            'level' => 5
        ]);

        Customer::create([
            'name' => $request->name,
            'CR' => $request->CR,
            'TIN' => $request->TIN,
            'national_address' => $request->national_address,
            'phone' => $request->phone,
            'email' => $request->email,
            'account_id' => $account->id
        ]);
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateCustomer(CustomerRequest $request, $id) {
        $customer = Customer::findOrFail($id);
        $validated = $request->validated();
        $customer->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteCustomer($id) {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();
        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }
}
