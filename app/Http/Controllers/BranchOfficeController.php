<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Requests\AttachOfficeToBranch;
use App\Http\Requests\DetachOfficeFromBranch;
use App\Office;

class BranchOfficeController extends Controller
{
    /**
     * @param Branch $branch
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Branch $branch)
    {
        $this->authorize('viewAny', Office::class);
        $this->authorize('view', $branch);

        return $branch->offices()->paginate();
    }

    /**
     * @param Branch               $branch
     * @param AttachOfficeToBranch $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Branch $branch, AttachOfficeToBranch $request)
    {
        $branch->offices()->syncWithoutDetaching($request->office_id);

        return response()->json($branch->offices()->where('offices.id', $request->office_id)->first());
    }

    /**
     * @param Branch                 $branch
     * @param Office                 $office
     * @param DetachOfficeFromBranch $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch, Office $office, DetachOfficeFromBranch $request)
    {
        $branch->offices()->detach($office->id);

        return response()->noContent();
    }
}
