<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    /**
     * This trait uses SESSION (not Auth::user()) to get company_id.
     *
     * Because using Auth::user() inside a global scope causes a disaster:
     * - The User model gets loaded.
     * - The trait re-applies its global scope during User load.
     * - That scope again tries to call Auth::user().
     * - Infinite loop.
     * - Whole system dies. Pages break. Life collapses.
     *
     * Using session('company_id') avoids all of that.
     *
     * If you ever think "let me change it to Auth::user()"...
     * DON'T.
     * This comment exists so Future-Me doesn't forget and burn the app again.
     */
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (session()->has('company_id')) {
                $builder->where('company_id', session('company_id'));
            }
        });

        static::creating(function ($model) {
            if (session()->has('company_id')) {
                $model->company_id = session('company_id');
            }
        });
    }
}
