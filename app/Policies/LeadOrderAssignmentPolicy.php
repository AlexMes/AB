<?php

namespace App\Policies;

use App\LeadOrderAssignment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadOrderAssignmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isVerifier()) {
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
     * @param \App\User                $user
     * @param \App\LeadOrderAssignment $leadOrderAssignment
     *
     * @return mixed
     */
    public function view(User $user, LeadOrderAssignment $leadOrderAssignment)
    {
        return $user->can('view', $leadOrderAssignment->lead);
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User                $user
     * @param \App\LeadOrderAssignment $leadOrderAssignment
     *
     * @return mixed
     */
    public function update(User $user, LeadOrderAssignment $leadOrderAssignment)
    {
        return $user->can('view', $leadOrderAssignment->lead);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User                $user
     * @param \App\LeadOrderAssignment $leadOrderAssignment
     *
     * @return mixed
     */
    public function delete(User $user, LeadOrderAssignment $leadOrderAssignment)
    {
        return $user->hasRole([User::ADMIN,User::HEAD,User::SUPPORT,User::SUBSUPPORT]) && $user->can('view', $leadOrderAssignment->lead);
    }

    /**
     * Determines if user can transfer the assignment
     *
     * @param User                $user
     * @param LeadOrderAssignment $leadOrderAssignment
     *
     * @return bool
     */
    public function transfer(User $user, LeadOrderAssignment $leadOrderAssignment)
    {
        return $user->hasRole([User::ADMIN,User::HEAD,User::SUPPORT,User::SUBSUPPORT]) && $user->can('view', $leadOrderAssignment->lead);
    }
}
