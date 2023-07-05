<?php

namespace App\Facebook\Traits;

use App\Facebook\Account;
use App\Facebook\Profile;

/**
 * Trait HasProfile
 *
 * @package App\Facebook\Traits
 * @mixin \Eloquent
 */
trait HasProfile
{
    /**
     * Related social facebook profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function profile()
    {
        return $this->hasOneThrough(
            Profile::class,
            Account::class,
            'account_id',
            'id',
            'account_id',
            'profile_id'
        );
    }
}
