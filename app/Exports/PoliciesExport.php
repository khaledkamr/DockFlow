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
        
        if(!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('date', [$this->filters['from'], $this->filters['to']]);
        }
        if(!empty($this->filters['customer']) && $this->filters['customer'] !== 'all') {
            $query->where('customer_id', $this->filters['customer']);
        }
        if(!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $query->where('type', $this->filters['type']);
        }
        if(!empty($this->filters['invoiced']) && $this->filters['invoiced'] !== 'all') {
            if($this->filters['invoiced'] == 'invoiced') {
                $query->whereHas('containers.invoices', function($q) {
                    $q->whereIn('type', ['تخزين', 'خدمات', 'تخليص']);
                });
            } elseif($this->filters['invoiced'] == 'not_invoiced') {
                $query->whereDoesntHave('containers.invoices', function($q) {
                    $q->whereIn('type', ['تخزين', 'خدمات', 'تخليص']);
                });
            }
        }
        if(!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('containers', function($q3) use ($search) {
                        $q3->where('code', 'like', '%' . $search . '%');
                    })
                    ->orWhere('reference_number', 'like', '%' . $search . '%')
                    ->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        $query->with(['customer', 'containers.invoices'])->orderBy('code');

        return $query->get()->map(function($policy) {
            if($policy->containers->first() && $policy->containers->first()->policies->where('type', 'تسليم')->first()) {
                $receivePolicy = $policy->containers->first()->policies->where('type', 'تسليم')->first()->code;
                $receivePolicyDriverName = $policy->containers->first()->policies->where('type', 'تسليم')->first()->driver_name;
                $receivePolicyCarCode = $policy->containers->first()->policies->where('type', 'تسليم')->first()->car_code;
            } else {
                $receivePolicy = '-';
                $receivePolicyDriverName = '-';
                $receivePolicyCarCode = '-';
            }

            if($policy->type == 'تخزين') {
                $storageDays = $policy->containers->first() ? $policy->containers->first()->storage_days : 0;
            } elseif($policy->type == 'خدمات') {
                $storageDays = 0;
            }

            if($policy->type == 'تخزين') {
                if($policy->containers->first()) {
                    if($policy->containers->first()->storge_days > $policy->storage_duration && $policy->storage_duration) {
                        $lateDays = $policy->containers->first()->storge_days - $policy->storage_duration;
                    } else {
                        $lateDays = 0;
                    }
                }
            } elseif($policy->type == 'خدمات') {
                $lateDays = 0;
            }

            if($policy->type == 'تخزين') {
                $basePrice = number_format($policy->storage_price, 2);
            } elseif($policy->type == 'خدمات') {
                if($policy->containers->first() && $policy->containers->first()->services->first()) {
                    $basePrice = number_format($policy->containers->first()->services->first()->pivot->price, 2);
                } else {
                    $basePrice = '-';
                }
            }

            if($policy->containers->first()) {
                if($policy->containers->first()->invoices()->where('type', 'LIKE', '%تخزين%')->first()) {
                    $invoice = $policy->containers->first()->invoices()->where('type', 'LIKE', '%تخزين%')->first()->code;
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
                $policy->reference_number ?? '-',
                Carbon::parse($policy->containers->first()->date)->format('Y/m/d'),
                $policy->driver_name,
                $policy->car_code,
                $policy->containers->first()->exit_date ? Carbon::parse($policy->containers->first()->exit_date)->format('Y/m/d') : '-',
                $receivePolicyDriverName,
                $receivePolicyCarCode,
                $storageDays,
                $lateDays,
                $basePrice,
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
            'الرقم المرجعي',
            'تاريخ الدخول',
            'إسم السائق',
            'لوحة السيارة',
            'تاريخ الخروج',
            'إسم السائق',
            'لوحة السيارة',
            'أيام التخزين',
            'أيام التأخير',
            'السعر الأساسي',
            'الفاتورة',
        ];
    }
}
