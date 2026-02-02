<?php

namespace App\Exports;

use App\Models\CostCenter;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CostCenterStatementExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $costCenterId = $this->filters['cost_center'] ?? null;
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        if (!$costCenterId) {
            return collect();
        }

        $costCenter = CostCenter::find($costCenterId);
        
        if (!$costCenter) {
            return collect();
        }

        if ($costCenter->children()->count() > 0) {
            $costCenterIDs = $costCenter->leafsChildren();
        } else {
            $costCenterIDs = [$costCenter->id];
        }

        $statement = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->select('journal_entry_lines.*')
            ->whereIn('cost_center_id', $costCenterIDs)
            ->when($from && $to, function ($query) use ($from, $to) {
                return $query->whereBetween('journal_entries.date', [$from, $to]);
            })
            ->with(['journal', 'account', 'costCenter'])
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.code')
            ->get();

        $rows = collect();
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($statement as $line) {
            $totalDebit += $line->debit;
            $totalCredit += $line->credit;

            $rows->push([
                $line->costCenter->name ?? '',
                Carbon::parse($line->journal->date)->format('Y/m/d'),
                $line->journal->code,
                $line->account->code ?? '',
                $line->account->name ?? '',
                $line->description,
                number_format($line->debit, 2),
            ]);
        }

        // Add totals row
        $rows->push([
            '',
            '',
            '',
            '',
            'الإجمالي',
            '',
            number_format($totalDebit, 2),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'مركز التكلفة',
            'التاريخ',
            'رقم القيد',
            'رقم الحساب',
            'اسم الحساب',
            'البيان',
            'المصروف',
        ];
    }
}
