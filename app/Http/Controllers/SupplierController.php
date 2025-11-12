<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\Account;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function suppliers(Request $request) {
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        
        $search = $request->input('search', null);
        if($search) {
            $suppliers = $suppliers->filter(function($supplier) use($search) {
                return stripos($supplier->id, $search) !== false 
                    || stripos($supplier->name, $search) !== false;
            });
        }

        $accountId = Account::where('name', 'الموردين')->first()->id;
        $supplierAccounts = Account::where('parent_id', $accountId)->get();

        return view('pages.suppliers.suppliers', compact('suppliers', 'supplierAccounts'));
    }

    public function supplierProfile(Supplier $supplier) {
        return view('pages.suppliers.supplierProfile', compact('supplier'));
    }

    public function storeSupplier(SupplierRequest $request) {
        if(Account::where('name', 'الموردين')->doesntExist()) {
            return redirect()->back()->with('error', 'يرجى إنشاء حساب الموردين أولاً من شاشة الحسابات');
        }

        $accountId = Account::where('id', $request->account_id)->first()->id;
        $lastSupplier = Account::where('parent_id', $accountId)->latest('id')->first();
        if($lastSupplier) {
            $code = (string)((int)$lastSupplier->code + 1);
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
        Supplier::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء مورد جديد بنجاح');
    }

    public function updateSupplier(SupplierRequest $request, Supplier $supplier) {
        $validated = $request->validated();
        $supplier->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    public function deleteSupplier(Supplier $supplier) {
        //
    }
}
