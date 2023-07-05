<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pages\CreatePageRequest;
use App\Http\Requests\Pages\UpdatePageRequest;
use App\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Page::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Page[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        return Page::query()
            ->searchIn(['name'], $request->input('search'))
            ->when($request->has('all'), fn ($query) => $query->get(), fn ($query) => $query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePageRequest $request
     *
     * @return \App\Page|\Illuminate\Database\Eloquent\Model
     */
    public function store(CreatePageRequest $request)
    {
        return Page::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Page $page
     *
     * @return \App\Page
     */
    public function show(Page $page)
    {
        return $page;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePageRequest $request
     * @param \App\Page         $page
     *
     * @return bool
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        return tap($page)->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Page $page
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return response('No content', 204);
    }
}
