<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\VehicleRequest;
use App\Models\Company;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Role;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Vehicle;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function users(Request $request) {
        if(Gate::denies('إدارة المستخدمين')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول إلى إدارة المستخدمين');
        }

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

    public function myProfile(User $user) {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('pages.users.my_profile', compact('user', 'roles', 'permissions'));
    }

    public function userProfile(User $user) {
        $roles = Role::all();
        $permissions = Permission::all();
        $activities = UserLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(100)->get();
        return view('pages.users.user_profile', compact('user', 'roles', 'permissions', 'activities'));
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

    public function storePermission(Request $request) {
        $request->validate(['name' => 'required|string|max:255|unique:permissions,name']);
        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'تم إضافة صلاحية جديدة بنجاح');
    }

    public function settings() {
        return view('pages.settings.settings');
    }
    
    public function logs(Request $request) {
        $logs = UserLog::query()
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('id', 'desc')
            ->paginate(100);

        $actions = UserLog::select('action')->distinct()->pluck('action');
        $users = User::all();

        return view('pages.users.logs', compact('logs', 'actions', 'users'));
    }

    public function deleteLogs(Request $request) {
        $logs = UserLog::where('company_id', Auth::user()->company_id);

        if($request->action) {
            $logs->where('action', $request->action);
        }
        if($request->user_id) {
            $logs->where('user_id', $request->user_id);
        }
        if($request->from) {
            $logs->whereDate('created_at', '>=', $request->from);
        }
        if($request->to) {
            $logs->whereDate('created_at', '<=', $request->to);
        }
            
        $logs->delete();

        return redirect()->back()->with('success', 'تم حذف السجلات بنجاح');
    }

    public function updateTimezone(Request $request) {
        $request->validate([
            'timezone' => 'required|string|in:Africa/Cairo,Asia/Riyadh',
        ]);

        $user = Auth::user();
        $user->timezone = $request->timezone;
        $user->save();

        return redirect()->back()->with('success', 'تم تحديث المنطقة الزمنية بنجاح');
    }
}
