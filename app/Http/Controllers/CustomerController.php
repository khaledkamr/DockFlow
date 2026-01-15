<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    public function customers(Request $request) {
        $customers = Customer::query();
        $search = $request->input('search', null);

        if($search) {
            $customers->where(function($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('account', function($q) use ($search) {
                          $q->where('code', 'LIKE', '%' . $search . '%');
                      });
            });
        }

        $customers = $customers->with('account')->orderBy('created_at', 'desc')->paginate(100)->onEachSide(1)->withQueryString();

        return view('pages.customers.customers', compact('customers'));
    }

    public function customerProfile(Customer $customer) {
        return view('pages.customers.customer_profile', compact('customer'));
    }

    public function storeCustomer(CustomerRequest $request) {
        if(Gate::allows('إضافة عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية إنشاء عميل');
        }
        if(Account::where('name', 'عملاء التشغيل')->doesntExist()) {
            return redirect()->back()->with('error', 'يرجى إنشاء حساب عملاء التشغيل أولاً من شاشة الحسابات');
        }
        
        $accountId = Account::where('name', 'عملاء التشغيل')->first()->id;
        $lastCustomer = Account::where('parent_id', $accountId)->latest('code')->first();

        if($lastCustomer) {
            $code = (string)((int)($lastCustomer->code) + 1);
        } else {
            $code = Account::where('id', $accountId)->latest('id')->first()->code;
            $code = $code . '0001';
        }
        $account = Account::create([
            'name' => $request->name,
            'code' => $code,
            'parent_id' => $accountId,
            'type_id' => 1,
            'level' => 5
        ]);
        logActivity('إنشاء حساب', "تم إنشاء حساب جديد في دليل الحسابات بإسم: " . $request->name . " برقم: " . $code);

        $validated = $request->validated();
        $validated['account_id'] = $account->id;

        $new = Customer::create($validated);
        logActivity('إنشاء عميل', "تم إنشاء عميل جديد: " . $request->name, null, $new->toArray());
        
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateCustomer(CustomerRequest $request, Customer $customer) {
        if(Gate::allows('تعديل عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل بيانات عميل');
        }
        $old = $customer->toArray();

        $validated = $request->validated();
        $customer->update($validated);

        $new = $customer->toArray();
        logActivity('تعديل عميل', "تم تعديل بيانات العميل: " . $request->name, $old, $new);

        $customer->account()->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteCustomer(Customer $customer) {
        if(Gate::allows('حذف عميل') == false) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف عميل');
        }
        if($customer->invoices()->exists() || $customer->contract()->exists() || $customer->containers()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا العميل لوجود تعاملات مرتبطة به');
        }

        $name = $customer->name;
        $old = $customer->toArray();
        $customer->delete();
        logActivity('حذف عميل', "تم حذف العميل: " . $name, $old, null);

        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }

    public function getContract($id) {
        $customer = Customer::with('contract.services')->findOrFail($id);

        if(!$customer || !$customer->contract) {
            return response()->json(['contract' => null]);
        }

        $contract = $customer->contract;
        $storageService = $contract?->services->where('description', 'خدمة تخزين الحاوية الواحدة في ساحتنا')->first();
        $lateService = $contract?->services->where('description', 'خدمة تخزين الحاوية بعد المدة المتفق عليها')->first();
        $late_Service_20ft = $contract?->services->where('description', 'خدمة تخزين حاوية 20 قدم بعد المدة المتفق عليها')->first();
        $late_Service_40ft = $contract?->services->where('description', 'خدمة تخزين حاوية 40 قدم بعد المدة المتفق عليها')->first();

        return response()->json([
            'contract' => $customer->contract,
            'storage_price' => $storageService ? $storageService->pivot->price : null,
            'storage_duration' => $storageService ? $storageService->pivot->unit : null,
            'late_fee' => $lateService ? $lateService->pivot->price : null,
            'late_fee_20ft' => $late_Service_20ft ? $late_Service_20ft->pivot->price : null,
            'late_fee_40ft' => $late_Service_40ft ? $late_Service_40ft->pivot->price : null,
        ]);
    }

    public function checkService(Request $request) {
        $customer = Customer::with('contract.services')->findOrFail($request->customer_id);
        $serviceDescription = null;
        
        if($request->pickup == 'الميناء' && $request->dropoff == 'الساحة') {
            $serviceDescription = 'نقل الحاوية من الميناء الى الساحة';
        }
        $service = $customer->contract?->services->where('description', $serviceDescription)->first();

        if($service) {
            return response()->json([
                'exists' => true,
                'price' => $service->pivot->price,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function getCustomerInvoicesByAccount($accountId) {
        try {
            // Find the account
            $account = Account::find($accountId);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحساب غير موجود',
                    'invoices' => []
                ], 404);
            }

            // Get the customer associated with this account
            $customer = $account->customer;
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد عميل مرتبط بهذا الحساب',
                    'invoices' => []
                ], 404);
            }

            // Get all invoices for this customer
            $invoices = $customer->invoices()
                ->where('isPaid' , '!=', 'تم الدفع')
                // ->where('is_posted', true)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($invoice) {
                    return [
                        'uuid' => $invoice->uuid,
                        'id' => $invoice->id,
                        'code' => $invoice->code,
                        'type' => $invoice->type,
                        'date' => $invoice->date,
                        'amount_before_tax' => $invoice->amount_before_tax,
                        'tax' => $invoice->tax,
                        'total_amount' => $invoice->total_amount,
                        'payment_method' => $invoice->payment_method,
                        'isPaid' => $invoice->isPaid,
                        'is_posted' => $invoice->is_posted,
                    ];
                });

            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ],
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الفواتير',
                'error' => $e->getMessage(),
                'invoices' => []
            ], 500);
        }
    }
}

