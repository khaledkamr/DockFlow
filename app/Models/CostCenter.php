<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use BelongsToCompany; 

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'company_id',
        'level',
        'is_active',
    ];

    public function parent() {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    public function leafsChildren() {
        $IDs = [];
        foreach ($this->children as $child) {
            if ($child->children->isEmpty()) {
                $IDs[] = $child->id;
            } else {
                $IDs = array_merge($IDs, $child->leafsChildren());
            }
        }
        return $IDs;
    }

    public function expenseInvoiceItems() {
        return $this->hasMany(ExpenseInvoiceItems::class, 'cost_center_id');
    }

    public function getAllChildrenIds() {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        return $ids;
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
