<?php

namespace App\Exports;

use App\Models\TransportOrder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransportOrdersExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TransportOrder::query();

        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }

        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to']]);
        }

        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('type', $this->filters['type']);
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

        $query->with(['customer', 'supplier', 'driver', 'vehicle', 'containers']);

        return $query->get()->map(function ($transportOrder) {
            $status = 'تم التسليم';
            return [
                $transportOrder->code,
                $transportOrder->transaction->code,
                $transportOrder->date,
                $transportOrder->customer->name ?? 'N/A',
                $transportOrder->type,
                $transportOrder->supplier->name ?? 'N/A',
                $transportOrder->supplier ? $transportOrder->driver_name : $transportOrder->driver->name ?? 'N/A',
                $transportOrder->supplier ? $transportOrder->vehicle_plate : $transportOrder->vehicle->plate_number ?? 'N/A',
                $transportOrder->containers->first()->code ?? 'N/A',
                $transportOrder->from,
                $transportOrder->to,
                $status,
                $transportOrder->supplier_cost,
                $transportOrder->diesel_cost,
                $transportOrder->driver_wage,
                $transportOrder->other_expenses,
                $transportOrder->client_cost,
                $transportOrder->total_cost,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم اشعار النقل',
            'رقم المعاملة',
            'التاريخ',
            'العميل',
            'نوع الناقل',
            'المورد',
            'السائق',
            'السيارة',
            'الحاوية',
            'مكان التحميل',
            'مكان التسليم',
            'الحالة',
            'تكلفة المورد',
            'تكلفة الديزل',
            'أجرة السائق',
            'مصروفات أخرى',
            'تكلفة العميل',
            'إجمالي التكلفة',
        ];
    }
}
