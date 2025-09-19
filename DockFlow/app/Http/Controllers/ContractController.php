<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function contracts(Request $request) {
        $contracts = Contract::orderBy('id', 'desc')->get();
        $search = $request->input('search', null);
        if($search) {
            $contracts = $contracts->filter(function($contract) use($search) {
                return stripos($contract->id, $search) !== false 
                    || stripos($contract->customer->name, $search) !== false
                    || stripos($contract->start_date, $search) !== false;
            });
        }
        $contracts = new \Illuminate\Pagination\LengthAwarePaginator(
            $contracts->forPage(request()->get('page', 1), 50),
            $contracts->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.contracts.contracts', compact('contracts'));
    }

    public function createContract() {
        $company = Company::first();
        $customers = Customer::all();
        $services = Service::all();
        return view('admin.contracts.createContract', compact('company', 'customers', 'services'));
    }

    public function storeContract(ContractRequest $request) {
        $validated = $request->validated();
        $contract = Contract::create($validated);
        if($request->has('services')) {
            foreach($request->services as $service) {
                $contract->services()->attach($service['service_id'], [
                    'price' => $service['price'],
                    'unit' => $service['unit'],
                    'unit_desc' => $service['unit_desc']
                ]);
            }
        }
        return redirect()->back()->with('success', 'تم إنشاء العقد بنجاح');
    }

    public function contractDetails($id) {
        $contract = Contract::findOrFail($id);
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        return view('admin.contracts.contractDetails', compact('contract', 'months', 'days'));
    }

    public function services() {
        $services = Service::all();
        return view('admin.contracts.services', compact('services'));
    }

    public function storeService(Request $request) {
        Service::create(['description' => $request->description]);
        return redirect()->back()->with('success', 'تم إضافة خدمة جديدة بنجاح');
    }

    public function updateService(Request $request, $id) {
        $service = Service::findOrFail($id);
        $service->description = $request->description;
        $service->save();
        return redirect()->back()->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function deleteService($id) {
        if(in_array($id, [0,1,2,3])) {
            return redirect()->back()->with('error', 'لا يمكنك حذف هذه الخدمة');
        }
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->back()->with('success', 'تم حذف الخدمة بنجاح');
    }
}
