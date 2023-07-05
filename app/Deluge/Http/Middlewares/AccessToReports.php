<?php

namespace App\Deluge\Http\Middlewares;

use Closure;

class AccessToReports
{
    protected array $allowedForBuyer = [
        'reports/performance',
        'reports/quiz',
        'reports/account-stats',
        'reports/buyer-stats',
        'reports/average-spend'
    ];

    protected array $allowedForTeamLead = [
        'reports/performance',
        'reports/quiz',
        'reports/account-stats',
        'reports/buyer-stats',
        'reports/exports/quiz',
        'reports/exports/performance',
        'reports/exports/buyer-costs',
    ];

    protected array $allowedForHead = [
        'reports/performance',
        'reports/quiz',
        'reports/account-stats',
        'reports/buyer-stats',
        'reports/average-spend',
        'reports/exports/quiz',
        'reports/exports/performance',
        'reports/exports/buyer-costs',
    ];

    protected array $allowedForDeveloper = [
        //
    ];

    protected array $allowedForDesigner = [
        'reports/performance',
        'reports/quiz',
    ];

    /**
     * Allowed reports for branch verifiers
     *
     * @var array
     */
    protected array $allowedForVerifier = [
        'reports/performance',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (auth()->user()->isVerifier() && $request->is($this->allowedForVerifier)) {
            return $next($request);
        }

        if (auth()->id() === 37 && $request->is('reports/lead-stats')) {
            return $next($request);
        }

        if (auth()->id() === 132 && $request->is(['reports/buyer-costs', 'reports/exports/buyer-costs'])) {
            return $next($request);
        }

        if (auth()->user()->isBuyer() && $request->is($this->allowedForBuyer)) {
            return $next($request);
        }

        if (auth()->user()->isTeamLead() && $request->is($this->allowedForTeamLead)) {
            return $next($request);
        }

        if (auth()->user()->isBranchHead() && $request->is($this->allowedForHead)) {
            return $next($request);
        }

        if (auth()->user()->isDeveloper() && $request->is($this->allowedForDeveloper)) {
            return $next($request);
        }

        if (auth()->user()->isDesigner() && $request->is($this->allowedForDesigner)) {
            return $next($request);
        }

        abort(403, 'You are not allowed to access this page.');
    }
}
