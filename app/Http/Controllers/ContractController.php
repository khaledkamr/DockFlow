<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                if( !isset($service['price']) || !isset($service['unit']) || !isset($service['unit_desc']) ) {
                    return redirect()->back()->withInput()->with('error', 'جميع حقول الخدمات مطلوبة');
                }
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
        if(!$request->description) {
            return redirect()->back()->with('error', 'وصف الخدمة مطلوب');
        }
        Service::create(['description' => $request->description]);
        return redirect()->back()->with('success', 'تم إضافة خدمة جديدة بنجاح');
    }

    public function updateService(Request $request, $id) {
        if(!$request->description) {
            return redirect()->back()->with('error', 'وصف الخدمة مطلوب');
        }
        $service = Service::findOrFail($id);
        $service->description = $request->description;
        $service->save();
        return redirect()->back()->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function deleteService($id) {
        $service = Service::findOrFail($id);
        if($service->type == 'primary') {
            return redirect()->back()->with('error', 'لا يمكنك حذف هذه الخدمة');
        }
        $service->delete();
        return redirect()->back()->with('success', 'تم حذف الخدمة بنجاح');
    }

    public function attachFile(Request $request, $id) {
        $contract = Contract::findOrFail($id);
        if($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');
            Attachment::create([
                'contract_id' => $contract->id,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'user_id' => Auth::user()->id,
            ]);
            return redirect()->back()->with('success', 'تم إرفاق الملف بنجاح');
        }
        return redirect()->back()->with('error', 'لم يتم إرفاق أي ملف');
    }

    public function deleteAttachment($id) {
        $attachment = Attachment::findOrFail($id);
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();
        return redirect()->back()->with('success', 'تم حذف المرفق بنجاح');
    }
}
