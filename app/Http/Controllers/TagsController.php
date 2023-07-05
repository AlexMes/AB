<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\Create;
use App\Http\Requests\Tags\Update;
use App\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Tag::class);

        return Tag::query()
            ->searchIn('name', $request->search)
            ->unless($request->has('all'), function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $this->authorize('create', Tag::class);

        return response()->json(Tag::create($request->validated()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Group $tag
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);

        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update     $request
     * @param \App\Group $tag
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $tag->update($request->validated());

        return response()->json($tag, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return response("Deleted", 204);
    }
}
