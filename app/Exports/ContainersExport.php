<?php

namespace App\Exports;

use App\Models\Container;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContainersExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Container::query();

        if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to'],]);
        }

        if(!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            if($this->filters['status'] == 'متأخر') {
                $query->where('status', 'في الساحة')
                    ->whereHas('policies', function ($q) {
                        $q->where('type', 'تخزين')
                            ->whereRaw('DATEDIFF(NOW(), containers.date) > policies.storage_duration');
                    });
            } else {
                $query->where('status', $this->filters['status']);
            }
        }
        
        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('container_type_id', $this->filters['type']);
        }

        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }

        if (!empty($this->filters['invoiced']) && $this->filters['invoiced'] !== 'all') {
            if ($this->filters['invoiced'] == 'مع فاتورة') {
                $query->whereHas('invoices');
            } elseif ($this->filters['invoiced'] == 'بدون فاتورة') {
                $query->whereDoesntHave('invoices');
            }
        }

        $query->with(['customer', 'containerType', 'invoices']);

        return $query->get()->map(function ($container) {
            $invoices = $container->invoices->pluck('code')->implode(' | ');

            return [
                $container->code,
                $container->customer ? $container->customer->name : 'N/A',
                $container->containerType ? $container->containerType->name : 'N/A',
                $container->location,
                $container->status,
                $container->date,
                $container->exit_date,
                $invoices ?: 'N/A'
            ];
        });
    }

    public function headings(): array {
        return [
            'رقم الحاوية',
            'العميل',
            'النوع',
            'الموقع',
            'الحالة',
            'تاريخ الدخول',
            'تاريخ الخروج',
            'الفاتورة'
        ];
    }
}
