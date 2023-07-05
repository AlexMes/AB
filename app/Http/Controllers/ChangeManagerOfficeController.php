<?php

namespace App\Http\Controllers;

use App\Http\Requests\Managers\ChangeOffice;
use App\Manager;
use App\Office;

class ChangeManagerOfficeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(ChangeOffice $request, Manager $manager)
    {
        $manager->changeOffice(Office::findOrFail($request->input('office_id')));

        return response()->json($manager->fresh(), 202);
    }
}
