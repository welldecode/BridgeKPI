<?php

namespace App\Observers;

use App\Models\PlanSubscriptions;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class PlanSubscriptionObserver
{
    /**
     * Handle the PlanSubscriptions "created" event.
     */
    public function created(PlanSubscriptions $planSubscriptions): void
    {
        dd('passa aq');
        //
    }

    /**
     * Handle the PlanSubscriptions "updated" event.
     */
    public function updated(PlanSubscriptions $planSubscriptions): void
    {
        // 
  
    }
    /**
     * Handle the PlanSubscriptions "deleted" event.
     */
    public function deleted(PlanSubscriptions $planSubscriptions): void
    {
        //
    }

    /**
     * Handle the PlanSubscriptions "restored" event.
     */
    public function restored(PlanSubscriptions $planSubscriptions): void
    {
        //
        dd('passa aq');
    }

    /**
     * Handle the PlanSubscriptions "force deleted" event.
     */
    public function forceDeleted(PlanSubscriptions $planSubscriptions): void
    {
        //
    }
}
