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
        $containerTypes = Container_type::all();
        $containerFilter = request()->query('status');
        $search = $request->input('search', null);

        $existingContainers = Container::where('status', 'في الساحة')->count();
        $deliveredContainers = Container::where('status', 'تم التسليم')->count();
        $lateContainers = Container::where('status', 'في الساحة')
            ->whereHas('policies', function($query) {
                $query->where('type', 'تخزين');
            })
            ->get()
            ->filter(function ($container) {
                $storagePolicy = $container->policies->where('type', 'تخزين')->first();
                if(!$storagePolicy) {
                    return false;
                }
                $dueDays = $storagePolicy->storage_duration;
                return $container->days > $dueDays;
            })->count();

        $containers = Container::query();
        
        if ($containerFilter && $containerFilter !== 'all') {
            if($containerFilter === 'متأخر') {
                $containers->where('status', 'في الساحة')
                    ->whereHas('policies', function ($q) {
                        $q->where('type', 'تخزين')
                            ->whereRaw('DATEDIFF(NOW(), containers.date) > policies.storage_duration');
                    });
            } else {
                $containers->where('status', $containerFilter);
            }
        }
        
        if($search) {
            $containers->where(function($query) use ($search) {
                $query->where('code', 'like', "%$search%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })->orWhere('location', 'like', "%$search%");
            });
        }

        $containers = $containers->with(['customer', 'containerType'])->orderBy('date', 'desc')->paginate(100)->withQueryString();

        return view('pages.containers.containers', compact(
            'existingContainers',
            'lateContainers', 
            'deliveredContainers',
            'containers', 
            'containerTypes',
        ));
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

        $old = $container->toArray();

        $container->code = $request->code;
        $container->location = $request->location;
        $container->container_type_id = $request->container_type_id;
        $container->notes = $request->notes;
        $container->save();

        $new = $container->toArray();
        logActivity('تعديل حاوية', "تم تعديل بيانات الحاوية رقم " . $container->code, $old, $new);
        
        return redirect()->back()->with('success', "تم تعديل بيانات الحاوية بنجاح");
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('pages.containers.containers_types', compact('containerTypes'));
    }
    
    public function containerDetails(Container $container) {
        $transaction = $container->transactions->first();
        $transportOrder = $container->transportOrders->first();
        $storagePolicy = $container->policies->where('type', 'تخزين')->first();
        $receivePolicy = $container->policies->where('type', 'تسليم')->first();
        $servicePolicy = $container->policies->where('type', 'خدمات')->first();

        $clearanceInvoice = $container->invoices->where('type', 'تخليص')->first();
        $storageInvoice = $container->invoices->where('type', 'تخزين')->first();
        $serviceInvoice = $container->invoices->where('type', 'خدمات')->first();

        return view('pages.containers.container_details', compact(
            'container', 
            'transportOrder', 
            'transaction', 
            'storagePolicy', 
            'receivePolicy', 
            'servicePolicy',
            'clearanceInvoice',
            'storageInvoice',
            'serviceInvoice'
        ));
    }

    public function deleteContainer(Container $container) {
        $old = $container->toArray();
        $code = $container->code;

        $container->services()->detach();
        $container->invoices()->detach();
        $container->policies()->detach();
        $container->transactions()->detach();
        $container->transportOrders()->detach();
        $container->delete();

        logActivity('حذف حاوية', "تم حذف الحاوية رقم " . $code, $old, null);

        return redirect()->route('yard.containers')->with('success', "تم حذف الحاوية رقم " . $code . " بنجاح");
    }

    public function containerTypeStore(ContainerTypesRequest $request) {
        if(Gate::denies('إضافة نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لإضافة نوع حاوية');
        }

        $validated = $request->validated();
        $new = Container_type::create($validated);
        logActivity('إضافة نوع حاوية', "تم إضافة نوع حاوية جديد " . $new->name, null, $new->toArray());
        
        return redirect()->back()->with('success', 'تم إضافة نوع حاوية جديد بنجاح');
    }

    public function updateContainerType(ContainerTypesRequest $request, $id) {
        if(Gate::denies('تعديل نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لتعديل نوع حاوية');
        }
        $containerType = Container_type::findOrFail($id);
        $old = $containerType->toArray();
        $validated = $request->validated();
        $containerType->update($validated);

        $new = $containerType->toArray();
        logActivity('تعديل نوع حاوية', "تم تعديل نوع الحاوية " . $containerType->name, $old, $new);

        return redirect()->back()->with('success', 'تم تحديث بيانات الفئــة بنجاح');
    }

    public function deleteContainerType($id) {
        if(Gate::denies('حذف نوع حاوية')) {
            return redirect()->back()->with('error', 'ليس لديك الصلاحية لحذف نوع حاوية');
        }
        $containerType = Container_type::findOrFail($id);
        $old = $containerType->toArray();

        if($containerType->containers()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا النوع لوجود حاويات مرتبطة به');
        }
        $name = $containerType->name;
        $containerType->delete();

        logActivity('حذف نوع حاوية', "تم حذف نوع الحاوية " . $name, $old, null);

        return redirect()->back()->with('success', 'تم حذف ' . $name . ' بنجاح');
    }

    public function reports(Request $request) {
        $containers = Container::query();
        $types = Container_type::all();
        $customers = Customer::all();

        $from = $request->input('from', null);
        $to = $request->input('to', null);
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');
        $customer = $request->input('customer', 'all');
        $invoiced = $request->input('invoiced', 'all');
        $perPage = $request->input('per_page', 100);

        if($to && $from) {
            $containers->whereBetween('date', [$from, $to]);
        }
        if($status !== 'all') {
            if($status == 'متأخر') {
                $containers->where('status', 'في الساحة')
                    ->whereHas('policies', function ($q) {
                        $q->where('type', 'تخزين')
                            ->whereRaw('DATEDIFF(NOW(), containers.date) > policies.storage_duration');
                    });
            } else {
                $containers->where('status', $status);
            }
        }
        if($type !== 'all') {
            $containers->where('container_type_id', $type);
        }
        if($customer !== 'all') {
            $containers->where('customer_id', $customer);
        }
        if($invoiced !== 'all') {
            if($invoiced == 'مع فاتورة') {
                $containers->whereHas('invoices');
            } elseif($invoiced == 'بدون فاتورة') {
                $containers->whereDoesntHave('invoices');
            }
        }

        $containers = $containers->with(['customer', 'containerType', 'invoices'])->orderBy('date')->paginate($perPage)->withQueryString();

        return view('pages.containers.reports', compact(
            'containers',
            'types',
            'customers',
            'perPage',
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

        $serviceName = $container->services()->where('service_id', $serviceId)->first()->name;
        logActivity('إضافة خدمة إلى حاوية', "تم إضافة خدمة " . $serviceName . " إلى الحاوية رقم " . $container->code . " بسعر " . $price);

        return redirect()->back()->with('success', 'تم إضافة الخدمة الى الحاوية بنجاح');
    }
}
