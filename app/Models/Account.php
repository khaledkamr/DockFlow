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
        return $this->hasOne(Customer::class, 'account_id');
    }

    public function calculateBalance($from = null, $to = null) {
        $ids = $this->getAllChildrenIds();
        $ids[] = $this->id;

        $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as j', 'l.journal_entry_id', '=', 'j.id')
            ->whereIn('l.account_id', $ids);

        if ($from) {
            $query->where('j.date', '>=', $from);
        }

        if ($to) {
            $query->where('j.date', '<=', $to);
        }

        $result = $query->selectRaw("
            COALESCE(SUM(l.debit),0) as total_debit,
            COALESCE(SUM(l.credit),0) as total_credit
        ")->first();

        return (object)[
            'debit'   => $result->total_debit,
            'credit'  => $result->total_credit,
            'balance' => [
                'debit'  => max(0, $result->total_debit - $result->total_credit),
                'credit' => max(0, $result->total_credit - $result->total_debit)
            ]
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
}
