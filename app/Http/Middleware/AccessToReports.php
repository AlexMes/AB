<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessToReports
{
    /**
     * Allowed reports for support role
     *
     * @var array|string[]
     */
    protected array $allowedForSupport = [
        'api/reports/lead-manager-assignments',
        'api/reports/affiliates',
        'api/reports/lead-office-assignments',
        'api/reports/daily-opt',
        'api/reports/leads-received',
        'api/reports/office-performance',
        'api/reports/office-affiliate-performance',
        'api/reports/lead-stats',
        '/api/reports/night-shift',
        '/api/reports/conversion-stats',
        '/api/reports/conversion-timeline',
        'api/reports/revise',
        'api/reports/revise-v2',
        'api/reports/offer-source',

        'api/reports/exports/revise',
        'api/reports/exports/revise-v2',
        'api/reports/exports/lead-stats',
        'api/reports/leftovers-by-buyers',
    ];

    /**
     * Allowed reports for sub-support role
     *
     * @var array|string[]
     */
    protected array $allowedForSubSupport = [
        'api/reports/lead-manager-assignments',
        'api/reports/lead-office-assignments',
        'api/reports/leads-received',
        'api/reports/office-performance',
        'api/reports/office-affiliate-performance',
    ];

    /**
     * Allowed reports for sales role
     *
     * @var array|string[]
     */
    protected array $allowedForSales = [
        'api/reports/lead-stats',
        'api/reports/affiliates',
        'api/reports/office-performance',
        'api/reports/office-affiliate-performance',
        'api/reports/conversion-stats',
        'api/reports/lead-manager-assignments'
    ];

    /**
     * Allowed reports for branch heads
     *
     * @var array
     */
    protected array $allowedForHeads = [
        'api/reports/lead-stats',
        'api/reports/office-performance',
        'api/reports/office-affiliate-performance',
        'api/reports/gender',
        'api/reports/regions',
        'api/reports/os',
        'api/reports/lead-manager-assignments',
        'api/reports/statistic',
        'api/reports/performance',
        'api/reports/placements',
        'api/reports/revise',
        'api/reports/revise-v2',
        'api/reports/affiliates',
        'api/reports/resell-doubles',
        'api/reports/lead-duplicates',

        'api/reports/exports/revise',
        'api/reports/exports/revise-v2',
        'api/reports/exports/lead-stats',
        'api/reports/leftovers-by-buyers',
    ];

    /**
     * Allowed reports for branch verifiers
     *
     * @var array
     */
    protected array $allowedForVerifier = [
        'api/reports/performance',
        'api/reports/facebook-performance',
        'api/reports/exports/facebook-performance',

    ];

    /**
     * Allowed reports for branch verifiers
     *
     * @var array
     */
    protected array $allowedForTeamLead = [
        'api/reports/performance',
        'api/reports/statistics',
        'api/reports/placements',
        'api/reports/gender',
        'api/reports/regions',
        'api/reports/os',
        'api/reports/lead-stats',
        'api/reports/revise',
        'api/reports/revise-v2',
        'api/reports/offer-source',

        'api/reports/exports/performance',
        'api/reports/exports/revise',
        'api/reports/exports/revise-v2',
    ];

    /**
     * @var array
     */
    protected array $specificPermissions = [
        [
            'branch_id' => 19,
            'allowed'   => [
                'api/reports/dtd-cr',
                'api/reports/rejected-unique',
            ],
        ],
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
        if (auth()->user()->isAdmin() || auth()->id() === 132) {
            return $next($request);
        }

        $permission = collect($this->specificPermissions)->where('email', auth()->user()->email)->first();
        if ($permission === null && auth()->user()->branch_id !== null) {
            $permission = collect($this->specificPermissions)->where('branch_id', auth()->user()->branch_id)->first();
        }

        if ($permission !== null && $request->is($permission['allowed'])) {
            return $next($request);
        }

        if (auth()->user()->isVerifier() && $request->is($this->allowedForVerifier)) {
            return $next($request);
        }

        if (auth()->user()->isSubSupport() && $request->is($this->allowedForSupport)) {
            return $next($request);
        }

        if (auth()->user()->isSupport() && $request->is($this->allowedForSupport)) {
            return $next($request);
        }

        if (auth()->user()->isSales() && $request->is($this->allowedForSales)) {
            return $next($request);
        }

        if (auth()->user()->isBranchHead() && $request->is($this->allowedForHeads)) {
            return $next($request);
        }

        if (auth()->user()->isTeamLead() && $request->is($this->allowedForTeamLead)) {
            return $next($request);
        }

        abort(403, 'This action is not authorized.');
    }
}
