<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOfficeManager;
use App\Http\Requests\Offices\DeleteOfficeManager;
use App\Manager;
use App\Office;
use Illuminate\Http\Request;

class OfficeManagers extends Controller
{
    /**
     * Index office managers
     *
     * @param Office                   $office
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Office $office, Request $request)
    {
        $this->authorize('view', $office);

        return $office->managers()
            ->orderBy('id')
            ->when($request->has('paginate'), fn ($q) => $q->paginate(50), fn ($q) => $q->get());
    }

    /**
     * Create new manager
     *
     * @param Office              $office
     * @param CreateOfficeManager $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Office $office, CreateOfficeManager $request)
    {
        $data = $request->has('frx_role')
            ? $request->validated()
            : array_merge($request->validated(), ['frx_tenant_id' => $office->frx_tenant_id]);

        return $office->managers()->create($data);
    }

    /**
     * Deletes manager
     *
     * @param Office                                         $office
     * @param Manager                                        $manager
     * @param \App\Http\Requests\Offices\DeleteOfficeManager $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Office $office, Manager $manager, DeleteOfficeManager $request)
    {
        $manager->deleteAndTransferLeads(
            $office->managers()
                ->when(
                    $request->input('assign_type') == 'onlyManagers',
                    fn ($query) => $query->whereIn('id', $request->input('managers', [])),
                    fn ($query) => $query->whereNotIn('id', $request->input('managers', []))
                )
                ->where('id', '!=', $manager->id)
                ->get()
        );

        return response(null, 204);
    }
}
