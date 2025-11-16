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
use Illuminate\Support\Facades\Gate;

class ContainerController extends Controller
{
    public function containers(Request $request) {
        $containers = Container::orderBy('id', 'desc')->get();
        
        $containerFilter = request()->query('status');
        if ($containerFilter && $containerFilter !== 'all') {
            if($containerFilter === 'متأخر') {
                $containers = $containers->filter(function ($container) {
                    if($container->status !== 'في الساحة') {
                        return false;
                    }
                    $storagePolicy = $container->policies->where('type', 'تخزين')->first();
                    if(!$storagePolicy) {
                        return false;
                    }
                    $dueDays = $storagePolicy->storage_duration;
                    return $container->days > $dueDays;
                });
            } else {
                $containers = $containers->filter(function ($container) use ($containerFilter) {
                    return $container->status === $containerFilter;
                });
            }
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

        return view('pages.containers.containers', compact('containers'));
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

    public function containerUpdate(Request $request, Container $container) {
        if(Gate::denies('تعديل حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية تعديل بيانات الحاوية');
        }

        $container->code = $request->code;
        $container->location = $request->location;
        $container->notes = $request->notes;
        $container->save();
        
        return redirect()->back()->with('success', "تم تعديل بيانات الحاوية بنجاح");
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('pages.containers.containersTypes', compact('containerTypes'));
    }

    public function containerTypeStore(ContainerTypesRequest $request) {
        if(Gate::denies('إضافة نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإضافة نوع حاوية');
        }
        $validated = $request->validated();
        Container_type::create($validated);
        return redirect()->back()->with('success', 'تم إضافة نوع حاوية جديد بنجاح');
    }

    public function updateContainerType(ContainerTypesRequest $request, $id) {
        if(Gate::denies('تعديل نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل نوع حاوية');
        }
        $containerType = Container_type::findOrFail($id);
        $validated = $request->validated();
        $containerType->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الفئــة بنجاح');
    }

    public function deleteContainerType($id) {
        if(Gate::denies('حذف نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف نوع حاوية');
        }
        $containerType = Container_type::findOrFail($id);
        if($containerType->containers()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا النوع لوجود حاويات مرتبطة به');
        }
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
        $perPage = $request->input('per_page', 100);
        $containers = new \Illuminate\Pagination\LengthAwarePaginator(
            $containers->forPage(request()->get('page', 1), $perPage),
            $containers->count(),
            $perPage,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('pages.containers.reports', compact(
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

    public function containerDetails(Container $container) {
        $transaction = $container->transactions->first();
        $transportOrder = $container->transportOrders->first();
        $storagePolicy = $container->policies->where('type', 'تخزين')->first();
        $receivePolicy = $container->policies->where('type', 'تسليم')->first();

        $clearanceInvoice = $container->invoices->where('type', 'تخليص')->first();
        $storageInvoice = $container->invoices->where('type', 'تخزين')->first();
        $serviceInvoice = $container->invoices->where('type', 'خدمات')->first();

        return view('pages.containers.containerDetails', compact(
            'container', 
            'transportOrder', 
            'transaction', 
            'storagePolicy', 
            'receivePolicy', 
            'clearanceInvoice',
            'storageInvoice',
            'serviceInvoice'
        ));
    }
}
