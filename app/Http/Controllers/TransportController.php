<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Container_type;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function transportOrders() {
        $transportOrders = TransportOrder::all();
        return view('pages.transportOrders.transportOrders', compact('transportOrders'));
    }

    public function createTransportOrder() {
        $transactions = Transaction::all();
        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('pages.transportOrders.createTransportOrder', compact('transactions', 'drivers', 'vehicles'));
    }

    public function storeTransportOrder(Request $request) {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'code' => 'required|unique:transport_orders,code',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'diesel_cost' => 'nullable|numeric|min:0',
            'driver_wage' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
        ]);

        $transportOrder = TransportOrder::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء إشعار نقل جديد, <a class="text-white fw-bold" href="'.route('transportOrders.details', $transportOrder).'">عرض الإشعار؟</a>');
    }

    public function transportOrderDetails(TransportOrder $transportOrder) {
        return view('pages.transportOrders.transportOrderDetails', compact('transportOrder'));
    }
}
