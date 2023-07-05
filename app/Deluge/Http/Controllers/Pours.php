<?php

namespace App\Deluge\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ManualPour;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Pours extends Controller
{
    /**
     * Pours constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualPour::class, 'pour');
    }

    /**
     * Index pours
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::pours.index', [
            'users'    => User::query()->withFacebookTraffic()->get(['id', 'name']),
            'pours'    => ManualPour::query()
                ->visible()
                ->notEmptyWhereIn('user_id', Arr::wrap($request->input('user')))
                ->orderByDesc('date')
                ->paginate(),
        ]);
    }

    /**
     * Show single pour page
     *
     * @param \App\ManualPour $pour
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualPour $pour)
    {
        return view('deluge::pours.show')->with([
            'pour'     => $pour,
            'accounts' => $pour->accounts()
                ->orderByDesc('created_at')
                ->paginate(50),
        ]);
    }

    /**
     * @param ManualPour $pour
     *
     * @throws \Exception
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(ManualPour $pour)
    {
        $pour->delete();

        return redirect(route('deluge.pours.index'))
            ->with('message', 'Залив был успешно удалён.');
    }
}
