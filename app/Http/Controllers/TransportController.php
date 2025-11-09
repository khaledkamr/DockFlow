<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRequest;
use App\Http\Requests\TransportRequest;
use App\Http\Requests\VehicleRequest;
use App\Models\Account;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\Vehicle;
use App\Models\Supplier;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class TransportController extends Controller
{
    public function transportOrders(Request $request) {
        $transportOrders = TransportOrder::all();

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

        return view('pages.transportOrders.transportOrders', compact('transportOrders'));
    }

    public function createTransportOrder() {
        $transactions = Transaction::where('status', 'معلقة')->with('containers.transportOrders')->get();
        $drivers = Driver::all();
        $suppliers = Supplier::all();

        return view('pages.transportOrders.createTransportOrder', compact(
            'transactions', 
            'drivers', 
            'suppliers'
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
                'description' => 'بوليصة شحن رقم '.$transportOrder->code
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $creditAccount ? $creditAccount->id : null,
                'debit' => $transportOrder->supplier_cost,
                'credit' => 0,
                'description' => 'بوليصة شحن رقم '.$transportOrder->code
            ]);
        }

        return redirect()->back()->with('success', 'تم إنشاء إشعار نقل جديد, <a class="text-white fw-bold" href="'.route('transactions.transportOrders.details', $transportOrder).'">عرض الإشعار؟</a>');
    }

    public function updateNotes(Request $request, TransportOrder $transportOrder) {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $transportOrder->update($request->only('notes'));

        return redirect()->back()->with('success', 'تم تحديث بيانات اشعار النقل بنجاح');
    }

    public function transportOrderDetails(TransportOrder $transportOrder) {
        $transportOrder->load('driver', 'vehicle', 'containers.containerType');
        return view('pages.transportOrders.transportOrderDetails', compact('transportOrder'));
    }

    public function driversAndVehicles(Request $request) {
        $view = $request->query('view', 'السائقين');
        $drivers = Driver::with('vehicle')->get();
        $vehicles = Vehicle::with('driver')->get();

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

        return view('pages.transportOrders.driversAndVehicles', compact(
            'view', 
            'drivers', 
            'vehicles', 
            'vehiclesWithoutDriver'
        ));
    }

    public function storeDriver(DriverRequest $request) {
        $validated = $request->validated();
        Driver::create($validated);

        return redirect()->back()->with('success', 'تم إضافة سائق جديد بنجاح');
    }

    public function updateDriver(DriverRequest $request, Driver $driver) {
        $validated = $request->validated();
        $driver->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات السائق بنجاح');
    }

    public function deleteDriver(Driver $driver) {

    }

    public function storeVehicle(VehicleRequest $request) {
        $validated = $request->validated();
        Vehicle::create($validated);

        return redirect()->back()->with('success', 'تم إضافة شاحنة جديدة بنجاح');
    }

    public function updateVehicle(Request $request, Vehicle $vehicle) {

    }

    public function deleteVehicle(Vehicle $vehicle) {

    }
}
