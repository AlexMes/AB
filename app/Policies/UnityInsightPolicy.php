<?php

namespace App\Policies;

use App\UnityInsight;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnityInsightPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User         $user
     * @param \App\UnityInsight $unityInsight
     *
     * @return mixed
     */
    public function view(User $user, UnityInsight $unityInsight)
    {
        return $user->branch_id === 16;
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
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User         $user
     * @param \App\UnityInsight $unityInsight
     *
     * @return mixed
     */
    public function update(User $user, UnityInsight $unityInsight)
    {
        return $user->branch_id === 16;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User         $user
     * @param \App\UnityInsight $unityInsight
     *
     * @return mixed
     */
    public function delete(User $user, UnityInsight $unityInsight)
    {
        return false;
    }
}
