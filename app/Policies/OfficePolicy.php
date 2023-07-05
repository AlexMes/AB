<?php

namespace App\Policies;

use App\Office;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficePolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole([User::SUPPORT, User::HEAD, User::SUBSUPPORT, User::ADMIN, User::TEAMLEAD]);
    }

    /**
     * @param User   $user
     * @param Office $office
     *
     * @return bool
     */
    public function view(User $user, Office $office)
    {
        if ($user->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return $user->branch->offices()->where('offices.id', $office->id)->exists();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole([User::ADMIN,User::DEVELOPER]);
    }

    /**
     * @param User   $user
     * @param Office $office
     *
     * @return bool
     */
    public function update(User $user, Office $office)
    {
        return $user->hasRole([User::ADMIN,User::DEVELOPER]);
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        return false;
    }
}
