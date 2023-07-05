<?php

namespace App\Reports\OfferSource;

use App\Lead;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\OfferSource
 */
class Report implements Responsable, Arrayable
{
    /**
     * Start date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * End date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * @var |null
     */
    protected $offers;

    /**
     * @var mixed|null
     */
    protected $traffic;

    /**
     * @var array|null
     */
    protected $users;

    /**
     * @var array|null
     */
    protected $leads;

    /**
     * @var bool
     */
    protected bool $groupByUtmSource = true;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\OfferSource\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'            => $request->get('since'),
            'until'            => $request->get('until'),
            'offers'           => $request->get('offers'),
            'traffic'          => $request->get('traffic'),
            'users'            => $request->get('users'),
            'leads'            => $request->get('leads'),
            'groupByUtmSource' => $request->boolean('groupByUtmSource', true),
        ]);
    }

    /**
     * Report constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forOffers($settings['offers'] ?? null)
            ->forTraffic($settings['traffic'] ?? null)
            ->forUsers($settings['users'] ?? null)
            ->forLeads($settings['leads'] ?? null)
            ->groupByUtmSource($settings['groupByUtmSource'] ?? true);
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
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->aggregate();

        return [
            'headers'  => $this->headers(),
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Lead[]|array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return Lead::visible()
            ->allowedOffers()
            ->select([
                DB::raw('offers.name as offer'),
                DB::raw('leads.email as email'),
            ])
            ->when($this->groupByUtmSource, fn ($query) => $query->addSelect('leads.utm_source as source'))
            ->leftJoin('offers', 'leads.offer_id', 'offers.id')
            ->when($this->traffic === 'own', fn ($query) => $query->own())
            ->when($this->traffic === 'affiliate', fn ($query) => $query->fromAffiliates())
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offers, fn ($query) => $query->whereIn('offers.id', $this->offers))
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->leads, function ($query) {
                return $query->whereIn('leads.email', $this->leads);
            })
            ->orderByDesc('leads.created_at')
            ->get()
            ->unique('email')
            ->groupBy(function (Lead $lead, $key) {
                return $lead->offer . ($this->groupByUtmSource ? '###' . $lead->source : '');
            })
            ->map(function (Collection $leads, $key) {
                $keys = explode('###', $key);

                $result = new \stdClass();
                $result->offer = $keys[0];
                $result->leads = $leads->count();
                $result->source = $keys[1] ?? '';

                return $result;
            });

        /*return Lead::visible()
            ->allowedOffers()
            ->select([
                DB::raw('offers.name as offer'),
                DB::raw('count(distinct leads.id) AS leads'),
            ])
            ->when($this->groupByUtmSource, fn ($query) => $query->addSelect('leads.utm_source as source'))
            ->leftJoin('offers', 'leads.offer_id', 'offers.id')
            ->when($this->traffic === 'own', fn ($query) => $query->own())
            ->when($this->traffic === 'affiliate', fn ($query) => $query->fromAffiliates())
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offers, fn ($query) => $query->whereIn('offers.id', $this->offers))
            ->when($this->users, fn ($query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->leads, function ($query) {
                return $query->whereIn('leads.email', $this->leads);
            })
            ->groupBy(['offer'])
            ->when($this->groupByUtmSource, fn ($query) => $query->groupBy('leads.utm_source'))
            ->get();*/
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return $data
            ->map(function ($row) use ($data) {
                $result = [
                    'offer'  => $row->offer,
                    'source' => $row->source,
                    'leads'  => $row->leads,
                ];

                if (!$this->groupByUtmSource) {
                    unset($result['source']);
                }

                return $result;
            });
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        $result = [
            'offer'  => 'Итого',
            'source' => '',
            'leads'  => $data->sum('leads'),
        ];

        if (!$this->groupByUtmSource) {
            unset($result['source']);
        }

        return $result;
    }

    /**
     * Gets headers of the report
     *
     * @return string[]
     */
    protected function headers()
    {
        $result = [
            'offer'  => 'offer',
            'source' => 'UTM source',
            'leads'  => 'leads',
        ];

        if (!$this->groupByUtmSource) {
            unset($result['source']);
        }

        return $result;
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return $this
     */
    public function since($since = null)
    {
        $this->since = now()->subYears(10);

        return $this;

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
     * @return $this
     */
    public function until($until = null)
    {
        $this->until = now();

        return $this;

        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * @param null $offers
     *
     * @return $this
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * @param $traffic
     *
     * @return $this
     */
    protected function forTraffic($traffic = null)
    {
        $this->traffic = $traffic;

        return $this;
    }

    /**
     * @param array|null $users
     *
     * @return Report
     */
    public function forUsers($users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @param array|null $leads
     *
     * @return Report
     */
    public function forLeads($leads = null)
    {
        $this->leads = $leads;

        return $this;
    }

    /**
     * @param bool $groupByUtmSource
     *
     * @return $this
     */
    public function groupByUtmSource(bool $groupByUtmSource = true)
    {
        $this->groupByUtmSource = $groupByUtmSource;

        return $this;
    }
}
