<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRequest;
use App\Http\Requests\TransportRequest;
use App\Http\Requests\VehicleRequest;
use App\Models\Account;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\CostCenter;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Places;
use App\Models\ShippingPolicy;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\Vehicle;
use App\Models\Supplier;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class TransportController extends Controller
{
    public function transportOrders(Request $request) {
        $transportOrders = TransportOrder::orderBy('code', 'desc')->get();

        $search = $request->input('search', null);
        if ($search) {
            $transportOrders = $transportOrders->filter(function ($order) use ($search) {
                $matchCode = stripos($order->code, $search) !== false;
                $matchTransaction = stripos($order->transaction->code, $search) !== false;
                $matchCustomer = stripos($order->customer->name, $search) !== false;
                $matchDate = stripos($order->date, $search) !== false;
                $matchContainer = $order->containers->contains(function ($container) use ($search) {
                    return stripos($container->code, $search) !== false;
                });

                return $matchCode || $matchTransaction || $matchCustomer || $matchDate || $matchContainer;
            });
        }

        $transportOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $transportOrders->forPage(request()->get('page', 1), 100),
            $transportOrders->count(),
            100,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.transportOrders.transport_orders', compact('transportOrders'));
    }

    public function createTransportOrder() {
        $transactions = Transaction::where('status', 'معلقة')->with('containers.transportOrders')->get();
        $drivers = Driver::all();
        $suppliers = Supplier::all();
        $destinations = Places::all();

        return view('pages.transportOrders.create_transport_order', compact(
            'transactions', 
            'drivers', 
            'suppliers',
            'destinations'
        ));
    }

    public function storeTransportOrder(TransportRequest $request) {
        $validated = $request->validated();
        $transportOrder = TransportOrder::create($validated);

        $containers = Container::whereIn('id', $request->selected_containers)->get();
        foreach($containers as $container) {
            $container->status = 'قيد النقل';
            $container->save();
        }
        $transportOrder->containers()->attach($containers);
        $transaction = Transaction::find($transportOrder->transaction_id);
        
        $new = $transportOrder->load('containers')->toArray();
        logActivity('إنشاء إشعار نقل', "تم إنشاء إشعار نقل جديد برقم " . $transportOrder->code . " للمعاملة رقم " . $transaction->code, null, $new);

        $isFullyTransported = true;
        foreach($transaction->containers as $container) {
            if($container->transportOrders->isEmpty()) {
                $isFullyTransported = false;
                break;
            }
        }

        if($isFullyTransported) {
            $transaction->status = 'مغلقة';
            $transaction->save();
            logActivity('تحديث حالة المعاملة', "تم تحديث حالة المعاملة رقم " . $transaction->code . " إلى مغلقة بعد إنشاء إشعار النقل رقم " . $transportOrder->code);
        }

        if($transportOrder->type == 'ناقل خارجي') {
            $supplierAccount = $transportOrder->supplier ? $transportOrder->supplier->account : null;
            $creditAccount = Account::where('name', 'ايجار شاحنات')->where('level', 5)->first();

            $journal = JournalEntry::create([
                'type' => 'قيد يومي',
                'date' => $transportOrder->date,
                'totalDebit' => $transportOrder->supplier_cost,
                'totalCredit' => $transportOrder->supplier_cost,
                'user_id' => $transportOrder->user_id
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $supplierAccount ? $supplierAccount->id : null,
                'debit' => 0,
                'credit' => $transportOrder->supplier_cost,
                'description' => 'اشعار نقل رقم '.$transportOrder->code
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $creditAccount ? $creditAccount->id : null,
                'debit' => $transportOrder->supplier_cost,
                'credit' => 0,
                'description' => 'اشعار نقل رقم '.$transportOrder->code
            ]);

            $new = $journal->load('lines')->toArray();
            logActivity('إنشاء قيد', "تم إنشاء قيد يومي برقم " . $journal->code . "من اشعار نقل رقم " . $transportOrder->code, null, $new);
        }

        if($transportOrder->from && $transportOrder->to) {
            Places::firstOrCreate([
                'name' => $transportOrder->from,
            ]);
            Places::firstOrCreate([
                'name' => $transportOrder->to,
            ]);
        }

        return redirect()->back()->with('success', 'تم إنشاء إشعار نقل جديد, <a class="text-white fw-bold" href="'.route('transactions.transportOrders.details', $transportOrder).'">عرض الإشعار؟</a>');
    }

    public function updateTransportOrder(Request $request, TransportOrder $transportOrder) {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'type' => 'required|string',
            'driver_id' => 'required_if:type,ناقل داخلي|nullable|exists:drivers,id',
            'vehicle_id' => 'required_if:type,ناقل داخلي|nullable|exists:vehicles,id',
            'supplier_id' => 'required_if:type,ناقل خارجي|nullable|exists:suppliers,id',
            'driver_name' => 'nullable|string',
            'driver_contact' => 'nullable|string',
            'vehicle_plate' => 'nullable|string',
            'client_cost' => 'required|numeric|min:0',
            'supplier_cost' => 'nullable|numeric|min:0',
            'diesel_cost' => 'nullable|numeric|min:0',
            'driver_wage' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
        ]);

        $old = $transportOrder->toArray();
        $transportOrder->update($validated);
        $new = $transportOrder->toArray();
        logActivity('تحديث اشعار النقل', "تم تحديث بيانات اشعار النقل رقم " . $transportOrder->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات اشعار النقل بنجاح');
    }

    public function updateNotes(Request $request, TransportOrder $transportOrder) {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $old = $transportOrder->toArray();
        $transportOrder->update($request->only('notes'));
        $new = $transportOrder->toArray();
        logActivity('تحديث ملاحظات اشعار النقل', "تم تحديث ملاحظات اشعار النقل رقم " . $transportOrder->code, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات اشعار النقل بنجاح');
    }

    public function toggleReceiveStatus(TransportOrder $transportOrder) {
        $transportOrder->is_received = !$transportOrder->is_received;
        $transportOrder->save();

        $container = $transportOrder->containers()->first();
        if ($transportOrder->is_received) {
            $container->status = 'تم التسليم';
        } else {
            $container->status = 'قيد النقل';
        }
        $container->save();

        logActivity('تحديث حالة تسليم اشعار النقل', "تم تحديث حالة التسليم لاشعار النقل برقم " . $transportOrder->code . " إلى " . ($transportOrder->is_received ? 'تم التسليم' : 'في الانتظار'));
        return redirect()->back()->with('success', 'تم تحديث حالة التسليم بنجاح');
    }

    public function transportOrderDetails(TransportOrder $transportOrder) {
        $transportOrder->load('driver', 'vehicle', 'containers.containerType');
        $drivers = Driver::all();
        $suppliers = Supplier::all();

        return view('pages.transportOrders.transport_order_details', compact(
            'transportOrder',
            'drivers',
            'suppliers'
        ));
    }

    public function driversAndVehicles(Request $request) {
        $view = $request->query('view', 'السائقين');
        $drivers = Driver::with('vehicle')->get();
        $vehicles = Vehicle::with('driver')->get();
        $destinations = Places::all();

        $vehiclesWithoutDriver = $vehicles->filter(function($vehicle) {
            return $vehicle->driver == null;
        });

        if($view == 'السائقين' && $search = request()->query('search', '')) {
            $drivers = $drivers->filter(function($driver) use ($search) {
                return str_contains(strtolower($driver->name), strtolower($search)) 
                    || str_contains(strtolower($driver->NID), strtolower($search)) 
                    || ($driver->vehicle && str_contains(strtolower($driver->vehicle->plate_number), strtolower($search)));
            });
        } elseif($view == 'الشاحنات' && $search = request()->query('search', '')) {
            $vehicles = $vehicles->filter(function($vehicle) use ($search) {
                return str_contains(strtolower($vehicle->plate_number), strtolower($search)) 
                    || str_contains(strtolower($vehicle->type), strtolower($search)) 
                    || ($vehicle->driver && str_contains(strtolower($vehicle->driver->name), strtolower($search)));
            });
        }

        return view('pages.transportOrders.drivers_and_vehicles', compact(
            'view', 
            'drivers', 
            'vehicles', 
            'vehiclesWithoutDriver',
            'destinations'
        ));
    }

    public function storeDriver(DriverRequest $request) {
        $parentCostCenter = CostCenter::where('name', 'السائقون')->first();
        $lastChildCostCenter = CostCenter::where('parent_id', $parentCostCenter->id)->latest('code')->first();

        if($lastChildCostCenter) {
            $code = (string)((int)($lastChildCostCenter->code) + 1);
        } else {
            $code = $parentCostCenter->code . '0001';
        }

        $costCenter = CostCenter::create([
            'name' => $request->name,
            'code' => $code,
            'parent_id' => $parentCostCenter->id,
            'level' => $parentCostCenter->level + 1,
        ]);

        $validated = $request->validated();
        $validated['cost_center_id'] = $costCenter->id;
        $new = Driver::create($validated);
        logActivity('إنشاء سائق', "تم إنشاء سائق جديد باسم " . $new->name, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إضافة سائق جديد بنجاح');
    }

    public function updateDriver(DriverRequest $request, Driver $driver) {
        $old = $driver->toArray();

        $validated = $request->validated();
        $driver->update($validated);

        $new = $driver->toArray();
        logActivity('تعديل سائق', "تم تعديل بيانات السائق باسم " . $driver->name, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات السائق بنجاح');
    }

    public function deleteDriver(Driver $driver) {
        if($driver->transportOrders()->exists() || $driver->shippingPolicies()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا السائق لوجود بيانات مرتبطة به.');
        }

        $old = $driver->toArray();
        $driver->delete();
        logActivity('حذف سائق', "تم حذف السائق باسم " . $driver->name, $old, null);

        return redirect()->back()->with('success', 'تم حذف السائق بنجاح');
    }

    public function storeVehicle(VehicleRequest $request) {
        $parentCostCenter = CostCenter::where('name', 'السيارات')->first();
        $lastChildCostCenter = CostCenter::where('parent_id', $parentCostCenter->id)->latest('code')->first();

        if($lastChildCostCenter) {
            $code = (string)((int)($lastChildCostCenter->code) + 1);
        } else {
            $code = $parentCostCenter->code . '0001';
        }

        $costCenter = CostCenter::create([
            'name' => $request->type . ' - ' . $request->plate_number,
            'code' => $code,
            'parent_id' => $parentCostCenter->id,
            'level' => $parentCostCenter->level + 1,
        ]);

        $validated = $request->validated();
        $validated['cost_center_id'] = $costCenter->id;
        $new = Vehicle::create($validated);
        logActivity('إنشاء شاحنة', "تم إنشاء شاحنة جديدة برقم اللوحة " . $new->plate_number, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إضافة شاحنة جديدة بنجاح');
    }

    public function updateVehicle(VehicleRequest $request, Vehicle $vehicle) {
        $old = $vehicle->toArray();
        $validated = $request->validated();
        $vehicle->update($validated);
        $new = $vehicle->toArray();
        logActivity('تعديل شاحنة', "تم تعديل بيانات الشاحنة برقم اللوحة " . $vehicle->plate_number, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات الشاحنة بنجاح');
    }

    public function deleteVehicle(Vehicle $vehicle) {
        if($vehicle->transportOrders()->exists() || $vehicle->shippingPolicies()->exists() || $vehicle->driver) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذه الشاحنة لوجود بيانات مرتبطة بها.');
        }

        $old = $vehicle->toArray();
        $vehicle->delete();
        logActivity('حذف شاحنة', "تم حذف الشاحنة برقم اللوحة " . $vehicle->plate_number, $old, null);

        return redirect()->back()->with('success', 'تم حذف الشاحنة بنجاح');
    }

    public function storeDestination(Request $request) {
        $request->validate([
            'name' => 'required|string|unique:places,name',
        ]);

        $new = Places::create([
            'name' => $request->name,
        ]);
        logActivity('إنشاء موقع', "تم إنشاء موقع جديد باسم " . $new->name);

        return redirect()->back()->with('success', 'تم إضافة موقع جديد بنجاح');
    }

    public function updateDestination(Request $request, Places $destination) {
        $request->validate([
            'name' => 'required|string|unique:places,name,'.$destination->id,
        ]);

        $destination->update([
            'name' => $request->name,
        ]);
        logActivity('تعديل موقع', "تم تعديل اسم الموقع باسم " . $destination->name . "الى " . $request->name);

        return redirect()->back()->with('success', 'تم تحديث بيانات الموقع بنجاح');
    }

    public function deleteDestination(Places $destination) {
        $destination->delete();
        logActivity('حذف موقع', "تم حذف الموقع باسم " . $destination->name);

        return redirect()->back()->with('success', 'تم حذف الموقع بنجاح');
    }

    public function reports(Request $request) {
        $transportOrders = TransportOrder::orderBy('date')->get();
        $customers = Customer::all();
        $drivers = Driver::with('vehicle')->get();
        $vehicles = Vehicle::all();
        $suppliers = Supplier::all();
        $loadingLocations = $transportOrders->pluck('from')->unique();
        $deliveryLocations = $transportOrders->pluck('to')->unique();

        $customer = $request->input('customer', 'all');
        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $type = $request->input('type', 'all');
        $supplier = $request->input('supplier', 'all');
        $driver = $request->input('driver', 'all');
        $vehicle = $request->input('vehicle', 'all');
        $loading_location = $request->input('loading_location', 'all');
        $delivery_location = $request->input('delivery_location', 'all');

        if($customer && $customer != 'all') {
            $transportOrders = $transportOrders->where('customer_id', $customer);
        }
        if($from) {
            $transportOrders = $transportOrders->where('date', '>=', $from);
        }
        if($to) {
            $transportOrders = $transportOrders->where('date', '<=', $to);
        }
        if($type && $type != 'all') {
            $transportOrders = $transportOrders->where('type', $type);
        }
        if($supplier && $supplier != 'all') {
            $transportOrders = $transportOrders->where('supplier_id', $supplier);
        }
        if($driver && $driver != 'all') {
            $transportOrders = $transportOrders->where('driver_id', $driver);
        }
        if($vehicle && $vehicle != 'all') {
            $transportOrders = $transportOrders->where('vehicle_id', $vehicle);
        }
        if($loading_location && $loading_location != 'all') {
            $transportOrders = $transportOrders->where('from', $loading_location);
        }
        if($delivery_location && $delivery_location != 'all') {
            $transportOrders = $transportOrders->where('to', $delivery_location);
        }

        $perPage = $request->input('per_page', 100);
        $transportOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $transportOrders->forPage(request()->get('page', 1), $perPage),
            $transportOrders->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('pages.transportOrders.reports', compact(
            'transportOrders',
            'customers',
            'drivers',
            'vehicles',
            'suppliers',
            'loadingLocations',
            'deliveryLocations',
            'perPage'
        ));
    }
}
