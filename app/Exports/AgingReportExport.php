<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgingReportExport implements FromCollection, WithHeadings, WithStyles
{
    use Exportable;

    protected $filters;
    protected $customers;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->loadCustomers();
    }

    protected function loadCustomers()
    {
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        if ($from && $to) {
            $this->customers = Customer::whereHas('invoices', function ($query) use ($from, $to) {
                $query->where('isPaid', 'لم يتم الدفع')
                    ->whereBetween('date', [$from, $to]);
            })->get();
        } else {
            $this->customers = collect();
        }
    }

    public function collection()
    {
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        if (!$from || !$to) {
            return collect();
        }

        $rows = $this->customers->map(function ($customer) use ($from, $to) {
            return [
                $customer->name,
                number_format($customer->agingBalance($from, $to, 0, 0), 2) . ' ر.س (' . $customer->agingBalanceCount($from, $to, 0, 0) . ')',
                number_format($customer->agingBalance($from, $to, 1, 30), 2) . ' ر.س (' . $customer->agingBalanceCount($from, $to, 1, 30) . ')',
                number_format($customer->agingBalance($from, $to, 31, 60), 2) . ' ر.س (' . $customer->agingBalanceCount($from, $to, 31, 60) . ')',
                number_format($customer->agingBalance($from, $to, 61, null), 2) . ' ر.س (' . $customer->agingBalanceCount($from, $to, 61, null) . ')',
                number_format($customer->totalAgingBalance($from, $to), 2) . ' ر.س (' . $customer->totalAgingBalanceCount($from, $to) . ')',
            ];
        });

        // Add totals row
        $rows->push([
            'الإجمالي',
            number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 0, 0)), 2),
            number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 1, 30)), 2),
            number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 31, 60)), 2),
            number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 61, null)), 2),
            number_format($this->customers->sum(fn($c) => $c->totalAgingBalance($from, $to)), 2),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'اسم العميل',
            'حالي (0 يوم)',
            '1-30 يوم',
            '31-60 يوم',
            '+90 يوم',
            'إجمالي الرصيد'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->customers->count() + 2; // +1 for header, +1 for totals row

        return [
            1 => ['font' => ['bold' => true]],
            $lastRow => ['font' => ['bold' => true]],
        ];
    }

    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function(AfterSheet $event) {
    //             $event->sheet->getDelegate()->setRightToLeft(true);
    //         },
    //     ];
    // }
}
