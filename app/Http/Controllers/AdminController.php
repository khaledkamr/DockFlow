<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerRequest;
use App\Http\Requests\ContainerTypesRequest;
use App\Http\Requests\ContractRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\RootRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\VoucherRequest;
use App\Models\Account;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\Contract_container;
use App\Models\invoice;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users() {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function userProfile($id) {
        $user = User::findOrFail($id);
        return view('admin.userProfile', compact('user'));
    }

    public function admins() {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.admins', compact('users'));
    }

    public function createUser(UserRequest $request) {
        $validated = $request->validated();
        User::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateUser(UserRequest $request, $id) {
        $user = User::findOrFail($id);
        $validated = $request->validated();
        $user->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteUser($id) {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();
        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }

    public function yard() {
        $containers = Container::orderBy('id', 'desc')->get();
        return view('admin.yard', compact('containers'));
    }

    public function yardAdd(Request $request) {
        $users = User::orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        $clientId = $request->input('user_id', null);
        $client = [
            'id' => '',
            'name' => '',
            'NID' => '',
            'phone' => '',
        ];
        
        if($clientId) {
            $user = User::find($clientId);
            $client = [
                'id' => $user->id,
                'name' => $user->name,
                'NID' => $user->NID,
                'phone' => $user->phone,
            ];
        }

        return view('admin.createContainer', compact('users', 'containerTypes', 'client'));
    }

    public function containerCreate(ContainerRequest $request) {
        $validated = $request->validated();
        Container::create($validated);
        return redirect()->back()->with('success', 'تم إضافة حاوية جديدة بنجاح');
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('admin.containersTypes', compact('containerTypes'));
    }

    public function containerTypeCreate(ContainerTypesRequest $request) {
        $validated = $request->validated();
        Container_type::create($validated);
        return redirect()->back()->with('success', 'تم إضافة نوع حاوية جديد بنجاح');
    }

    public function updateContainerType(ContainerTypesRequest $request, $id) {
        $containerType = Container_type::findOrFail($id);
        $validated = $request->validated();
        $containerType->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الفئــة بنجاح');
    }

    public function deleteContainerType($id) {
        $containerType = Container_type::findOrFail($id);
        $name = $containerType->name;
        $containerType->delete();
        return redirect()->back()->with('success', 'تم حذف ' . $name . ' بنجاح');
    }

    public function contracts() {
        $contracts = Contract::orderBy('id', 'desc')->get();
        $users = User::all();
        return view('admin.contracts', compact('contracts', 'users'));
    }

    public function createContract(Request $request) {
        $users = User::orderBy('name', 'asc')->get();
        $clientId = $request->input('user_id', null);
        $client = [
            'id' => '',
            'name' => '',
            'NID' => '',
            'phone' => '',
        ];
        $containers = [
            [
                'type' => 'فئة صغيرة',
                'price' => Container_type::where('name', 'فئة صغيرة')->value('daily_price'),
                'count' => 0
            ],
            [
                'type' => 'فئة متوسطة',
                'price' => Container_type::where('name', 'فئة متوسطة')->value('daily_price'),
                'count' => 0
            ],
            [
                'type' => 'فئة كبيرة',
                'price' => Container_type::where('name', 'فئة كبيرة')->value('daily_price'),
                'count' => 0
            ],
        ];
        $price = 0;
        if($clientId) {
            $user = User::find($clientId);
            $client = [
                'id' => $user->id,
                'name' => $user->name,
                'NID' => $user->NID,
                'phone' => $user->phone,
            ];
            $containers[0]['count'] = Container::where('user_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة صغيرة');
                                                })->count();
            $containers[1]['count'] = Container::where('user_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة متوسطة');
                                                })->count();
            $containers[2]['count'] = Container::where('user_id', $clientId)
                                                ->where('status', 'في الإنتظار')
                                                ->whereHas('containerType', function($query) {
                                                    $query->where('name', 'فئة كبيرة');
                                                })->count();
            foreach ($containers as $container) {
                $price += $container['count'] * $container['price'];
            }
        }

        return view('admin.createContract', compact('users', 'client', 'containers', 'price'));
    }

    public function storeContract(ContractRequest $request) {
        $validated = $request->validated();
        $days = Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['expected_end_date']));
        $validated['price'] = $validated['price'] * $days;

        $containers = Container::where('user_id', $validated['user_id'])
        ->where('status', 'في الإنتظار')->get();
        
        foreach($containers as $container) {
            $container->status = 'موجود';
            $container->save();
        }

        $contract = Contract::create($validated);
        $contract->containers()->attach($containers);
        return redirect()->back()->with('success', 'تم إنشاء عقد جديد بنجاح');
    }

    public function contractDetails($id) {
        $contract = Contract::with('containers.containerType')->findOrFail($id);
        $remainingDays = Carbon::now()->diffInDays(Carbon::parse($contract->expected_end_date));
        return view('admin.contractDetails', compact('contract', 'remainingDays'));
    }

    public function invoices() {
        $invoices = invoice::orderBy('id', 'desc')->get();
        return view('admin.invoices', compact('invoices'));
    }

    public function createInvoice(InvoiceRequest $request) {
        $validated = $request->validated();
        $contract = Contract::findOrFail($validated['contract_id']);
        $contract->status = 'منتهي';
        $contract->actual_end_date = $validated['date'];
        $contract->save();
        invoice::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء الفاتوره بنجاح');
    }

    public function exitPermission(Request $request) {
        $contract = Contract::findOrFail($request->contract_id);
        foreach($contract->containers as $container) {
            $container->status = 'غير متوفر';
            $container->save();
        }
        return redirect()->back()->with('success', 'تم إنشاء إذن خروج للحاويات');
    }

    public function tree() {
        $accounts = Account::where('parent_id', null)->get();
        return view('admin.tree', compact('accounts'));
    }

    public function createRoot(RootRequest $request) {
        $validated = $request->validated();
        $name = $validated['name'];
        Account::create($validated);
        return redirect()->back()->with('success', "تم إنشاء الفرع $name بنجاح");
    }

    public function entries() {
        $accounts = Account::where('level', 5)->get();
        $vouchers = Voucher::all();
        if(request()->query('view', 'قيود يومية') == 'قيود يومية') {

        }
        elseif(request()->query('view') == 'سند قبض نقدي') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'payment_cash';
            });
        }
        elseif(request()->query('view') == 'سند قبض بشيك') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'payment_cheque';
            });
        }
        elseif(request()->query('view') == 'سند صرف نقدي') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'receipt_cash';
            });
        }
        elseif(request()->query('view') == 'سند صرف بشيك') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'receipt_cheque';
            });
        }
        elseif(request()->query('view') == 'الصندوق') {
            $vouchers = $vouchers->filter(function($voucher) {
                return $voucher->type == 'receipt_cash' || $voucher->type == 'payment_cash';
            });
        }
        
        $sum = 0;
        return view('admin.entries', compact('accounts', 'vouchers', 'sum'));
    }

    public function createVoucher(VoucherRequest $request) {
        $code = $request->account_code;
        $validated = $request->validated();
        $account = Account::where('code', $code)->first();
        $validated['account_id'] = $account->id;
        Voucher::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء سند بنجاح');
    }

    public function deleteVoucher($id) {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return redirect()->back()->with('success', 'تم حذف السند بنجاح');
    }
}
