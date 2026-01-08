<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\Account;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function suppliers(Request $request) {
        $accounts = Account::where('level', 5)->get();
        $supplierAccount = Account::where('name', 'الموردين')->first();
        $supplierTypes = $supplierAccount ? $supplierAccount->children()->pluck('name')->toArray() : [];
        $suppliers = Supplier::query();
        
        $search = $request->input('search', null);

        if($search) {
            $suppliers->where('name', 'like', '%' . $search . '%');
        }

        $suppliers = $suppliers->with(['account', 'made_by'])->orderBy('created_at', 'desc')->paginate(100)->withQueryString();

        $accountId = Account::where('name', 'الموردين')->first()->id;
        $supplierAccounts = Account::where('parent_id', $accountId)->get();

        return view('pages.suppliers.suppliers', compact(
            'suppliers', 
            'supplierAccounts', 
            'accounts',
            'supplierTypes',
        ));
    }

    public function supplierProfile(Supplier $supplier) {
        $accounts = Account::where('level', 5)->get();
        return view('pages.suppliers.supplier_profile', compact('supplier', 'accounts'));
    }

    public function storeSupplier(SupplierRequest $request) {
        if(Account::where('name', 'حسابات دائنة تحت التسوية')->doesntExist()) {
            return redirect()->back()->with('error', 'يرجى إنشاء حساب حسابات دائنة تحت التسوية أولاً من شاشة الحسابات');
        }

        $accountId = Account::where('name', $request->type)->where('level', 4)->first()->id;
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

        $settlementAccountId = Account::where('name', 'حسابات دائنة تحت التسوية')->where('level', 4)->first()->id;
        $lastSupplier = Account::where('parent_id', $settlementAccountId)->latest('id')->first();
        if($lastSupplier) {
            $code = (string)((int)$lastSupplier->code + 1);
        } else {
            $code = Account::where('id', $settlementAccountId)->latest('id')->first()->code;
            $code = $code . '0001';
        }
        $settlement_account = Account::create([
            'name' => $request->name . " - تحت التسوية",
            'code' => $code,
            'parent_id' => $settlementAccountId,
            'type_id' => 1,
            'level' => 5
        ]);

        $validated = $request->validated();
        $validated['account_id'] = $account->id;
        $validated['settlement_account_id'] = $settlement_account->id;
        $new = Supplier::create($validated);
        logActivity('إنشاء مورد', "تم إنشاء مورد جديد باسم " . $new->name, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إنشاء مورد جديد بنجاح');
    }

    public function updateSupplier(SupplierRequest $request, Supplier $supplier) {
        $old = $supplier->toArray();

        $validated = $request->validated();
        $supplier->update($validated);
        
        $new = $supplier->toArray();
        logActivity('تحديث مورد', "تم تحديث مورد باسم " . $supplier->name, $old, $new);
        return redirect()->back()->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    public function deleteSupplier(Supplier $supplier) {
        if($supplier->expenseInvoices()->exists() || $supplier->shippingPolicies()->exists() || $supplier->transportOrders()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المورد لأنه مرتبط بسجلات أخرى في النظام');
        }

        $old = $supplier->toArray();
        $supplier->delete();
        logActivity('حذف مورد', "تم حذف مورد باسم " . $supplier->name, $old, null);
        return redirect()->back()->with('success', 'تم حذف المورد بنجاح');
    }
}
