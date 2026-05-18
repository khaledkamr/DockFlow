<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany, HasUuid;

    protected $fillable = [
        'name_ar',
        'name_en',
        'sku',
        'img_url',
        'description',
        'profit_margin',
        'unit',
        'featured',
        'active',
        'category_id',
        'company_id',
        'user_id',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoices() {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
            ->withPivot('quantity', 'price', 'tax', 'total')
            ->withTimestamps();
    }
}
