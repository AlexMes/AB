<?php

namespace App\Http\Controllers\Comments;

use App\Bundle;
use App\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\CreateComment;
use App\Http\Requests\Comments\UpdateComment;
use Illuminate\Support\Facades\Auth;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Bundle $bundle
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Bundle $bundle)
    {
        $this->authorize('index', Comment::class);

        return $bundle->comments()
            ->with('user')
            ->paginate();
    }

    /**
     * @param Bundle        $bundle
     * @param CreateComment $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Bundle $bundle, CreateComment $request)
    {
        $comment = $bundle->comments()->create([
            'user_id' => Auth::id(),
            'text'    => $request->text,
        ]);

        return response()->json($comment->loadMissing('user'), 201);
    }

    /**
     * @param $bundleId
     * @param Comment       $comment
     * @param UpdateComment $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update($bundleId, Comment $comment, UpdateComment $request)
    {
        $comment->update($request->validated());

        return response($comment->loadMissing('user'), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed   $bundleId
     * @param Comment $comment
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($bundleId, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response(null, 204);
    }
}
