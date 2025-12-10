<?php

namespace App\Exports;

use App\Models\JournalEntryLine;
use Carbon\Carbon;
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
            $query->whereHas('journal', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            });
        }

        $query->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->select('journal_entry_lines.*')
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.code');

        return $query->get()->map(function ($line) use($balance) {
            $balance += $line->debit - $line->credit;
            return [
                $line->account->name,
                Carbon::parse($line->journal->date)->format('Y/m/d'),
                $line->journal->code,
                $line->journal->type,
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
            'مدين',
            'دائن',
            'الرصيد',
        ];
    }
}
