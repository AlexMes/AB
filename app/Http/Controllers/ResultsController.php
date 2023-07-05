<?php

namespace App\Http\Controllers;

use App\Http\Requests\Results\CreateResultRequest;
use App\Http\Requests\Results\UpdateResultRequest;
use App\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResultsController extends Controller
{
    /**
     * OrdersController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Result::class, 'result');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Result::visible()
            ->with('offer', 'office')
            ->when(
                $request->has('since') && $request->has('until'),
                function (Builder $query) use ($request) {
                    return $query->whereBetween('date', [
                        $request->get('since'),
                        $request->get('until'),
                    ]);
                }
            )
            ->notEmptyWhereIn('offer_id', $request->get('offers'))
            ->notEmptyWhereIn('office_id', $request->get('offices'))
            ->orderByDesc('id')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateResultRequest $request
     *
     * @return \App\Result|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse
     */
    public function store(CreateResultRequest $request)
    {
        if (
            Result::where([
                'date' => $request->get('date'),
                'offer_id' => $request->get('offer_id'),
                'office_id' => $request->get('office_id'),
            ])->exists()
        ) {
            return response()->json([
                'message' => 'Результат для этой даты, оффера и оффиса уже существует.'
            ]);
        }

        return Result::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param Result $result
     *
     * @return Result
     */
    public function show(Result $result)
    {
        return $result->load('offer', 'office');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateResultRequest $request
     * @param Result              $result
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateResultRequest $request, Result $result)
    {
        return response()->json(tap($result)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Result $result
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return Response
     */
    public function destroy(Result $result)
    {
        $this->authorize('destroy', $result);

        $result->delete();

        return response('No Content', 204);
    }
}
