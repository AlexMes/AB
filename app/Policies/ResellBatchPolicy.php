<?php

namespace App\Policies;

use App\Office;
use App\ResellBatch;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class ResellBatchPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
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
        return $user->id === 27 || in_array($user->branch_id, [20, 16])
            || $user->isSupport() && $user->branch_id === 19;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User        $user
     * @param \App\ResellBatch $resellBatch
     *
     * @return mixed
     */
    public function view(User $user, ResellBatch $resellBatch)
    {
        if ($user->id === 27) {
            return true;
        }

        if ($user->isSupport() && $user->branch_id === 19) {
            if ($resellBatch->branch_id === $user->branch_id && Str::startsWith($resellBatch->name, 'Cold 1k')) {
                return true;
            }

            return $resellBatch->offices()
                ->whereIn('offices.id', Office::visible()->pluck('id'))
                ->exists();
        }

        if (in_array($user->branch_id, [20, 16])) {
            return $resellBatch->offices()
                ->whereIn('offices.id', Office::visible()->pluck('id'))
                ->exists();
        }

        return false;
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
        return $user->id === 27 || in_array($user->branch_id, [20, 16])
            || $user->isSupport() && $user->branch_id === 19;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User        $user
     * @param \App\ResellBatch $resellBatch
     *
     * @return mixed
     */
    public function update(User $user, ResellBatch $resellBatch)
    {
        if ($user->id === 27) {
            return true;
        }

        if ($user->isSupport() && $user->branch_id === 19) {
            if ($resellBatch->branch_id === $user->branch_id && Str::startsWith($resellBatch->name, 'Cold 1k')) {
                return true;
            }

            return $resellBatch->offices()
                ->whereIn('offices.id', Office::visible()->pluck('id'))
                ->exists();
        }

        if (in_array($user->branch_id, [20, 16])) {
            return $resellBatch->offices()
                ->whereIn('offices.id', Office::visible()->pluck('id'))
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User        $user
     * @param \App\ResellBatch $resellBatch
     *
     * @return mixed
     */
    public function delete(User $user, ResellBatch $resellBatch)
    {
        return $user->id === 27;
    }
}
