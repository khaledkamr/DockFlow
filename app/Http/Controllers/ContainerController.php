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

        $containers = $containers->with(['customer', 'containerType'])->orderBy('date', 'desc')->paginate(100)->onEachSide(1)->withQueryString();

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

        $timeline = collect();

        $timeline->push([
            'type' => 'container_creation',
            'title' => 'إنشاء الحاوية',
            'icon' => 'fa-plus',
            'date' => $container->created_at,
            'description' => 'تم إنشاء الحاوية في النظام برقم <strong>' . $container->code . '</strong> من نوع <strong>' . ($container->containerType->name ?? 'N/A') . '</strong> في النظام بواسطة <strong>' . $container->made_by->name . '</strong>',
            'link' => null,
            'code' => $container->code,
            'made_by' => $container->made_by->name,
            'extra_info' => $container->notes ? [['icon' => 'fa-sticky-note', 'label' => 'الملاحظات', 'value' => $container->notes]] : null,
            'model' => $container,
        ]);

        if ($servicePolicy) {
            $timeline->push([
                'type' => 'service_policy',
                'title' => 'بوليصة خدمات',
                'icon' => 'fa-file-contract',
                'date' => $servicePolicy->created_at,
                'description' => 'تم إضافة الحاوية إلى بوليصة خدمات رقم <a href="' . route('policies.services.details', $servicePolicy) . '" class="text-decoration-none fw-bold">' . $servicePolicy->code . '</a> بواسطة <strong>' . $servicePolicy->made_by->name . '</strong>',
                'link' => route('policies.services.details', $servicePolicy),
                'code' => $servicePolicy->code,
                'made_by' => $servicePolicy->made_by->name,
                'extra_info' => null,
                'model' => $servicePolicy,
            ]);
        }

        if ($serviceInvoice) {
            $timeline->push([
                'type' => 'service_invoice',
                'title' => 'فاتورة خدمات',
                'icon' => 'fa-file-invoice-dollar',
                'date' => $serviceInvoice->created_at,
                'description' => 'تم فوترة الحاوية بموجب فاتورة رقم <a href="' . route('invoices.services.details', $serviceInvoice) . '" class="text-decoration-none fw-bold">' . $serviceInvoice->code . '</a> بواسطة <strong>' . $serviceInvoice->made_by->name . '</strong>',
                'link' => route('invoices.services.details', $serviceInvoice),
                'code' => $serviceInvoice->code,
                'made_by' => $serviceInvoice->made_by->name,
                'extra_info' => null,
                'model' => $serviceInvoice,
            ]);
        }

        if ($transaction) {
            $timeline->push([
                'type' => 'transaction',
                'title' => 'معاملة تخليص',
                'icon' => 'fa-file-contract',
                'date' => $transaction->created_at,
                'description' => 'تم إنشاء معاملة تخليص جمركي بموجب معاملة رقم <a class="fw-bold text-decoration-none" href="' . route('transactions.details', $transaction) . '">' . $transaction->code . '</a> بواسطة <strong>' . $transaction->made_by->name . '</strong>',
                'link' => route('transactions.details', $transaction),
                'code' => $transaction->code,
                'made_by' => $transaction->made_by->name,
                'extra_info' => [
                    ['icon' => 'fa-file', 'label' => 'رقم البوليصة', 'value' => $transaction->policy_number],
                    ['icon' => 'fa-file-alt', 'label' => 'البيان الجمركي', 'value' => $transaction->customs_declaration ?? 'N/A'],
                    ['icon' => 'fa-calendar', 'label' => 'تاريخ البيان الجمركي', 'value' => $transaction->customs_declaration_date ?? 'N/A'],
                ],
                'model' => $transaction,
            ]);
        }

        if ($transportOrder) {
            $timeline->push([
                'type' => 'transport_order',
                'title' => 'اشعار نقل',
                'icon' => 'fa-truck',
                'date' => $transportOrder->created_at,
                'description' => 'تم نقل للحاوية من ' . $transportOrder->from . ' الى ' . $transportOrder->to . ' بموجب اشعار نقل <a href="' . route('transactions.transportOrders.details', $transportOrder) . '" class="text-decoration-none fw-bold">' . $transportOrder->code . '</a> بواسطة <strong>' . $transportOrder->made_by->name . '</strong>',
                'link' => route('transactions.transportOrders.details', $transportOrder),
                'code' => $transportOrder->code,
                'made_by' => $transportOrder->made_by->name,
                'extra_info' => null,
                'model' => $transportOrder,
            ]);
        }

        if ($clearanceInvoice) {
            $timeline->push([
                'type' => 'clearance_invoice',
                'title' => 'إنشاء فاتورة تخليص جمركي',
                'icon' => 'fa-file-invoice-dollar',
                'date' => $clearanceInvoice->created_at,
                'description' => 'تم فوترة الحاوية بموجب فاتورة رقم <a href="' . route('invoices.clearance.details', $clearanceInvoice) . '" class="text-decoration-none fw-bold">' . $clearanceInvoice->code . '</a> بواسطة <strong>' . ($clearanceInvoice->made_by->name ?? 'N/A') . '</strong>',
                'link' => route('invoices.clearance.details', $clearanceInvoice),
                'code' => $clearanceInvoice->code,
                'made_by' => $clearanceInvoice->made_by->name ?? 'N/A',
                'extra_info' => null,
                'model' => $clearanceInvoice,
            ]);
        }

        if ($storagePolicy) {
            $timeline->push([
                'type' => 'storage_policy',
                'title' => 'بوليصة تخزين',
                'icon' => 'fa-warehouse',
                'date' => $storagePolicy->created_at,
                'description' => 'تم تخزين الحاوية بموجب بوليصة رقم <a class="fw-bold text-decoration-none" href="' . route('policies.storage.details', $storagePolicy) . '">' . $storagePolicy->code . '</a> بواسطة <strong>' . $storagePolicy->made_by->name . '</strong> و موقعها في الساحه <i class="fas fa-map-marker-alt text-muted me-1 ms-1"></i> <strong>' . $container->location . '</strong>',
                'link' => route('policies.storage.details', $storagePolicy),
                'code' => $storagePolicy->code,
                'made_by' => $storagePolicy->made_by->name,
                'extra_info' => [
                    ['icon' => 'fa-user', 'label' => 'السائق', 'value' => $storagePolicy->driver_name],
                    ['icon' => 'fa-id-card', 'label' => 'هوية السائق', 'value' => $storagePolicy->driver_NID],
                    ['icon' => 'fa-car', 'label' => null, 'value' => $storagePolicy->driver_car . ' - ' . $storagePolicy->car_code],
                ],
                'model' => $storagePolicy,
            ]);
        }

        if ($receivePolicy) {
            $timeline->push([
                'type' => 'receive_policy',
                'title' => 'بوليصة تسليم',
                'icon' => 'fa-truck-fast',
                'date' => $receivePolicy->created_at,
                'description' => 'تم تسليم الحاوية للعميل <strong>' . $receivePolicy->customer->name . '</strong> بموجب بوليصة رقم <a class="fw-bold text-decoration-none" href="' . route('policies.receive.details', $receivePolicy) . '">' . $receivePolicy->code . '</a> بواسطة <strong>' . $receivePolicy->made_by->name . '</strong>',
                'link' => route('policies.receive.details', $receivePolicy),
                'code' => $receivePolicy->code,
                'made_by' => $receivePolicy->made_by->name,
                'extra_info' => [
                    ['icon' => 'fa-user', 'label' => 'السائق', 'value' => $receivePolicy->driver_name],
                    ['icon' => 'fa-id-card', 'label' => 'هوية السائق', 'value' => $receivePolicy->driver_NID],
                    ['icon' => 'fa-car', 'label' => null, 'value' => $receivePolicy->driver_car . ' - ' . $receivePolicy->car_code],
                ],
                'model' => $receivePolicy,
            ]);
        }

        if ($storageInvoice) {
            $timeline->push([
                'type' => 'storage_invoice',
                'title' => 'إنشاء فاتورة تخزين',
                'icon' => 'fa-file-invoice-dollar',
                'date' => $storageInvoice->created_at,
                'description' => 'تم فوترة الحاوية بموجب فاتورة رقم <a href="' . route('invoices.details', $storageInvoice) . '" class="text-decoration-none fw-bold">' . $storageInvoice->code . '</a> بواسطة <strong>' . $storageInvoice->made_by->name . '</strong>',
                'link' => route('invoices.details', $storageInvoice),
                'code' => $storageInvoice->code,
                'made_by' => $storageInvoice->made_by->name,
                'extra_info' => null,
                'model' => $storageInvoice,
            ]);
        }

        $timeline = $timeline->sortBy('date')->values();

        return view('pages.containers.container_details', compact(
            'container', 
            'timeline'
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
        $search = $request->input('search', null);

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
        if($search) {
            $containers->where(function($query) use ($search) {
                $query->where('code', 'like', "%$search%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })->orWhere('location', 'like', "%$search%");
            });
        }

        $containers = $containers->with(['customer', 'containerType', 'invoices'])->orderBy('date')->paginate($perPage)->onEachSide(1)->withQueryString();

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
