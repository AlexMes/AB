<?php

namespace App\Http\Controllers\Tags;

use App\Facebook\Ad;
use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request, Tag $tag)
    {
        $this->authorize('view', $tag);

        $query = $tag->ads()
            ->searchIn('name', $request->search)
            ->orderByDesc('pivot_created_at');

        return $request->get('page') === 'all' ? $query->get() : $query->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $ad = Ad::findOrFail($request->id);
        if ($tag->ads->filter(fn ($a) => $a->id == $ad->id)->count() > 0) {
            abort(422, 'Creative is already added to the tag.');
        }
        $tag->ads()->attach($ad->id);

        return response()->json($ad, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag              $tag
     * @param \App\Facebook\Ad $ad
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return void
     */
    public function destroy(Tag $tag, Ad $ad)
    {
        $this->authorize('update', $tag);

        $tag->ads()->detach($ad->id);

        return response(null, 204);
    }
}
