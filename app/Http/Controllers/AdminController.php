<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\UserRequest;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Policy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard(Request $request) {
        $customers = Customer::all()->count();
        $contracts = Contract::all()->count();
        $invoices = Invoice::all()->count();
        $containers = Container::all();

        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $availableContainers = $containers->where('date', '<=', $date)->where('status', 'متوفر')->count();
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
        $validated = $request->validated();
        $company->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    public function users() {
        $users = User::all();
        return view('pages.users.users', compact('users'));
    }

    public function userProfile(User $user) {
        // return $user;
        return view('pages.users.userProfile', compact('user'));
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
        // $user->roles()->attach($request->role == 'admin' ? 1 : 2);
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
        // $user->roles()->sync($request->role == 'admin' ? 1 : 2);
        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function deleteUser(User $user) {
        $user->delete();
        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function roles() {
        return view('pages.users.roles');
    }
}
