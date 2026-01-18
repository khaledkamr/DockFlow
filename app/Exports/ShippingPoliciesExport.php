<?php

namespace App\Exports;

use App\Models\ShippingPolicy;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShippingPoliciesExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ShippingPolicy::query();

        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }
        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to']]);
        }
        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('type', $this->filters['type']);
        }
        if(!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('is_received', $this->filters['status']);
        }
        if(!empty($this->filters['invoice_status']) && $this->filters['invoice_status'] !== 'all') {
            if($this->filters['invoice_status'] == 'with_invoice') {
                $query->whereHas('invoices');
            } else {
                $query->whereDoesntHave('invoices');
            }
        }
        if(!empty($this->filters['supplier']) && $this->filters['supplier'] !== 'all') {
            $query->where('supplier_id', $this->filters['supplier']);
        }
        if(!empty($this->filters['driver']) && $this->filters['driver'] !== 'all') {
            $query->where('driver_id', $this->filters['driver']);
        }
        if(!empty($this->filters['vehicle']) && $this->filters['vehicle'] !== 'all') {
            $query->where('vehicle_id', $this->filters['vehicle']);
        }
        if(!empty($this->filters['loading_location']) && $this->filters['loading_location'] !== 'all') {
            $query->where('from', $this->filters['loading_location']);
        }
        if(!empty($this->filters['delivery_location']) && $this->filters['delivery_location'] !== 'all') {
            $query->where('to', $this->filters['delivery_location']);
        }
        if(!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%$search%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('goods', function ($q3) use ($search) {
                        $q3->where('description', 'LIKE', "%$search%");
                    });
            });
        }

        $query->with(['customer', 'driver', 'vehicle', 'supplier', 'goods', 'invoices']);

        return $query->get()->map(function ($policy) {
            $invoices = $policy->invoices->pluck('code')->implode(' | ');
            return [
                $policy->code,
                $policy->date,
                $policy->customer ? $policy->customer->name : 'N/A',
                $policy->type,
                $policy->supplier ? $policy->supplier->name : 'N/A',
                $policy->driver ? $policy->driver->name : $policy->driver_name,
                $policy->vehicle ? $policy->vehicle->plate_number : $policy->vehicle_plate,
                $policy->goods->first() ? $policy->goods->first()->description : 'N/A',
                $policy->from,
                $policy->to,
                $policy->is_received ? 'تم التسليم' : 'تحت التسليم',
                $policy->supplier_cost,
                $policy->diesel_cost,
                $policy->driver_wage,
                $policy->other_expenses,
                $policy->customer_cost,
                $policy->total_cost,
                $invoices ?: 'N/A'
            ];
        });
    }

    public function headings(): array {
        return [
            'رقم البوليصة',
            'التاريخ',
            'العميل',
            'نوع الناقل',
            'المورد',
            'السائق',
            'السيارة',
            'البيان',
            'مكان التحميل',
            'مكان التسليم',
            'الحالة',
            'تكلفة المورد',
            'تكلفة الديزل',
            'أجرة السائق',
            'مصروفات أخرى',
            'تكلفة العميل',
            'التكلفة الإجمالية',
            'الفاتورة'
        ];
    }
}
