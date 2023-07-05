<?php

namespace App\Http\Controllers\External;

use App\Affiliate;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExtenalLead;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ListLeads extends Controller
{
    /**
     * List leads for the affiliates
     *
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        $affiliate = Affiliate::whereApiKey($request->query('api_key'))->first();

        throw_unless($affiliate, AuthenticationException::class);

        return ExtenalLead::collection(
            $affiliate->leads()
                ->searchIn(['uuid','phone','email'], $request->get('search'))
                ->when(
                    $request->filled('since'),
                    fn ($query) => $query->whereDate('created_at', '>=', $request->get('since'))
                )
                ->when(
                    $request->filled('until'),
                    fn ($query) => $query->whereDate('created_at', '<=', $request->get('until'))
                )
                ->paginate($request->get('perPage') ?? 100)
        );
    }
}
