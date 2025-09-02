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

        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('container_type_id', $this->filters['type']);
        }

        if(!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }

        if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to'],]);
        }

        return $query->get()->map(function ($container) {
            return [
                'ID'           => $container->id,
                'Code'         => $container->code,
                'Customer'     => $container->customer ? $container->customer->name : 'N/A',
                'Type'         => $container->containerType ? $container->containerType->name : 'N/A',
                'Location'     => $container->location,
                'Status'       => $container->status,
                'Received By'  => $container->received_by,
                'Delivered By' => $container->delivered_by,
                'Date'         => $container->date,
                'Exit Date'    => $container->exit_date,
            ];
        });
    }

    public function headings(): array {
        return [
            'ID',
            'code',
            'customer',
            'type',
            'location',
            'status',
            'received_by',
            'delivered_by',
            'date',
            'exit_date'
        ];
    }
}
