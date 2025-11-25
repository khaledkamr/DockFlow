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
use App\Models\Vehicle;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function handle(Request $request) {
        $method = Auth::user()->company->dashboard_method;

        if($method && method_exists($this, $method)) {
            return $this->$method($request);
        }

        return $this->defaultDashboard();
    }

    public function shams_dashboard(Request $request) {
        $customers = Customer::all()->count();
        $users = User::all()->count();
        $contracts = Contract::all()->count();
        $invoices = Invoice::all()->count();
        $containers = Container::all();

        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $availableContainers = $containers->where('date', '<=', $date)->where('status', 'في الساحة')->count();
        
        $receivedContainers = $containers->where('date', $date)->count();
        $deliveredContainers = $containers->where('exit_date', $date)->count();
        
        $policies = Policy::where('date', $date)->get();
        
        $containersEnteredTrend = [];
        $containersExitTrend = [];
        for($i = 6; $i >= 0; $i--) {
            $day = Carbon::parse($date)->subDays($i);
            $containersEnteredTrend[$day->format('l')] = $containers->where('date', $day->format('Y-m-d'))->count();
            $containersExitTrend[$day->format('l')] = $containers->where('exit_date', $day->format('Y-m-d'))->count();
        }

        $containersTypes = Container_type::all();
        $containersDistribution = [];
        foreach($containersTypes as $type) {
            $containersDistribution[$type->name] = $containers->where('container_type_id', $type->id)->where('status', 'في الساحة')->count();
        }

        $receipt_vouchers_amount = $policies->where('type', 'سند صرف نقدي')->sum('amount');
        $payment_vouchers_amount = $policies->where('type', 'سند قبض نقدي')->sum('amount');
        
        $balance = 0;
        $balanceBox = 0;
        $vouchersBox = Voucher::where('type', 'سند قبض نقدي')
            ->orWhere('type', 'سند صرف نقدي')
            ->get();

        foreach($vouchersBox as $voucher) {
            if($voucher->type == 'سند قبض نقدي') {
                $balance += $voucher->amount;
            } elseif($voucher->type == 'سند صرف نقدي') {
                $balance -= $voucher->amount;
            }
        }

        return view('pages.dashboards.shams_dashboard', compact(
            'customers', 
            'users',
            'contracts', 
            'invoices', 
            'containers',
            'availableContainers',
            'receivedContainers',
            'deliveredContainers',
            'containersEnteredTrend',
            'containersExitTrend',
            'containersDistribution',
            'receipt_vouchers_amount',
            'payment_vouchers_amount',
            'balanceBox'
        ));
    }

    public function masar_dashboard() {
        return view('pages.dashboards.masar_dashboard');
    }

    public function defaultDashboard() {
        $customers = Customer::all()->count();
        $users = User::all()->count();
        $contracts = Contract::all()->count();
        $invoices = Invoice::all()->count();
        
        return view('pages.dashboards.home', compact('customers', 'users', 'contracts', 'invoices'));
    }
}
