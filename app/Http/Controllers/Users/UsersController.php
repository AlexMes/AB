<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Create;
use App\Http\Requests\Users\Update;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class UsersController
 *
 * @package App\Http\Controllers\API
 */
class UsersController extends Controller
{
    /**
     * UsersController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Load all visible users
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return User::query()
            ->visible()
            ->searchIn(['name','email'], $request->name)
            ->orderBy('report_sort')
            ->when($request->has('branch'), function ($query) use ($request) {
                return $query->where('branch_id', $request->input('branch'));
            })
            ->when($request->has('team'), function ($query) use ($request) {
                return $query->whereHas('teams', function ($query) use ($request) {
                    return $query->where('teams.id', $request->input('team'));
                });
            })
            ->when($request->has('userRole'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    return $q->where('role', $request->input('userRole'))
                        ->orWhere('id', 2)
                        ->orWhere('id', 5);
                });
            })
            ->when($request->boolean('teammates'), fn ($query) => $query->teammates())
            ->when($request->input('office'), fn ($q, $input) => $q->whereIn('users.office_id', Arr::wrap($input)))
            ->unless($request->has('all'), function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });
    }

    /**
     * Load single user data
     *
     * @param \App\User $user
     *
     * @return \App\User
     */
    public function show(User $user)
    {
        return $user->load('deniedTelegramNotifications');
    }

    /**
     * Store new user
     *
     * @param \App\Http\Requests\Users\Create $request
     *
     * @return \App\User|\Illuminate\Database\Eloquent\Model
     */
    public function store(Create $request)
    {
        return User::create($request->validated());
    }

    /**
     * Update user info
     *
     * @param \App\User                       $user
     * @param \App\Http\Requests\Users\Update $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, Update $request)
    {
        return response()->json(tap($user)->update($request->validated()), 202);
    }

    /**
     * Delete user from DB
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
