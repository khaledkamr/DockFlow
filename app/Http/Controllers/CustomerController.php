<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

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
        return view('pages.customers.customers', compact('customers'));
    }

    public function customerProfile(Customer $customer) {
        return view('pages.customers.customerProfile', compact('customer'));
    }

    public function storeCustomer(CustomerRequest $request) {
        if(Gate::allows('إضافة عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء عميل');
        }
        if(Account::where('name', 'عملاء التشغيل')->doesntExist()) {
            return redirect()->back()->with('error', 'يرجى إنشاء حساب عملاء التشغيل أولاً من شاشة الحسابات');
        }
        
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

        $validated = $request->validated();
        $validated['account_id'] = $account->id;

        Customer::create($validated);
        
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateCustomer(CustomerRequest $request, Customer $customer) {
        if(Gate::allows('تعديل عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل بيانات عميل');
        }
        $validated = $request->validated();
        $customer->update($validated);

        $customer->account()->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteCustomer(Customer $customer) {
        if(Gate::allows('حذف عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف عميل');
        }
        if($customer->invoices()->exists() || $customer->contract()->exists() || $customer->containers()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا العميل لوجود تعاملات مرتبطة به');
        }
        $name = $customer->name;
        $customer->delete();
        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }
}
