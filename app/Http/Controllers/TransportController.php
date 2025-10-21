<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportRequest;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\Vehicle;
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
        $transactions = Transaction::with('containers.transportOrders')->get();
        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('pages.transportOrders.createTransportOrder', compact('transactions', 'drivers', 'vehicles'));
    }

    public function storeTransportOrder(TransportRequest $request) {
        if($request->driver_id == null) {
            $driver = Driver::create([
                'name' => $request->driver_name,
                'NID' => $request->driver_NID,
            ]);
            $validated['driver_id'] = $driver->id;
        }
        if($request->vehicle_id == null) {
            $vehicle = Vehicle::create([
                'plate_number' => $request->vehicle_plate_number,
                'type' => $request->vehicle_type,
            ]);
            $validated['vehicle_id'] = $vehicle->id;
        }

        $validated = $request->validated();
        $transportOrder = TransportOrder::create($validated);

        $containers = Container::whereIn('id', $request->selected_containers)->get();
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

        return redirect()->back()->with('success', 'تم إنشاء إشعار نقل جديد, <a class="text-white fw-bold" href="'.route('transactions.transportOrders.details', $transportOrder).'">عرض الإشعار؟</a>');
    }

    public function transportOrderDetails(TransportOrder $transportOrder) {
        $transportOrder->load('driver', 'vehicle', 'containers.containerType');
        return view('pages.transportOrders.transportOrderDetails', compact('transportOrder'));
    }
}
