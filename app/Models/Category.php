<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'name_ar',
        'name_en',
        'parent_id',
        'company_id',
        'user_id',
    ];

    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
