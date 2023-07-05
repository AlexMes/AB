<?php

namespace App\Http\Controllers\Comments;

use App\Comment;
use App\Domain;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\CreateComment;
use App\Http\Requests\Comments\UpdateComment;

/**
 * Class CustomerComments
 *
 * @package App\Http\Controllers\Customers
 */
class DomainComments extends Controller
{
    /**
     * @param Domain $domain
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Domain $domain)
    {
        $this->authorize('viewAny', Comment::class);

        return $domain->comments()
            ->with('user')
            ->paginate();
    }

    /**
     * @param Domain        $domain
     * @param CreateComment $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Domain $domain, CreateComment $request)
    {
        $comment = $domain->comments()->create([
            'user_id' => $request->user()->id,
            'text'    => $request->text
        ]);

        return $comment->loadMissing('user');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Domain        $domain
     * @param UpdateComment $request
     * @param Comment       $comment
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Domain $domain, UpdateComment $request, Comment $comment)
    {
        $comment->update($request->validated());

        return response()->json($comment->refresh()->loadMissing('user'), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Domain  $domain
     * @param Comment $comment
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain, Comment $comment)
    {
        $this->authorize('delete', $comment);

        return response()->noContent();
    }
}
