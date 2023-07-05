<?php

namespace App\Policies;

use App\Tag;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isFarmer()) {
            return false;
        }

        if ($user->isFinancier()) {
            return false;
        }

        if ($user->isVerifier()) {
            return true;
        }

        if ($user->isDeveloper()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any Tags.
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
     * Determine whether the user can view the Tag.
     *
     * @param \App\User $user
     * @param \App\Tag  $tag
     *
     * @return mixed
     */
    public function view(User $user, Tag $tag)
    {
        if ($user->isSales()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create Tags.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isSales()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the Tag.
     *
     * @param \App\User $user
     * @param \App\Tag  $tag
     *
     * @return mixed
     */
    public function update(User $user, Tag $tag)
    {
        if ($user->isSales()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the Tag.
     *
     * @param \App\User $user
     * @param \App\Tag  $tag
     *
     * @return mixed
     */
    public function delete(User $user, Tag $tag)
    {
        return false;
    }
}
