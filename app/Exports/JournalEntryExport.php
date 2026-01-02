<?php

namespace App\Exports;

use App\Models\JournalEntry;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalEntryExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = JournalEntry::with('lines.account', 'voucher');

        if (!empty($this->filters['type']) && $this->filters['type'] !== 'all') {
            $type = $this->filters['type'];
            if($type == "قيد يومية") {
                $query->whereNull('voucher_id');
            } else {
                $query->whereHas('voucher', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            }
        }

        if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $from = $this->filters['from'];
            $to   = $this->filters['to'];
            $query->whereBetween('date', [$from, $to]);
        }

        $entries = $query->get();

        return $entries->flatMap(function($entry) {
            $rows = collect();
            
            $rows->push([
                "قيد - " . ($entry->voucher->type ?? 'قيد يومية') . " - بتاريخ " . $entry->date,
                '', '', '', '', '', ''
            ]);

            foreach ($entry->lines as $index => $line) {
                $rows->push([
                    $line->journal->code,
                    $index + 1,
                    $line->account->code,
                    $line->account->name,
                    $line->description,
                    $line->debit,
                    $line->credit,
                ]);
            }

            $rows->push([
                '', '', '', 'إجمالي',
                '',
                $entry->totalDebit,
                $entry->totalCredit
            ]);

            return $rows;
        });
    }

    public function headings(): array
    {
        return [
            'رقم القيد',
            'سطر',
            'رقم الحساب',
            'اسم الحساب',
            'البيان',
            'مدين',
            'دائن',
        ];
    }
}
