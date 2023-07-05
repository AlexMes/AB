<?php

namespace App\CRM\Policies;

use App\CRM\LeadOrderAssignment;
use App\Manager;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class AssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determines is user allowed to see
     * lead order assignments at all
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Manager ?: $user->hasRole([User::ADMIN,User::HEAD,User::SUPPORT]);
    }

    /**
     * Determines is user allowed to see details
     * of the assignment
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \App\CRM\LeadOrderAssignment               $assignment
     *
     * @return bool
     */
    public function view(Authenticatable $user, LeadOrderAssignment $assignment)
    {
        if ($user instanceof User) {
            return $user->can('view', $assignment->lead);
        }

        if ($user->id === 3761) {
            return in_array($assignment->route->order->office_id, [[8,20,25,83,108,118]]);
        }

        if (! $user->hasTenant()) {
            if ($user->isAdmin()) {
                return $assignment->route->order->office_id === $user->office_id;
            }

            return $assignment->route->manager->is($user);
        }

        if ($assignment->route->manager->is($user)) {
            return true;
        }

        if ($user->isOfficeHead()) {
            return $assignment->route->manager->office_id === $user->office_id;
        }

        if ($user->isAdmin()) {
            return $assignment->route->manager->frx_tenant_id === $user->frx_tenant_id;
        }

        return false;
    }

    /**
     * Determine is user allowed to update
     * assignment details in the database
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \App\CRM\LeadOrderAssignment               $assignment
     *
     * @return bool
     */
    public function update(Authenticatable $user, LeadOrderAssignment $assignment)
    {
        if ($user instanceof User) {
            return $user->can('view', $assignment->lead);
        }

        if (! $user->hasTenant()) {
            if ($user->isAdmin()) {
                return $assignment->route->order->office_id === $user->office_id;
            }

            return $assignment->route->manager->is($user);
        }

        if ($assignment->route->manager->is($user)) {
            return true;
        }

        if ($user->isOfficeHead()) {
            return $assignment->route->manager->office_id === $user->office_id;
        }

        if ($user->isAdmin()) {
            return $assignment->route->manager->frx_tenant_id === $user->frx_tenant_id;
        }

        return false;
    }

    /**
     * Determine is user allowed to delete
     * assignment from the database
     *
     * @param Authenticatable     $user
     * @param LeadOrderAssignment $assignment
     *
     * @return bool
     */
    public function delete(Authenticatable $user, LeadOrderAssignment $assignment)
    {
        if ($user instanceof User) {
            return $user->id === 1 || $user->isSupport();
        }

        return false;
    }

    /**
     * Determines if user can transfer the assignment
     *
     * @param Authenticatable     $user
     * @param LeadOrderAssignment $assignment
     *
     * @return bool
     */
    public function transfer(Authenticatable $user, LeadOrderAssignment $assignment)
    {
        if ($user instanceof User) {
            return $user->can('view', $assignment->lead);
        }

        /** @var Manager $user */
        if (optional($user->office)->allow_transfer && $user->isAgent()) {
            return $assignment->route->manager->is($user);
        }

        if (! $user->hasTenant()) {
            if ($user->isAdmin()) {
                return $assignment->route->order->office_id === $user->office_id;
            }

            return false;
        }

        if ($user->isAdmin() || $user->isOfficeHead()) {
            return true;
        }

        if ($user->isCloser()) {
            return $assignment->route->manager->is($user);
        }

        return false;
    }

    /**
     * Determines if user can mark the assignment as leftover
     *
     * @param Authenticatable     $user
     * @param LeadOrderAssignment $assignment
     *
     * @return bool
     */
    public function markAsLeftover(Authenticatable $user, LeadOrderAssignment $assignment)
    {
        if ($user instanceof User) {
            return $user->can('view', $assignment->lead);
        }

        return false;
    }

    /**
     * Determines if user is allowed to export assignments
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function export(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin();
        }

        if ($user->id === 3761) {
            return false;
        }

        /** @var Manager $user */
        return $user->isOfficeHead() || $user->isAdmin();
    }

    /**
     * Determines if user is allowed to mass transfer assignments
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function massTransfer(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin() || $user->isSupport();
        }

        /** @var Manager $user */
        if ($user->isOfficeHead() || $user->isAdmin()) {
            return true;
        }

        if ($user->isAgent()) {
            return $user->office->allow_transfer;
        }

        return false;
    }

    /**
     * Determines if user is allowed to mass transfer assignments
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function massMarkAsLeftover(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin() || $user->isSupport();
        }

        return false;
    }

    /**
     * Determines if user is allowed to mass transfer assignments
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return bool
     */
    public function massDelete(Authenticatable $user)
    {
        if ($user instanceof User) {
            return $user->isAdmin() || $user->isSupport();
        }

        return false;
    }
}
