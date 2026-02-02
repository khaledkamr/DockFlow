<?php

namespace App\Exports;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        $account = $this->filters['account'] ?? null;
        $costCenter = $this->filters['cost_center'] ?? null;
        $from = $this->filters['from'] ?? null;
        $to = $this->filters['to'] ?? null;

        $query->when($account, function($q) use ($account) {
            $q->where('account_id', $account);
        });
        $query->when($costCenter, function($q) use ($costCenter) {
            $q->where('cost_center_id', $costCenter);
        });

        if ($from && $to) {
            $query->whereHas('journal', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            });
        }

        $opening_balance = 0;
        if ($from) {
            $opening_balance = JournalEntryLine::where(function($q) {
                    // placeholder to chain account condition below
                });

            if (!empty($this->filters['account'])) {
                $opening_balance = JournalEntryLine::where('account_id', $this->filters['account']);
            } else {
                $opening_balance = JournalEntryLine::query();
            }

            $opening_balance = $opening_balance
                ->whereHas('journal', function ($q) use ($from) {
                    $q->where('date', '<', $from);
                })
                ->select(DB::raw('COALESCE(SUM(debit - credit),0) as opening'))
                ->value('opening');
        }

        if ($from && $to) {
            $query->whereHas('journal', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            });
        }

        $query->with('journal')
            ->orderBy(
                JournalEntry::select('date')
                    ->whereColumn('journal_entries.id', 'journal_entry_lines.journal_entry_id')
            )
            ->orderBy(
                JournalEntry::select('code')
                    ->whereColumn('journal_entries.id', 'journal_entry_lines.journal_entry_id')
            );

        $lines = $query->get();

        $rows = collect();

        $rows->push([
            'التاريخ'    => '',
            'رقم القيد'  => '',
            'نوع القيد'  => '',
            'مركز التكلفة' => '',
            'البيان'     => 'رصيد افتتاحي',
            'مدين'       => '',
            'دائن'       => '',
            'الرصيد'     => (float) $opening_balance,
        ]);

        $balance = (float) $opening_balance;

        foreach ($lines as $line) {
            $balance += ($line->debit - $line->credit);

            $rows->push([
                Carbon::parse($line->journal->date)->format('Y/m/d'),
                $line->journal->code,
                $line->journal->type,
                $line->costCenter->name ?? '',
                $line->description,
                number_format($line->debit, 2),
                number_format($line->credit, 2),
                number_format($balance, 2)
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'رقم القيد',
            'نوع القيد',
            'مركز التكلفة',
            'البيان',
            'مدين',
            'دائن',
            'الرصيد',
        ];
    }
}
