<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
}
