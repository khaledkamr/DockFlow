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
use Illuminate\Support\Facades\Gate;
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
        return view('pages.contracts.contracts', compact('contracts'));
    }

    public function createContract() {
        if(Gate::allows('إضافة عقد') == false) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }
        $company = Auth::user()->company;
        $customers = Customer::all();
        $customers = $customers->filter(function($customer) {
            return !$customer->contract;
        });
        $services = Service::all();
        return view('pages.contracts.createContract', compact('company', 'customers', 'services'));
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

        return redirect()->back()->with('success', 'تم إنشاء عقد جديد بنجاح, <a class="text-white fw-bold" href="'.route('contracts.details', $contract).'">عرض العقد؟</a>');
    }
    
    public function updateContract(Request $request, Contract $contract) {
        if(Gate::denies('تعديل بيانات العقد') == true) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية التعديل على بيانات العقد');
        }

        if($request->has('start_date')) {
            $contract->start_date = $request->start_date;
            $contract->end_date = $request->end_date;
            $contract->payment_grace_period = $request->payment_grace_period;
            $contract->payment_grace_period_unit = $request->payment_grace_period_unit;
            $contract->save();
        }

        if($request->has('company_representative')) {
            $contract->company_representative = $request->company_representative;
            $contract->company_representative_nationality = $request->company_representative_nationality;
            $contract->company_representative_NID = $request->company_representative_NID;
            $contract->save();
        }

        if($request->has('customer_representative')) {
            $contract->customer_representative = $request->customer_representative;
            $contract->customer_representative_nationality = $request->customer_representative_nationality;
            $contract->customer_representative_NID = $request->customer_representative_NID;
            $contract->save();
        }

        if($request->has('services')) {
            $contract->services()->detach();
            foreach($request->services as $service) {
                $contract->services()->attach($service['service_id'], [
                    'price' => $service['price'],
                    'unit' => $service['unit'],
                    'unit_desc' => $service['unit_desc']
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات العقد بنجاح');
    }

    public function contractDetails(Contract $contract) {
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        $services = Service::all();

        return view('pages.contracts.contractDetails', compact('contract', 'months', 'days', 'services'));
    }

    public function services() {
        $services = Service::all();
        return view('pages.contracts.services', compact('services'));
    }

    public function storeService(Request $request) {
        if(Gate::allows('إضافة خدمة') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإنشاء خدمة');
        }
        if(!$request->description) {
            return redirect()->back()->with('error', 'وصف الخدمة مطلوب');
        }
        Service::create(['description' => $request->description]);
        return redirect()->back()->with('success', 'تم إضافة خدمة جديدة بنجاح');
    }

    public function updateService(Request $request, $id) {
        if(Gate::allows('تعديل خدمة') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل الخدمة');
        }
        if(!$request->description) {
            return redirect()->back()->with('error', 'وصف الخدمة مطلوب');
        }
        $service = Service::findOrFail($id);
        $service->description = $request->description;
        $service->save();
        return redirect()->back()->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function deleteService($id) {
        if(Gate::allows('حذف خدمة') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف الخدمة');
        }
        $service = Service::findOrFail($id);
        if($service->type == 'primary') {
            return redirect()->back()->with('error', 'لا يمكنك حذف هذه الخدمة');
        }
        $service->delete();
        return redirect()->back()->with('success', 'تم حذف الخدمة بنجاح');
    }

    public function attachFile(Request $request, Contract $contract) {
        if(Gate::allows('إرفاق مستند الى العقد') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإرفاق مستندات للعقد');
        }

        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments/contracts', $fileName, 'public');

            $contract->attachments()->create([
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'user_id' => Auth::user()->id,
            ]);
            
            return redirect()->back()->with('success', 'تم إرفاق الملف بنجاح');
        }

        return redirect()->back()->with('error', 'لم يتم إرفاق أي ملف');
    }

    public function deleteAttachment(Attachment $attachment) {
        if(Gate::allows('حذف مستند من العقد') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف المرفقات');
        }

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->back()->with('success', 'تم حذف المرفق بنجاح');
    }
}
