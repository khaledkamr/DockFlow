<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\UserRequest;

class CustomerController extends Controller
{
    public function customers() {
        $customers = Customer::orderBy('id', 'desc')->get();
        return view('admin.users.customers', compact('customers'));
    }

    public function customerProfile($id) {
        $customer = Customer::findOrFail($id);
        return view('admin.users.customerProfile', compact('customer'));
    }

    public function storeCustomer(CustomerRequest $request) {
        $validated = $request->validated();
        Customer::create($validated);
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
