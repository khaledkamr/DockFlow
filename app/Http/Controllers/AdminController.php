<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\VehicleRequest;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function dashboard(Request $request) {
        $customers = Customer::all()->count();
        $contracts = Contract::all()->count();
        $invoices = Invoice::all()->count();
        $containers = Container::all();

        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $availableContainers = $containers->where('date', '<=', $date)->where('status', 'في الساحة')->count();
        if($date == Carbon::now()->format('Y-m-d')) {
            $waitingContainers = $containers->where('status', 'في الإنتظار')->count();
        } else {
            $waitingContainers = 0;
        }
        $receivedContainers = $containers->where('date', $date)->count();
        $deliveredContainers = $containers->where('exit_date', $date)->count();
        $policies = Policy::where('date', $date)->get();
        $containersTrend = [1,5, 3,10,25 ,7, 10, 15, 11, 20];
        $containersDistribution = [30, 25, 40, 5];
        
        return view('pages.home', compact(
            'customers', 
            'contracts', 
            'invoices', 
            'containers',
            'availableContainers',
            'waitingContainers',
            'receivedContainers',
            'deliveredContainers',
            'containersTrend',
            'containersDistribution'
        ));
    }

    public function company(Company $company) {
        return view('pages.company', compact('company'));
    }

    public function updateCompany(CompanyRequest $request, Company $company) {
        if(Gate::allows('تعديل بيانات الشركة') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل بيانات الشركة');
        }
        $validated = $request->validated();
        $company->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    public function users(Request $request) {
        $users = User::all();
        $roles = Role::all();

        $filter = $request->input('role', 'all');
        if($filter != 'all') {
            $users = $users->filter(function($user) use ($filter) {
                return $user->roles->contains('id', $filter);
            });
        }

        $search = $request->input('search', '');
        if($search) {
            $users = $users->filter(function($user) use ($search) {
                return str_contains(strtolower($user->name), strtolower($search)) || str_contains(strtolower($user->email), strtolower($search));
            });
        }

        return view('pages.users.users', compact('users', 'roles'));
    }

    public function userProfile(User $user) {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.users.userProfile', compact('user', 'roles', 'permissions'));
    }

    public function storeUser(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:3|confirmed',
            'nationality' => 'nullable|string|max:255',
            'NID' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nationality' => $request->nationality,
            'NID' => $request->NID,
            'company_id' => Auth::user()->company_id,
        ]);
        $user->roles()->attach($request->role);
        return redirect()->back()->with('success', 'تم إضافة مستخدم جديد بنجاح');
    }

    public function updateUser(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nationality' => 'nullable|string|max:255',
            'NID' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role' => 'required',
        ]);
        if($request->password) {
            if($request->password != $request->password_confirmation) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور غير متطابقة']);
            } elseif(strlen($request->password) < 3) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور يجب أن تكون على الأقل 3 أحرف']);
            }
            $validated['password'] = Hash::make($request->password);
        }
        $user->update($validated);
        $user->roles()->sync($request->role);
        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function updateMyUser(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nationality' => 'nullable|string|max:255',
            'NID' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);
        if($request->password) {
            if($request->password != $request->password_confirmation) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور غير متطابقة']);
            } elseif(strlen($request->password) < 3) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور يجب أن تكون على الأقل 3 أحرف']);
            }
            $validated['password'] = Hash::make($request->password);
        }
        $user->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function updatePassword(Request $request, User $user) {
        $request->validate([
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    public function deleteUser(User $user) {
        $user->delete();
        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function roles() {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.users.roles', compact('roles', 'permissions'));
    }

    public function storeRole(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);
        $role = Role::create([
            'name' => $request->name,
        ]);
        $role->permissions()->attach($request->permissions);
        return redirect()->back()->with('success', 'تم إضافة وظيفة جديدة بنجاح');
    }

    public function updateRole(Request $request, Role $role) {
        $role->permissions()->sync($request->permissions);
        return redirect()->back()->with('success', 'تم تحديث صلاحيات الوظيفة بنجاح');
    }

    public function deleteRole(Role $role) {
        $role->delete();
        return redirect()->back()->with('success', 'تم حذف الوظيفة بنجاح');
    }
}
