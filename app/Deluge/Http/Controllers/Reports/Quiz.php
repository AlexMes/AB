<?php

namespace App\Deluge\Http\Controllers\Reports;

use App\Branch;
use App\Deluge\Http\Middlewares\AccessToReports;
use App\Deluge\Http\Middlewares\CheckOffice;
use App\Deluge\Reports\Quiz\Report;
use App\Http\Controllers\Controller;
use App\ManualBundle;
use App\Offer;
use App\Team;
use App\Trail;
use App\User;
use Illuminate\Http\Request;

class Quiz extends Controller
{
    /**
     * PerformanceReport constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([CheckOffice::class, AccessToReports::class]);
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        $start = microtime(true);

        app(Trail::class)->add(sprintf('Start report loading for a %s (%s). #deluge', auth()->user()->name, auth()->id()));
        $bundles = cache()
            ->remember(
                sprintf('quiz-%s-bundles', auth()->id()),
                now()->addMinutes(30),
                fn () => ManualBundle::allowedOffers()->orderBy('name', 'asc')->get(['id', 'name'])
            );


        app(Trail::class)->add(sprintf('Bundles %s loading for took %s', $bundles->count(), microtime(true) - $start));

        $view = view('deluge::reports.quiz')
            ->with(
                [
                    'offers' => cache()
                        ->remember(
                            sprintf('quiz-%s-offers', auth()->id()),
                            now()->addMinutes(30),
                            fn () => Offer::allowed()->get(['id', 'name', 'vertical'])
                        ),
                    'users' => cache()
                        ->remember(
                            sprintf('quiz-%s-users', auth()->id()),
                            now()->addMinutes(30),
                            fn () => User::forBranchStats()->forAllowedBranches()->withFacebookTraffic()->get(['id', 'name'])
                        ),
                    'branches' => cache()
                        ->remember(
                            sprintf('quiz-%s-branches', auth()->id()),
                            now()->addMinutes(30),
                            fn () => Branch::allowed()->get(['id', 'name'])
                        ),
                    'teams' => cache()
                        ->remember(
                            sprintf('quiz-%s-teams', auth()->id()),
                            now()->addMinutes(30),
                            fn () => Team::visible()->get(['id', 'name'])
                        ),
                    'bundles' => $bundles,
                    'report'  => Report::fromRequest($request)->toArray(),
                ]
            );

        app(Trail::class)->add(
            sprintf('Full report loading took %s', microtime(true) - $start)
        );

        return $view;
    }
}
