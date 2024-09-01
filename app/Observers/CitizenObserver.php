<?php

namespace App\Observers;

use App\Models\Citizen;
use Storage;

class CitizenObserver
{
    /**
     * Handle the Citizen "created" event.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return void
     */
    public function created(Citizen $citizen)
    {
        //
    }

    /**
     * Handle the Citizen "updated" event.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return void
     */
    public function updated(Citizen $citizen)
    {
        if( request()->avatar ){
            $old_avatar = $citizen->getOriginal('avatar');
            Storage::disk('public')->delete( $old_avatar );
        }
    }

    /**
     * Handle the Citizen "deleted" event.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return void
     */
    public function deleted(Citizen $citizen)
    {
        // Delete its avatar
        Storage::disk('public')->delete( $citizen->avatar );
    }

    /**
     * Handle the Citizen "restored" event.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return void
     */
    public function restored(Citizen $citizen)
    {
        //
    }

    /**
     * Handle the Citizen "force deleted" event.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return void
     */
    public function forceDeleted(Citizen $citizen)
    {
        //
    }
}
