<?php

namespace App\Policies;

use App\Lead;
use App\Offer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Carbon;

class LeadPolicy
{
    use HandlesAuthorization;


    /**
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole([User::ADMIN,User::HEAD,User::SUPPORT,User::SUBSUPPORT,User::DEVELOPER]);
    }

    /**
     * @return bool
     */
    public function view(User $user, Lead $lead)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return Offer::allowed()->whereId($lead->offer_id)->exists()
                && ($lead->user === null
                    || $user->branch_id === $lead->user->branch_id
                    || $lead->affiliate !== null && $user->branch_id === $lead->affiliate->branch_id);
        }

        if ($user->isDeveloper()) {
            return Carbon::parse($lead->created_at)->isSameMonth(now());
        }

        return $user->allowedOffers->contains($lead->offer);
    }

    /**
     * @return bool
     */
    public function create()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function update(User $user, Lead $lead)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return Offer::allowed()->whereId($lead->offer_id)->exists()
                && ($lead->user === null
                    || $user->branch_id === $lead->user->branch_id
                    || $lead->affiliate !== null && $user->branch_id === $lead->affiliate->branch_id);
        }

        if ($user->isDeveloper()) {
            return Carbon::parse($lead->created_at)->isSameMonth(now());
        }

        return $user->allowedOffers->contains($lead->offer);
    }

    /**
     * @return bool
     */
    public function delete(User $user, Lead $lead)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return Offer::allowed()->whereId($lead->offer_id)->exists()
                && ($lead->user === null
                    || $user->branch_id === $lead->user->branch_id
                    || $lead->affiliate !== null && $user->branch_id === $lead->affiliate->branch_id);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\User $user
     * @param \App\Lead $lead
     *
     * @return mixed
     */
    public function restore(User $user, Lead $lead)
    {
        return $user->isSupport();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function import(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function export(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function assignLeftovers(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function markAsLeftover(User $user)
    {
        return $user->isSupport();
    }
}
