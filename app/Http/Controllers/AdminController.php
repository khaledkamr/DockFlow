<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\invoice;
use App\Models\Policy;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request) {
        $customers = Customer::all()->count();
        $contracts = Contract::all()->count();
        $invoices = invoice::all()->count();
        $containers = Container::all();

        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $availableContainers = $containers->where('date', '<=', $date)->count();
        if($date == Carbon::now()->format('Y-m-d')) {
            $waitingContainers = $containers->where('status', 'في الإنتظار')->count();
        } else {
            $waitingContainers = 0;
        }
        $receivedContainers = $containers->where('date', $date)->count();
        $deliveredContainers = $containers->where('exit_date', $date)->count();
        $policies = Policy::where('date', $date)->get();
        
        return view('admin.home', compact(
            'customers', 
            'contracts', 
            'invoices', 
            'containers',
            'availableContainers',
            'waitingContainers',
            'receivedContainers',
            'deliveredContainers'
        ));
    }
}
