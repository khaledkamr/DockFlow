<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Transaction::query();

        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }
        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to']]);
        }
        if(!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }
        if(!empty($this->filters['invoice_status']) && $this->filters['invoice_status'] !== 'all') {
            if($this->filters['invoice_status'] === 'with_invoice') {
                $query->whereHas('containers.invoices', function($q) {
                    $q->where('type', 'تخليص');
                });
            } elseif($this->filters['invoice_status'] === 'without_invoice') {
                $query->whereDoesntHave('containers.invoices', function($q) {
                    $q->where('type', 'تخليص');
                });
            }
        }
        if(!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('customs_declaration', 'like', '%' . $search . '%')
                    ->orWhere('policy_number', 'like', '%' . $search . '%')
                    ->orWhereHas('containers', function($q3) use ($search) {
                        $q3->where('code', 'like', "%{$search}%");
                    })
                    ->orWhereDate('date', 'like', '%' . $search . '%');
            });
        }

        $query = $query->with(['customer', 'containers', 'items'])->orderBy('code');

        return $query->get()->map(function ($transaction) {
            return [
                $transaction->code,
                $transaction->customer->name,
                $transaction->containers->count(),
                $transaction->status,
                Carbon::parse($transaction->date)->format('Y/m/d'),
                $transaction->containers->first()->invoices->where('type', 'تخليص')->first()->code ?? '-',
                $transaction->items->where('type', 'مصروف')->sum('amount'),
                $transaction->items->where('type', 'ايراد تخليص')->sum('amount'),
                $transaction->items->where('type', 'ايراد نقل')->sum('amount'),
                $transaction->items->where('type', 'ايراد عمال')->sum('amount'),
                $transaction->items->where('type', 'ايراد سابر')->sum('amount'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم المعاملة',
            'اسم العميل',
            'عدد الحاويات',
            'حالة المعاملة',
            'تاريخ المعاملة',
            'رقم فاتورة',
            'المصروفات',
            'ايراد التخليص',
            'ايراد النقل',
            'ايراد العمال',
            'ايراد سابر',
        ];
    }
}
