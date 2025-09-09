<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;
use App\Models\User;
use App\Models\Container_type;
use App\Http\Requests\ContainerRequest;
use App\Http\Requests\ContainerTypesRequest;
use App\Models\Customer;
use Carbon\Carbon;

class ContainerController extends Controller
{
    public function containers(Request $request) {
        $containers = Container::orderBy('id', 'desc')->get();
        $availableContainer = $containers->where('status', 'متوفر')->count();
        $lateContainers = $containers->where('status', 'متأخر')->count();
        $containerFilter = request()->query('status');
        if ($containerFilter && $containerFilter !== 'all') {
            $containers = $containers->filter(function ($container) use ($containerFilter) {
                return $container->status === $containerFilter;
            });
        }
        $search = $request->input('search', null);
        if($search) {
            $containers = $containers->filter(function($container) use($search) {
                return stripos($container->id, $search) !== false 
                    || stripos($container->code, $search) !== false 
                    || stripos($container->customer->name, $search) !== false
                    || stripos($container->location, $search) !== false;
            });
        }
        $containers = new \Illuminate\Pagination\LengthAwarePaginator(
            $containers->forPage(request()->get('page', 1), 50),
            $containers->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.containers.containers', compact(
            'containers',
            'availableContainer',
            'lateContainers',
        ));
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
        return view('admin.containers.createContainer', compact('customers', 'containerTypes', 'client'));
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

    public function containerUpdate(Request $request, $id) {
        $container = Container::findOrFail($id);
        $name = $container->code;
        $container->location = $request->location;
      
        $container->notes = $request->notes;
        $container->save();
        return redirect()->back()->with('success', "تم تعديل بيانات الحاوية '$name' بنجاح");
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('admin.containers.containersTypes', compact('containerTypes'));
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

    public function reports(Request $request) {
        $containers = Container::orderBy('id', 'desc')->get();
        $types = Container_type::all();
        $customers = Customer::all();

        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');
        $customer =$request->input('customer', 'all');

        if($to && $from) {
            $containers = $containers->whereBetween('date', [$from, $to]);
        }
        if($status !== 'all') {
            $containers = $containers->where('status', $status);
        }
        if($type !== 'all') {
            $containers = $containers->filter(function($container) use($type) {
                return $container->containerType->id == $type;
            });
        }
        if($customer !== 'all') {
            $containers = $containers->filter(function($container) use($customer) {
                return $container->customer->id == $customer;
            });
        }
        $perPage = $request->input('per_page', 10);
        $containers = new \Illuminate\Pagination\LengthAwarePaginator(
            $containers->forPage(request()->get('page', 1), $perPage),
            $containers->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.containers.reports', compact(
            'containers',
            'types',
            'customers',
            'perPage'
        ));
    }

    public function addService(Request $request, $id) {
        $container = Container::findOrFail($id);
        $serviceId = $request->input('service_id');
        $price = $request->input('price', 0);
        $notes = $request->input('notes', '');
        if($container->services->contains($serviceId)) {
            return redirect()->back()->with('error', 'هذه الخدمة مضافة مسبقاً لهذه الحاوية');
        }
        $container->services()->attach($serviceId, [
            'price' => $price,
            'notes' => $notes
        ]);
        return redirect()->back()->with('success', 'تم إضافة الخدمة الى الحاوية بنجاح');
    }

    public function containerDetails($id) {
        $container = Container::with(['customer', 'policies', 'invoices', 'services'])->findOrFail($id);
        return view('admin.containers.containerDetails', compact('container'));
    }
}
