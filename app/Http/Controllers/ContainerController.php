<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;
use App\Models\User;
use App\Models\Container_type;
use App\Http\Requests\ContainerRequest;
use App\Http\Requests\ContainerTypesRequest;
use App\Models\Customer;

class ContainerController extends Controller
{
    public function containers() {
        $containers = Container::orderBy('id', 'desc')->get();
        return view('admin.containers', compact('containers'));
    }

    public function createContainer(Request $request) {
        $customers = Customer::orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        $clientId = $request->input('customer_id', null);
        $client = [
            'id' => '',  'name' => '',
            'CR' => '',  'phone' => '',
        ];
        if($clientId) {
            $customer = Customer::find($clientId);
            $client = [
                'id' => $customer->id,  'name' => $customer->name,
                'CR' => $customer->CR,  'phone' => $customer->phone,
            ];
        }
        return view('admin.createContainer', compact('customers', 'containerTypes', 'client'));
    }

    public function containerStore(Request $request) {
        foreach($request->containers as $container) {
            Container::create([
                'customer_id' => $request->customer_id,
                'code' => $container['code'],
                'container_type_id' => $container['container_type_id'],
                'status' => $container['status'],
                'location' => $container['location'] 
            ]);
        }
        $count = count($request->containers);
        return redirect()->back()->with('success', "تم إضافة $count حاويات جديدة بنجاح");
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('admin.containersTypes', compact('containerTypes'));
    }

    public function containerTypeStore(ContainerTypesRequest $request) {
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
}
