<?php

namespace App\Http\Controllers;

use App\Office;
use App\OfficePayment;
use Illuminate\Http\Request;

class OfficePayments extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Office $office, Request $request)
    {
        $this->authorize('viewAny', OfficePayment::class);

        return $office->payments()
            ->orderByDesc('created_at')
            ->when(
                $request->has('all'),
                fn ($q) => $q->get(),
                fn ($q) => $q->paginate()
            );
    }
}
