<?php

namespace App\Http\Controllers;

use App\Access;
use App\Http\Requests\Access\Create;
use App\Http\Requests\Access\Update;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /**
     * AccessController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Access::class, 'access');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Access::visible()
            ->when($request->get('received_at'), function ($q) use ($request) {
                return $q->whereDate('received_at', $request->get('received_at'));
            })
            ->notEmptyWhereIn('supplier_id', $request->get('suppliers'))
            ->notEmptyWhereIn('user_id', $request->get('users'))
            ->notEmptyWhereIn('type', $request->get('types'))
            ->searchIn(['profile_name'], $request->get('search'))
            ->with(['supplier','user'])
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Access\Create $request
     *
     * @return \App\Access|\Illuminate\Database\Eloquent\Model
     */
    public function store(Create $request)
    {
        return Access::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Access $access
     *
     * @return \App\Access
     */
    public function show(Access $access)
    {
        return $access->load('user', 'supplier');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Access\Update $request
     * @param \App\Access                      $access
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Update $request, Access $access)
    {
        return response()->json(tap($access)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Access $access
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Access $access)
    {
        $access->delete();

        return response('Gone', 204);
    }
}
