<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
        
        if(isset($this->filters['type']) && $this->filters['type'] != 'all') {
            $this->accounts = Account::where('name', $this->filters['type'])->get();
        } else {
            $this->accounts = Account::where('level', 1)->get();
        }
    }

    public function collection()
    {
        $rows = collect();

        $sum_beginning_debit = 0;
        $sum_beginning_credit = 0;
        $sum_movement_debit = 0;
        $sum_movement_credit = 0;
        $sum_final_debit = 0;
        $sum_final_credit = 0;

        foreach($this->accounts as $account) {
            $this->flattenAccount($account, $rows, $sum_beginning_debit, $sum_beginning_credit, $sum_movement_debit, $sum_movement_credit, $sum_final_debit, $sum_final_credit);
        }

        if(isset($this->filters['with_balances']) && $this->filters['with_balances'] == '1') {
            $rows->push((object)[
                'code' => '',
                'name' => 'الإجمالي',
                'closing_debit' => $sum_final_debit ?? 0,
                'closing_credit' => $sum_final_credit ?? 0,
            ]);
        } else {
            $rows->push((object)[
                'code' => '',
                'name' => 'الإجمالي',
                'opening_debit' => $sum_beginning_debit ?? 0,
                'opening_credit' => $sum_beginning_credit ?? 0,
                'movement_debit' => $sum_movement_debit ?? 0,
                'movement_credit' => $sum_movement_credit ?? 0,
                'closing_debit' => $sum_final_debit ?? 0,
                'closing_credit' => $sum_final_credit ?? 0,
            ]);
        }

        return $rows;
    }

    protected function flattenAccount($account, &$rows, &$sum_beginning_debit, &$sum_beginning_credit, &$sum_movement_debit, &$sum_movement_credit, &$sum_final_debit, &$sum_final_credit)
    {
        $balance = $account->calculateBalance($this->filters['from'], $this->filters['to']);

        if('0' === $this->filters['debit_movements'] && $balance->final_debit > 0) {
            return;
        }
        if('0' === $this->filters['credit_movements'] && $balance->final_credit > 0) {
            return;
        }
        if('0' === $this->filters['zero_balances'] && $balance->final_debit == 0 && $balance->final_credit == 0) {
            return;
        }

        if(!isset($this->filters['with_balances']) || $this->filters['with_balances'] == '1') {
            $rows->push((object)[
                'code' => $account->code,
                'name' => str_repeat(' - ', $account->level) . ' ' . $account->name,
                'closing_debit' => $balance->final_debit ?? 0,
                'closing_credit' => $balance->final_credit ?? 0,
            ]);
        } else {
            $rows->push((object)[
                'code' => $account->code,
                'name' => str_repeat(' - ', $account->level) . ' ' . $account->name,
                'opening_debit' => $balance->beginning_debit ?? 0,
                'opening_credit' => $balance->beginning_credit ?? 0,
                'movement_debit' => $balance->movement_debit ?? 0,
                'movement_credit' => $balance->movement_credit ?? 0,
                'closing_debit' => $balance->final_debit ?? 0,
                'closing_credit' => $balance->final_credit ?? 0,
            ]);
        }

        $sum_beginning_debit += $balance->beginning_debit;
        $sum_beginning_credit += $balance->beginning_credit;
        $sum_movement_debit += $balance->movement_debit;
        $sum_movement_credit += $balance->movement_credit;
        $sum_final_debit += $balance->final_debit;
        $sum_final_credit += $balance->final_credit;

        if($account->children->count()) {
            foreach($account->children as $child) {
                $this->flattenChild($child, $rows);
            }
        }
    }

    protected function flattenChild($child, &$rows)
    {
        $balance = $child->calculateBalance($this->filters['from'], $this->filters['to']);

        if('0' === $this->filters['debit_movements'] && $balance->final_debit > 0) {
            return;
        }
        if('0' === $this->filters['credit_movements'] && $balance->final_credit > 0) {
            return;
        }
        if('0' === $this->filters['zero_balances'] && $balance->final_debit == 0 && $balance->final_credit == 0) {
            return;
        }

        if(!isset($this->filters['with_balances']) || $this->filters['with_balances'] == '1') {
            $rows->push((object)[
                'code' => $child->code,
                'name' => str_repeat(' - ', $child->level) . ' ' . $child->name,
                'closing_debit' => $balance->final_debit ?? 0,
                'closing_credit' => $balance->final_credit ?? 0,
            ]);
        } else {
            $rows->push((object)[
                'code' => $child->code,
                'name' => str_repeat(' - ', $child->level) . ' ' . $child->name,
                'opening_debit' => $balance->beginning_debit ?? 0,
                'opening_credit' => $balance->beginning_credit ?? 0,
                'movement_debit' => $balance->movement_debit ?? 0,
                'movement_credit' => $balance->movement_credit ?? 0,
                'closing_debit' => $balance->final_debit ?? 0,
                'closing_credit' => $balance->final_credit ?? 0,
            ]);
        }

        if($child->children->count()) {
            foreach($child->children as $grandchild) {
                $this->flattenChild($grandchild, $rows);
            }
        }
    }

    public function headings(): array
    {
        if(isset($this->filters['with_balances']) && $this->filters['with_balances'] == '1') {
            return [
                'الرقم',
                'الاسم',
                'رصيد اخر المدة مدين',
                'رصيد اخر المدة دائن',
            ];
        } else {
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
}
