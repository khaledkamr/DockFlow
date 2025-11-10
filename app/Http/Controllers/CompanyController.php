<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyAddressRequest;
use App\Http\Requests\CompanyRequest;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\AssignOp\Mod;

class CompanyController extends Controller
{
    public function companies() {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    public function company(Company $company) {
        $modules = Module::all();
        return view('pages.company', compact('company', 'modules'));
    }

    public function updateCompany(CompanyRequest $request, Company $company) {
        if(Gate::allows('تعديل بيانات الشركة') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل بيانات الشركة');
        }

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $logoPath = $file->storeAs('logos', $fileName, 'public');
            $validated['logo'] = $logoPath;
        }

        $company->update($validated);
        
        return redirect()->back()->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    public function addModuleToCompany(Request $request, Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $moduleIds = $request->input('module_ids', []);
        $company->modules()->sync($moduleIds);

        return redirect()->back()->with('success', 'تم إضافة المديول بنجاح إلى الشركة');
    }

    public function toggleCompanyModule(Company $company, $moduleId) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }
        
        $module = $company->modules()->where('modules.id', $moduleId)->firstOrFail();
        $company->modules()->updateExistingPivot($moduleId, ['is_active' => !$module->pivot->is_active]);

        return redirect()->back()->with('success', "تم " . ($module->pivot->is_active ? 'تعطيل' : 'تفعيل') . " المديول '{$module->name}' بنجاح");
    }

    public function addLogo(Request $request, Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $logoPath = $file->storeAs('logos', $fileName, 'public');
            $company->update(['logo' => $logoPath]);

            return redirect()->back()->with('success', 'تم إضافة الشعار بنجاح');
        }

        return redirect()->back()->with('error', 'لم يتم اضافة شعار جديد');
    }

    public function removeLogo(Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        if(Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->update(['logo' => null]);

        return redirect()->back()->with('success', 'تم إزالة الشعار بنجاح');
    }

    public function storeAddress(CompanyAddressRequest $request, Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $validated = $request->validated();
        $company->address()->create($validated);

        return redirect()->back()->with('success', 'تم إضافة بيانات العنوان الوطني بنجاح');
    }

    public function updateAddress(CompanyAddressRequest $request, Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $validated = $request->validated();
        $company->address()->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات العنوان الوطني بنجاح');
    }

    public function storeBankNumber(Request $request, Company $company) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $validated = $request->validate([
            'bank' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);

        $company->bankAccounts()->create($validated);
            
        return redirect()->back()->with('success', 'تم إضافة رقم البنك بنجاح');
    }

    public function updateBankNumber(Request $request, $bankAccountId) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $validated = $request->validate([
            'bank' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);

        $bankAccount = BankAccount::findOrFail($bankAccountId);
        $bankAccount->update($validated);

        return redirect()->back()->with('success', 'تم تحديث رقم البنك بنجاح');
    }
    
    public function deleteBankNumber($bankAccountId) {
        if(Gate::denies('تعديل بيانات الشركة')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الشركة');
        }

        $bankAccount = BankAccount::findOrFail($bankAccountId);
        $bankAccount->delete();

        return redirect()->back()->with('success', 'تم حذف رقم البنك بنجاح');
    }
}
