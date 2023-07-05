<?php

namespace App\Reports\LeadDuplicates;

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
 * @package App\Reports\LeadDuplicates
 */
class Report implements Responsable, Arrayable
{
    public const GROUP_OFFER    = 'offer';
    public const GROUP_CAMPAIGN = 'campaign';

    public const GROUPS = [
        self::GROUP_OFFER,
        self::GROUP_CAMPAIGN,
    ];

    public const TRAFFIC_AFFILIATED     = 'affiliated';
    public const TRAFFIC_NOT_AFFILIATED = 'not_affiliated';

    public const TRAFFIC = [
        self::TRAFFIC_AFFILIATED,
        self::TRAFFIC_NOT_AFFILIATED,
    ];

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
    protected $offers = null;

    /**
     * Determines how to group statistics
     *
     * @var string|null
     */
    protected $groupBy;

    /**
     * @var string
     */
    protected ?string $trafficType = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\LeadDuplicates\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
            'offers'      => $request->get('offers'),
            'groupBy'     => $request->get('groupBy'),
            'trafficType' => $request->get('trafficType'),
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
            ->groupBy($settings['groupBy'] ?? null)
            ->forTrafficType($settings['trafficType'] ?? null);
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
            'headers' => $this->headers(),
            'rows'    => $this->rows($data),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Collection
     */
    protected function aggregate()
    {
        $duplicates = $this->duplicates()->groupBy('group');

        return Lead::visible()
            ->allowedOffers()
            ->select([
                DB::raw('count(distinct leads.id) as leads'),
            ])
            ->when($this->groupBy === self::GROUP_OFFER, function (Builder $builder) {
                return $builder->addSelect([DB::raw('offers.name as group')])
                    ->leftJoin('offers', 'leads.offer_id', 'offers.id');
            })
            ->when($this->groupBy === self::GROUP_CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect([DB::raw('leads.utm_campaign as group')]);
            })
            ->when($this->since && $this->until, function (Builder $builder) {
                return $builder->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('leads.offer_id', $this->offers))
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, function (Builder $query) {
                return $query->whereNotNull('leads.affiliate_id');
            })
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, function (Builder $query) {
                return $query->whereNull('leads.affiliate_id');
            })
            ->groupBy(['group'])
            ->get()
            ->map(function ($item) use ($duplicates) {
                return [
                    'group'      => $item->group,
                    'leads'      => $item->leads,
                    'duplicates' => optional($duplicates->get($item->group))->sum('cnt') ?? 0,
                ];
            })
            ->reject(fn ($item) => $item['duplicates'] < 2);
    }

    /**
     * @return array|Builder[]|Collection|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Lead[]
     */
    protected function duplicates()
    {
        return Lead::visible()
            ->allowedOffers()
            ->select([
                DB::raw('leads.phone'),
                DB::raw('count(leads.id) as cnt'),
            ])
            ->when($this->groupBy === self::GROUP_OFFER, function (Builder $builder) {
                return $builder->addSelect([DB::raw('offers.name as group')])
                    ->leftJoin('offers', 'leads.offer_id', 'offers.id');
            })
            ->when($this->groupBy === self::GROUP_CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect([DB::raw('leads.utm_campaign as group')]);
            })
            ->when($this->since && $this->until, function (Builder $builder) {
                return $builder->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('leads.offer_id', $this->offers))
            ->when($this->trafficType === self::TRAFFIC_AFFILIATED, function (Builder $query) {
                return $query->whereNotNull('leads.affiliate_id');
            })
            ->when($this->trafficType === self::TRAFFIC_NOT_AFFILIATED, function (Builder $query) {
                return $query->whereNull('leads.affiliate_id');
            })
            ->groupBy(['group', 'phone'])
            ->havingRaw('count(leads.id) > 1')
            ->get();
    }

    /**
     * @param Collection $data
     *
     * @return Collection
     */
    protected function rows(Collection $data)
    {
        return $data->map(function ($row) use ($data) {
            return [
                'group'      => $row['group'],
                'leads'      => $row['leads'],
                'duplicates' => $row['duplicates'],
            ];
        });
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        return [
            'group'      => 'Итого',
            'leads'      => $data->sum('leads'),
            'duplicates' => $data->sum('duplicates'),
        ];
    }

    /**
     * Gets headers of the report
     *
     * @return string[]
     */
    protected function headers()
    {
        return [
            'group'      => 'group',
            'leads'      => 'leads',
            'duplicates' => 'duplicates',
        ];
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return Report
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
     * @return Report
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
     * @param null $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        if (!empty($offers)) {
            $this->offers = $offers;
        }

        return $this;
    }

    /**
     * @param null $trafficType
     *
     * @return Report
     */
    public function forTrafficType($trafficType = null)
    {
        if (!empty($trafficType)) {
            $this->trafficType = in_array($trafficType, self::TRAFFIC) ? $trafficType : null;
        }

        return $this;
    }

    /**
     * @param string $groupBy
     *
     * @return Report
     */
    public function groupBy($groupBy = 'offer')
    {
        $this->groupBy = in_array($groupBy, self::GROUPS) ? $groupBy : self::GROUP_OFFER;

        return $this;
    }
}
