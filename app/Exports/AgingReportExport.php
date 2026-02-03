<?php

namespace App\Exports;

use App\Models\Customer;
use Carbon\Carbon;
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
    protected $selectedCustomer;
    protected $unpaidInvoices;
    protected $isSingleCustomer;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->isSingleCustomer = ($filters['report_type'] ?? 'all') === 'single' && !empty($filters['customer_id']);
        $this->loadData();
    }

    protected function loadData()
    {
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        $this->unpaidInvoices = collect();
        $this->selectedCustomer = null;
        $this->customers = Customer::all();

        if ($this->isSingleCustomer) {
            $this->selectedCustomer = Customer::find($this->filters['customer_id']);
            
            if ($this->selectedCustomer && $this->selectedCustomer->contract && $from && $to) {
                $this->unpaidInvoices = $this->selectedCustomer
                    ->invoices()
                    ->where('isPaid', 'لم يتم الدفع')
                    ->whereBetween('date', [$from, $to])
                    ->with('customer')
                    ->get()
                    ->map(function ($invoice) {
                        $paymentDueDate = Carbon::parse($invoice->date)->addDays(
                            (int) ($this->selectedCustomer->contract->payment_grace_period ?? 0),
                        );

                        $lateDays = Carbon::now()->gt($paymentDueDate)
                            ? Carbon::parse($paymentDueDate)->diffInDays(Carbon::now())
                            : 0;

                        $invoice->payment_due_date = $paymentDueDate;
                        $invoice->late_days = $lateDays;

                        return $invoice;
                    });
            } elseif ($this->selectedCustomer && !$this->selectedCustomer->contract && $from && $to) {
                $this->unpaidInvoices = $this->selectedCustomer
                    ->invoices()
                    ->where('isPaid', 'لم يتم الدفع')
                    ->whereBetween('date', [$from, $to])
                    ->with('customer')
                    ->get();
            }
        } else {
            if ($from && $to) {
                $this->customers = Customer::whereHas('invoices', function ($query) use ($from, $to) {
                    $query->where('isPaid', 'لم يتم الدفع')
                        ->whereBetween('date', [$from, $to]);
                })->get();
            } else {
                $this->customers = collect();
            }
        }
    }

    public function collection()
    {
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        if (!$from || !$to) {
            return collect();
        }

        if ($this->isSingleCustomer) {
            $rows = $this->unpaidInvoices->map(function ($invoice) {
                $paymentDueDate = $invoice->payment_due_date ? $invoice->payment_due_date->format('Y/m/d') : '';
                $lateDays = $paymentDueDate != '' ? (int) $invoice->late_days . ' يوم' : '0 يوم';
                return [
                    $invoice->customer->name ?? $this->selectedCustomer->name,
                    $invoice->code,
                    $invoice->type,
                    Carbon::parse($invoice->date)->format('Y/m/d'),
                    $paymentDueDate,
                    $lateDays,
                    number_format($invoice->total_amount, 2, '.', ''),
                ];
            });

            // Add totals row
            $rows->push([
                'الإجمالي',
                '',
                '',
                '',
                '',
                '',
                number_format($this->unpaidInvoices->sum('total_amount'), 2, '.', ''),
            ]);

            return $rows;
        } else {
            // All customers report - show aging summary
            $rows = $this->customers->map(function ($customer) use ($from, $to) {
                return [
                    $customer->name,
                    number_format($customer->agingBalance($from, $to, 0, 0), 2, '.', '') . ' ر.س (' . $customer->agingBalanceCount($from, $to, 0, 0) . ')',
                    number_format($customer->agingBalance($from, $to, 1, 30), 2, '.', '') . ' ر.س (' . $customer->agingBalanceCount($from, $to, 1, 30) . ')',
                    number_format($customer->agingBalance($from, $to, 31, 60), 2, '.', '') . ' ر.س (' . $customer->agingBalanceCount($from, $to, 31, 60) . ')',
                    number_format($customer->agingBalance($from, $to, 61, null), 2, '.', '') . ' ر.س (' . $customer->agingBalanceCount($from, $to, 61, null) . ')',
                    number_format($customer->totalAgingBalance($from, $to), 2, '.', '') . ' ر.س (' . $customer->totalAgingBalanceCount($from, $to) . ')',
                ];
            });

            // Add totals row
            $rows->push([
                'الإجمالي',
                number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 0, 0)), 2, '.', ''),
                number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 1, 30)), 2, '.', ''),
                number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 31, 60)), 2, '.', ''),
                number_format($this->customers->sum(fn($c) => $c->agingBalance($from, $to, 61, null)), 2, '.', ''),
                number_format($this->customers->sum(fn($c) => $c->totalAgingBalance($from, $to)), 2, '.', ''),
            ]);

            return $rows;
        }
    }

    public function headings(): array
    {
        if ($this->isSingleCustomer) {
            return [
                'اسم العميل',
                'رقم الفاتورة',
                'نوع الفاتورة',
                'تاريخ الفاتورة',
                'موعد السداد',
                'أيام التأخير',
                'المبلغ',
            ];
        }

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
        if ($this->isSingleCustomer) {
            $lastRow = $this->unpaidInvoices->count() + 2;
        } else {
            $lastRow = $this->customers->count() + 2;
        }

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
