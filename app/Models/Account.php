<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'type_id',
        'level',
        'is_active',
        'company_id',
    ];

    public function type() {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    public function parent() {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines() {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function customer() {
        return $this->hasOne(Customer::class);
    }

    public function calculateBalance($from = null, $to = null) {
        $ids = $this->getAllChildrenIds();
        $ids[] = $this->id;

        $movement_query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as j', 'l.journal_entry_id', '=', 'j.id')
            ->whereIn('l.account_id', $ids);

        $beginning_query = clone $movement_query;

        if($from && $to && $from <= $to) {
            $movement_query->whereBetween('j.date', [$from, $to]);
            $beginning_query->where('j.date', '<', $from);
        }
    
        $movement_result = $movement_query->selectRaw("
            COALESCE(SUM(l.debit),0) as total_debit,
            COALESCE(SUM(l.credit),0) as total_credit
        ")->first();

        $beginning_result = $beginning_query->selectRaw("
            COALESCE(SUM(l.debit),0) as total_debit,
            COALESCE(SUM(l.credit),0) as total_credit
        ")->first();

        return (object)[
            'beginning_debit'  => $beginning_result->total_debit,
            'beginning_credit' => $beginning_result->total_credit,
            'movement_debit'   => $movement_result->total_debit,
            'movement_credit'  => $movement_result->total_credit,
            'final_debit'  => max(0, $movement_result->total_debit + $beginning_result->total_debit - $movement_result->total_credit - $beginning_result->total_credit),
            'final_credit' => max(0, $movement_result->total_credit + $beginning_result->total_credit - $movement_result->total_debit - $beginning_result->total_debit)
        ];
    }

    public function getAllChildrenIds() {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        return $ids;
    }

    public function getLeafAccounts() {
        if ($this->level == 5) {
            return collect([$this]);
        }

        $leafs = collect();

        foreach ($this->children as $child) {
            $leafs = $leafs->merge($child->getLeafAccounts());
        }

        return $leafs;
    }
}
