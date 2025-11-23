<?php

namespace App\Exports;

use App\Models\JournalEntryLine;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountStatementExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = JournalEntryLine::query();
        $balance = 0;

        if (!empty($this->filters['account'])) {
            $query->where('account_id', $this->filters['account']);
        }

        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        if ($from && $to) {
            $query = $query->filter(function($line) use($from, $to) {
                return $line->journal->date >= $from && $line->journal->date <= $to;
            });
        }

        return $query->get()->map(function ($line) use($balance) {
            $balance += $line->debit - $line->credit;
            return [
                $line->account->name,
                $line->journal->date,
                $line->journal_entry_id,
                $line->journal->voucher->type ?? 'قيد يومي',
                $line->description,
                $line->debit,
                $line->credit,
                $balance
            ];
        });
    }

    public function headings(): array
    {
        return [
            'إسم الحساب',
            'تاريخ',
            'رقم القيد',
            'نوع القيد',
            'البيان',
            'البيان',
            'مدين',
            'دائن',
            'الرصيد',
        ];
    }
}
