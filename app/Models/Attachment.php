<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'user_id',
    ];

    public function attachable() {
        return $this->morphTo();
    }

    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function made_by() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
