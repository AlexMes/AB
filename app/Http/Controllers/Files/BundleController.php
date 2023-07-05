<?php

namespace App\Http\Controllers\Files;

use App\Bundle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Bundle $bundle)
    {
        $this->authorize('viewAny', Bundle::class);

        return $bundle->media()
            ->paginate();
    }

    /**
     * Display the specified resource.
     *
     * @param Bundle $bundle
     * @param Media  $file
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return Media
     */
    public function show(Bundle $bundle, Media $file)
    {
        $this->authorize('view', $bundle);

        return $file;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Bundle  $bundle
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Bundle $bundle)
    {
        $this->authorize('create', $bundle);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $bundle->clearMediaCollection();
            $file = $bundle->addMediaFromRequest('file')->toMediaCollection();

            return response()->json($file, 201);
        }

        return abort(422, 'Wrong file data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Bundle $bundle
     * @param $mediaId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bundle $bundle, $mediaId)
    {
        $this->authorize('delete', $bundle);

        $bundle->deleteMedia($mediaId);

        return response(null, 204);
    }
}
