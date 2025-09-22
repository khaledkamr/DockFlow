<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Company;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Policy;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        
        return view('admin.home', compact(
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

    public function company($id) {
        $company = Company::findOrFail($id);
        return view('admin.company', compact('company'));
    }

    public function updateCompany(CompanyRequest $request, $id) {
        $validated = $request->validated();
        $company = Company::findOrFail($id);
        $company->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }
}
