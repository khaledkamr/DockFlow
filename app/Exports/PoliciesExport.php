<?php

namespace App\Exports;

use App\Models\Policy;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PoliciesExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Policy::query()->whereIn('type', ['تخزين', 'خدمات']);
        
        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }
        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to']]);
        }
        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('type', $this->filters['type']);
        }
        if(!empty($this->filters['invoiced']) && $this->filters['invoiced'] !== 'all') {
            if($this->filters['invoiced'] == 'invoiced') {
                $query->whereHas('containers.invoices', function($q) {
                    $q->whereIn('type', ['تخزين', 'خدمات']);
                });
            } elseif($this->filters['invoiced'] == 'not_invoiced') {
                $query->whereDoesntHave('containers.invoices', function($q) {
                    $q->whereIn('type', ['تخزين', 'خدمات']);
                });
            }
        }

        $query->with(['customer', 'containers.invoices'])->orderBy('code');

        return $query->get()->map(function($policy) {
            if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first()) {
                $receivePolicy = $policy->containers->first()->policies->where('type', 'تسليم')->first()->code;
            } else {
                $receivePolicy = '-';
            }

            if($policy->type == 'تخزين') {
                $storageDays = $policy->containers->first() ? $policy->containers->first()->storage_days : 0;
            } elseif($policy->type == 'خدمات') {
                $storageDays = 0;
            }

            if($policy->containers->first()) {
                if($policy->containers->first()->invoices->where('type', 'تخزين')->first()) {
                    $invoice = $policy->containers->first()->invoices->where('type', 'تخزين')->first()->code;
                } elseif($policy->containers->first()->invoices->where('type', 'خدمات')->first()) {
                    $invoice = $policy->containers->first()->invoices->where('type', 'خدمات')->first()->code;
                } else {
                    $invoice = '-';
                }
            }
            return [
                $policy->code,
                $policy->type,
                $receivePolicy,         
                $policy->customer->name,
                $policy->containers->first() ? $policy->containers->first()->code : '-',
                Carbon::parse($policy->containers->first()->date)->format('Y/m/d'),
                $policy->containers->first()->exit_date ? Carbon::parse($policy->containers->first()->exit_date)->format('Y/m/d') : '-',
                $storageDays,
                $invoice,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'رقم البوليصة',
            'النوع',
            'بوليصة التسليم',
            'العميل',
            'الحاوية',
            'تاريخ الدخول',
            'تاريخ الخروج',
            'أيام التخزين',
            'الفاتورة',
        ];
    }
}
