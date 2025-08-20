<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\UserRequest;

class CustomerController extends Controller
{
    public function customers() {
        $customers = Customer::orderBy('id', 'desc')->get();
        return view('admin.customers', compact('customers'));
    }

    public function customerProfile($id) {
        $user = User::findOrFail($id);
        return view('admin.userProfile', compact('user'));
    }

    public function storeCustomer(UserRequest $request) {
        $validated = $request->validated();
        User::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateCustomer(UserRequest $request, $id) {
        $user = User::findOrFail($id);
        $validated = $request->validated();
        $user->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteCustomer($id) {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();
        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }
}
