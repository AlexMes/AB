<?php

namespace App\Reports\Placements;

use App\Offer;
use App\Queries\Placements;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class Report implements Arrayable, Responsable
{
    /**
     * @var Carbon
     */
    protected $since;
    /**
     * @var Carbon
     */
    protected $until;

    /**
     * Offers filter
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $offers;

    /**
     * Campaign filter
     *
     * @var string|null
     */
    protected $utmCampaign;

    /**
     *  Users filter
     *
     * @var \Illuminate\Support\Collection
     */
    protected $users;

    /**
     * Accounts filter
     *
     * /@var \Illuminate\Support\Collection
     */
    protected $accounts;

    /**
     * Placements filter
     *
     * /@var \Illuminate\Support\Collection
     */
    protected $placements;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $traffic;

    /**
     * Teams filter
     *
     * @var array
     */
    protected $teams;


    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\Placements\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'offers'        => $request->get('offers'),
            'users'         => $request->get('users'),
            'accounts'      => $request->get('accounts'),
            'utm'           => $request->get('utm'),
            'placements'    => $request->get('placements'),
            'traffic'       => $request->get('traffic'),
            'teams'         => $request->get('teams'),
        ]);
    }

    /**
     * DailyReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forCampaign($settings['utm'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forAccounts($settings['accounts'] ?? null)
            ->forPlacements($settings['placements'] ?? null)
            ->forTraffic($settings['traffic'] ?? null)
            ->forTeams($settings['teams']);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $placements = $this->getReportData();

        return [
            'headers' => ['offer','placement','leads','ftd','ftd %'],
            'rows'    => $this->rows($placements)->toArray(),
            'summary' => $this->summary($placements),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    /**
     * Get rows
     *
     * @param \Illuminate\Support\Collection $placements
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($placements)
    {
        return $placements->map(function ($offerPlacement) {
            $offerPlacement->ftdPercent = $this->percentage($offerPlacement->ftd, $offerPlacement->cnt);

            return $offerPlacement;
        });
    }

    /**
     * Get summary
     *
     * @param \Illuminate\Support\Collection $placements
     *
     * @return array
     */
    protected function summary($placements)
    {
        return [
            'name'       => 'ИТОГО',
            'placement'  => '',
            'cnt'        => $placements->sum('cnt'),
            'ftd'        => $placements->sum('ftd'),
            'ftdPercent' => $this->percentage($placements->sum('ftd'), $placements->sum('cnt')),
        ];
    }

    protected function percentage($one, $two)
    {
        if ($two) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
    }
    /**
     * Report data, formatted by database
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return Placements::fetch()
            ->forPeriod($this->since, $this->until)
            ->forOffers($this->offers->pluck('id'))
            ->forUsers($this->users)
            ->forAccounts($this->accounts)
            ->forUtmCampaign($this->utmCampaign)
            ->forPlacements($this->placements)
            ->forTraffic($this->traffic)
            ->forTeams($this->teams)
            ->get();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\Placements\Report
     */
    public function since($since = null)
    {
        if (is_null($since)) {
            $this->since = now();

            return $this;
        }

        $this->since = Carbon::parse($since);

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\Placements\Report
     */
    public function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param array $offers
     *
     * @return \App\Reports\Placements\Report
     */
    public function forOffers($offers = null)
    {
        if ($offers === null) {
            $this->offers = Offer::all();

            return $this;
        }

        $this->offers = Offer::whereIn('id', $offers)->get();

        return $this;
    }

    /**
     * Get response representation of the report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     *  Set utm campaign filter
     *
     * @param mixed $utmCampaign
     *
     * @return Report
     */
    public function forCampaign($utmCampaign = null)
    {
        $this->utmCampaign = $utmCampaign;

        return $this;
    }

    /**
     * Limit report for user
     *
     * @param null $users
     *
     * @return \App\Reports\Placements\Report
     */
    public function forUsers($users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Limit report for user
     *
     * @param null $accounts
     *
     * @return \App\Reports\Placements\Report
     */
    public function forAccounts($accounts = null)
    {
        $this->accounts = $accounts;

        return $this;
    }

    /**
     * @param array $placements
     *
     * @return Report
     */
    public function forPlacements($placements)
    {
        $this->placements = $placements;

        return $this;
    }

    /**
     * @param bool $traffic
     *
     * @return Report
     */
    public function forTraffic($traffic = null)
    {
        $this->traffic = $traffic;

        return $this;
    }

    /**
     * Filter by team
     *
     * @param null|array $teams
     *
     * @return $this
     */
    public function forTeams($teams = null)
    {
        $this->teams = $teams;

        return $this;
    }
}
