<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyZatca extends Model
{
    use HasFactory;
    protected $table = 'company_zatcas';
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class,'company_id','company_id');
    }

}
