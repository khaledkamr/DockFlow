<?php

namespace App\Exports;

use App\Models\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Invoice::query();

        if(!empty($this->filters['customers']) && $this->filters['customers'] !== 'all') {
            $query->whereIn('customer_id', explode(',', $this->filters['customers']));
        }

        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to'],]);
        }

        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('type', $this->filters['type']);
        }

        if(!empty($this->filters['payment_method']) && $this->filters['payment_method'] !== 'all') {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if(!empty($this->filters['is_posted']) && $this->filters['is_posted'] !== 'all') {
            $is_posted = $this->filters['is_posted'] == 'true' ? true : false;
            $query->where('is_posted', $is_posted);
        }

        $query->with(['customer', 'made_by'])->orderby('code', 'asc');

        return $query->get()->map(function ($invoice) {
            return [
                $invoice->code,
                Carbon::parse($invoice->date)->format('Y/m/d'),
                $invoice->customer->name,
                $invoice->type,
                $invoice->payment_method,
                $invoice->amount_after_discount,
                $invoice->tax,
                $invoice->total_amount,
                $invoice->is_posted ? 'تم الترحيل' : 'لم يتم الترحيل',
                $invoice->made_by->name,
            ];
        });
    }

    public function headings(): array {
        return [
            'رقم الفاتورة',
            'تاريخ الفاتورة',
            'اسم العميل',
            'نوع الفاتورة',
            'طريقة الدفع',
            'المبلغ',
            'الضريبة المضافة',
            'الإجمالي',
            'الترحيل',
            'تم بواسطة',
        ];
    }
}
