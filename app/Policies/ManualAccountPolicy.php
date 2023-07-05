<?php

namespace App\Policies;

use App\ManualAccount;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManualAccountPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isDeveloper()) {
            return false;
        }

        if ($user->isDesigner()) {
            return false;
        }

        if ($user->isFinancier()) {
            return false;
        }

        if ($user->isSales()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User          $user
     * @param \App\ManualAccount $manualAccount
     *
     * @return mixed
     */
    public function view(User $user, ManualAccount $manualAccount)
    {
        if ($manualAccount->user_id === null) {
            return true;
        }

        return $user->is($manualAccount->user)
            || in_array($manualAccount->user_id, User::visible()->pluck('id')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User          $user
     * @param \App\ManualAccount $manualAccount
     *
     * @return mixed
     */
    public function update(User $user, ManualAccount $manualAccount)
    {
        if ($manualAccount->user_id === null) {
            return true;
        }

        return $user->is($manualAccount->user)
            || in_array($manualAccount->user_id, User::visible()->pluck('id')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User          $user
     * @param \App\ManualAccount $manualAccount
     *
     * @return mixed
     */
    public function delete(User $user, ManualAccount $manualAccount)
    {
        return false;
    }
}
