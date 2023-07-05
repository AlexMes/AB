<?php


namespace App\Http\Controllers\Users;

use App\Facebook\ProfilePage;
use App\Http\Controllers\Controller;
use App\User;

class Pages extends Controller
{
    /**
     * Pages constructor.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function __invoke(User $user)
    {
        $this->authorize('viewAny', ProfilePage::class);

        return $user->pages()
            ->visible()
            ->with(['profile'])
            ->paginate();
    }
}
