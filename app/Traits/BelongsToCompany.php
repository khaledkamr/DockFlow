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
     *
     * NOTE: This problem has been solved by removing the trait from the User model.
     */

    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $user = auth()->user();

            if($user->type == 'admin') {
                return; // Admin can see all records.
            } elseif(!$user || !$user->company_id) {
                $builder->whereRaw('1 = 0'); // No company, no records.
                session()->flash('error', 'عذراً حدث خطأ في تحميل البيانات.');
                
                return;
            } 

            $builder->where('company_id', $user->company_id);
        });

        static::creating(function ($model) {
            $user = auth()->user();

            if($user->type == 'admin') {
                return; // Admin can create records for any company (though this is unlikely).
            } elseif (!$user || !$user->company_id) {
                throw new \Exception("Cannot create " . get_class($model) . " without a company_id. User has no company.");
            }

            $model->company_id = $user->company_id;
        });
    }
}
