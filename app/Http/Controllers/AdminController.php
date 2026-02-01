<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Account;
use App\Models\Company;
use App\Models\Container_type;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function companies() {
        $companies = Company::all();
        return view('admin.companies', compact('companies'));
    }

    public function storeCompany(CompanyRequest $request) {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $logoPath = $file->storeAs('logos', $fileName, 'public');
            $validated['logo'] = $logoPath;
        }

        Company::create($validated);

        return redirect()->back()->with('success', 'تم إضافة شركة جديدة بنجاح.');
    }

    public function updateCompany(CompanyRequest $request, Company $company) {
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

        return redirect()->back()->with('success', 'تم تحديث بيانات الشركة بنجاح.');
    }

    public function companyDetails(Company $company) {
        $company->load(['modules', 'bankAccounts', 'address']);
        $modules = Module::all();
        $users = User::where('company_id', $company->id)->with('roles')->get();
        $roles = Role::where('company_id', $company->id)->with('permissions')->get();
        $permissions = Permission::all();
        
        $setupStatus = [
            'accounts' => Account::where('company_id', $company->id)->where('level', 1)->exists(),
            'container_types' => Container_type::where('company_id', $company->id)->exists(),
            'roles' => Role::where('company_id', $company->id)->exists(),
        ];
        
        $setupCompleted = !in_array(false, $setupStatus);
        
        return view('admin.company_details', compact('company', 'modules', 'users', 'roles', 'permissions', 'setupStatus', 'setupCompleted'));
    }

    private function checkSetupComplete(Company $company): bool {
        $hasAccounts = Account::where('company_id', $company->id)->where('level', 1)->exists();
        $hasContainerTypes = Container_type::where('company_id', $company->id)->exists();
        $hasRoles = Role::where('company_id', $company->id)->exists();
        
        return $hasAccounts && $hasContainerTypes && $hasRoles;
    }

    public function seedPrimaryAccounts(Company $company) {
        // Check if accounts already exist
        if (Account::where('company_id', $company->id)->where('level', 1)->exists()) {
            return redirect()->back()->with('error', 'الحسابات الرئيسية موجودة بالفعل لهذه الشركة');
        }

        $accounts = [
            ["name" => "الأصول", "code" => "1", "parent_id" => null, "type_id" => 1, "level" => 1, "is_active" => 1, 'company_id' => $company->id],
            ["name" => "الخصوم", "code" => "2", "parent_id" => null, "type_id" => 2, "level" => 1, "is_active" => 1, 'company_id' => $company->id],
            ["name" => "المصاريف", "code" => "3", "parent_id" => null, "type_id" => 3, "level" => 1, "is_active" => 1, 'company_id' => $company->id],
            ["name" => "الإيرادات", "code" => "4", "parent_id" => null, "type_id" => 4, "level" => 1, "is_active" => 1, 'company_id' => $company->id],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }

        // Check if all setup is now complete
        $setupComplete = $this->checkSetupComplete($company);
        if ($setupComplete) {
            return redirect()->back()->with('success', 'تم إضافة الحسابات الرئيسية بنجاح')->with('setup_completed', true);
        }

        return redirect()->back()->with('success', 'تم إضافة الحسابات الرئيسية بنجاح');
    }

    public function seedContainerTypes(Company $company) {
        // Check if container types already exist
        if (Container_type::where('company_id', $company->id)->exists()) {
            return redirect()->back()->with('error', 'أنواع الحاويات موجودة بالفعل لهذه الشركة');
        }

        $types = [
            ['name' => 'حاوية 20', 'daily_price' => 0, 'company_id' => $company->id],
            ['name' => 'حاوية 40', 'daily_price' => 0, 'company_id' => $company->id],
            ['name' => 'وزن زائد', 'daily_price' => 0, 'company_id' => $company->id],
            ['name' => 'طرود LCL', 'daily_price' => 0, 'company_id' => $company->id],
            ['name' => 'حاوية مبرده', 'daily_price' => 0, 'company_id' => $company->id],
        ];

        foreach ($types as $type) {
            Container_type::create($type);
        }

        // Check if all setup is now complete
        $setupComplete = $this->checkSetupComplete($company);
        if ($setupComplete) {
            return redirect()->back()->with('success', 'تم إضافة أنواع الحاويات بنجاح')->with('setup_completed', true);
        }

        return redirect()->back()->with('success', 'تم إضافة أنواع الحاويات بنجاح');
    }

    public function seedRoles(Company $company) {
        // Check if roles already exist
        if (Role::where('company_id', $company->id)->exists()) {
            return redirect()->back()->with('error', 'الأدوار موجودة بالفعل لهذه الشركة');
        }

        $roles = [
            ['name' => 'Admin', 'company_id' => $company->id],
            ['name' => 'مشرف', 'company_id' => $company->id],
            ['name' => 'محاسب', 'company_id' => $company->id],
            ['name' => 'مدير مالي', 'company_id' => $company->id],
            ['name' => 'موظف تشغيل', 'company_id' => $company->id],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Check if all setup is now complete
        $setupComplete = $this->checkSetupComplete($company);
        if ($setupComplete) {
            return redirect()->back()->with('success', 'تم إضافة الأدوار بنجاح')->with('setup_completed', true);
        }

        return redirect()->back()->with('success', 'تم إضافة الأدوار بنجاح');
    }

    public function users() {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function storeCompanyUser(Request $request, Company $company) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:3|confirmed',
            'phone' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'NID' => 'nullable|string|max:50',
            'role' => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'NID' => $validated['NID'] ?? null,
            'company_id' => $company->id,
        ]);

        if (!empty($validated['role'])) {
            $user->roles()->attach($validated['role']);
        }

        return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function updateCompanyUser(Request $request, Company $company, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|exists:roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['role'])) {
            $user->roles()->sync([$validated['role']]);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function deleteCompanyUser(Company $company, User $user) {
        if ($user->company_id !== $company->id) {
            return redirect()->back()->with('error', 'المستخدم لا ينتمي لهذه الشركة');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function storeCompanyRole(Request $request, Company $company) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'company_id' => $company->id,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        return redirect()->back()->with('success', 'تم إضافة الوظيفة بنجاح');
    }

    public function updateCompanyRole(Request $request, Company $company, Role $role) {
        if ($role->company_id !== $company->id) {
            return redirect()->back()->with('error', 'الوظيفة لا تنتمي لهذه الشركة');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->back()->with('success', 'تم تحديث الوظيفة بنجاح');
    }

    public function deleteCompanyRole(Company $company, Role $role) {
        if ($role->company_id !== $company->id) {
            return redirect()->back()->with('error', 'الوظيفة لا تنتمي لهذه الشركة');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->back()->with('success', 'تم حذف الوظيفة بنجاح');
    }

    public function storeCompanyPermission(Request $request, Company $company) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'تم إضافة الصلاحية بنجاح');
    }

    public function deleteCompany(Company $company) {
        $company->delete();
        return redirect()->route('admin.companies')->with('success', 'تم حذف الشركة بنجاح');
    }
}
