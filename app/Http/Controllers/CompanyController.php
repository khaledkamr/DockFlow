<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Module;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\AssignOp\Mod;

class CompanyController extends Controller
{
    public function companies() {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    public function addModuleToCompany(Request $request, Company $company) {
        $moduleIds = $request->input('module_ids', []);
        $company->modules()->sync($moduleIds);

        return redirect()->back()->with('success', 'تم إضافة المديول بنجاح إلى الشركة');
    }

    public function toggleCompanyModule(Company $company, $moduleId) {
        $module = $company->modules()->where('modules.id', $moduleId)->firstOrFail();
        $company->modules()->updateExistingPivot($moduleId, ['is_active' => !$module->pivot->is_active]);

        return redirect()->back()->with('success', "تم " . ($module->pivot->is_active ? 'تعطيل' : 'تفعيل') . " المديول '{$module->name}' بنجاح");
    }
}
