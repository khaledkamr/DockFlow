<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TrialBalanceExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    protected $filters;
    protected $accounts;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->accounts = Account::where('level', 1)->get();
    }

    public function collection()
    {
        $rows = collect();

        foreach($this->accounts as $account) {
            $this->flattenAccount($account, $rows);
        }

        $totalOpeningDebit = $rows->sum(fn($row) => $row->opening_debit);
        $totalOpeningCredit = $rows->sum(fn($row) => $row->opening_credit);
        $totalMovementDebit = $rows->sum(fn($row) => $row->movement_debit);
        $totalMovementCredit = $rows->sum(fn($row) => $row->movement_credit);
        $totalClosingDebit = $rows->sum(fn($row) => $row->closing_debit);
        $totalClosingCredit = $rows->sum(fn($row) => $row->closing_credit);

        $rows->push((object)[
            'code' => '',
            'name' => 'الإجمالي',
            'opening_debit' => $totalOpeningDebit,
            'opening_credit' => $totalOpeningCredit,
            'movement_debit' => $totalMovementDebit,
            'movement_credit' => $totalMovementCredit,
            'closing_debit' => $totalClosingDebit,
            'closing_credit' => $totalClosingCredit,
        ]);

        return $rows;
    }

    protected function flattenAccount($account, &$rows)
    {
        $balance = $account->calculateBalance($this->filters['from'], $this->filters['to']);
        $opening = $account->calculateBalance(null, Carbon::parse($this->filters['from'])->subDay());

        $rows->push((object)[
            'code' => $account->code,
            'name' => str_repeat('-', $account->level) . ' ' . $account->name,
            'opening_debit' => $opening->debit,
            'opening_credit' => $opening->credit,
            'movement_debit' => $balance->debit,
            'movement_credit' => $balance->credit,
            'closing_debit' => $balance->balance['debit'],
            'closing_credit' => $balance->balance['credit'],
        ]);

        if($account->children->count()) {
            foreach($account->children as $child) {
                $this->flattenAccount($child, $rows);
            }
        }
    }

    public function headings(): array
    {
        return [
            'الرقم',
            'الاسم',
            'رصيد اول المدة مدين',
            'رصيد اول المدة دائن',
            'الحركة مدين',
            'الحركة دائن',
            'رصيد اخر المدة مدين',
            'رصيد اخر المدة دائن',
        ];
    }
}
