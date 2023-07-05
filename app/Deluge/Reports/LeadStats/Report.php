<?php

namespace App\Deluge\Reports\LeadStats;

use App\Deposit;
use App\LeadOrderAssignment;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Report implements Arrayable, Responsable
{
    public const CONTENT  = 'utm_content';
    public const CAMPAIGN = 'utm_campaign';

    public const LEVELS = [
        self::CONTENT,
        self::CAMPAIGN,
    ];

    /**
     * @var Carbon
     */
    protected $since;

    /**
     * @var Carbon
     */
    protected $until;

    /**
     * @var array|null
     */
    protected ?array $offers;

    /**
     * @var array|null
     */
    protected ?array $offices;

    /**
     * @var string|null
     */
    protected ?string $level = null;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var array|null
     */
    protected ?array $officeGroups;

    /**
     * @var string
     */
    protected string $sortBy = 'name';

    /**
     * @var boolean
     */
    protected bool $descending = false;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return self
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'offers'        => $request->get('offers'),
            'offices'       => $request->get('offices'),
            'level'         => $request->get('level'),
            'vertical'      => $request->get('vertical'),
            'office_groups' => $request->get('office_groups'),
            'sort_by'       => $request->get('sort'),
            'descending'    => $request->boolean('desc'),
        ]);
    }

    /**
     * RegionsReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now()->startOfMonth())
            ->until($settings['until'] ?? now())
            ->forLevel($settings['level'] ?? self::CONTENT)
            ->forOffers($settings['offers'])
            ->forOffices($settings['offices'])
            ->forVertical($settings['vertical'])
            ->forOfficeGroups($settings['office_groups']);

        $this->sortBy($settings['sort_by'], $settings['descending']);
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
     * @inheritDoc
     */
    public function toArray()
    {
        $data = $this->getReportData();

        return [
            'headers' => $this->headers(),
            'rows'    => $this->rows($data)->toArray(),
            'summary' => $this->summary($data),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    protected function headers()
    {
        return [
            'name'        => 'office',
            'level'       => 'level',
            'leads'       => 'leads',
            'ftd'         => 'ftd',
            'rate'        => 'ftd%',
            'no_answer'   => 'no answer',
            'double'      => 'double',
            'reject'      => 'reject',
            'on_closer'   => 'on closer',
        ];
    }

    /**
     * Get rows
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(function ($item) {
            return [
                'name'        => $item->office,
                'level'       => $item->level,
                'leads'       => $item->leads ?? 0,
                'ftd'         => $item->ftd ?? 0,
                'rate'        => sprintf('%s %%', percentage($item->ftd, $item->leads)),
                'no_answer'   => $item->no_answer ?? 0,
                'double'      => $item->double ?? 0,
                'reject'      => $item->reject ?? 0,
                'on_closer'   => $item->on_closer ?? 0,
            ];
        })->sortBy($this->sortBy, SORT_REGULAR, $this->descending);
    }

    /**
     * Get summary
     *
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    protected function summary($data)
    {
        return [
            'name'        => 'Итого',
            'level'       => '',
            'leads'       => $data->sum('leads') ?? 0,
            'ftd'         => $data->sum('ftd') ?? 0,
            'rate'        => sprintf('%s %%', percentage($data->sum('ftd'), $data->sum('leads'))),
            'no_answer'   => $data->sum('no_answer') ?? 0,
            'double'      => $data->sum('double') ?? 0,
            'reject'      => $data->sum('reject') ?? 0,
            'on_closer'   => $data->sum('on_closer') ?? 0,
        ];
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return LeadOrderAssignment::visible()->allowedOffers()
            ->select([
                DB::raw('offices.name AS office'),
                DB::raw('count(distinct lead_order_assignments.id) AS leads'),
                DB::raw("count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS no_answer"),
                DB::raw("count(CASE WHEN lead_order_assignments.status = 'Дубль' THEN 1 END) AS double"),
                DB::raw("count(CASE WHEN lead_order_assignments.status = 'Отказ' THEN 1 END) AS reject"),
                DB::raw("count(CASE WHEN lead_order_assignments.status = 'В работе у клоузера' THEN 1 END) AS on_closer"),
                DB::raw('count(distinct deps.id) AS ftd'),
            ])
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('offices', 'leads_orders.office_id', 'offices.id')
            ->when($this->level === self::CONTENT, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_content as level'))
                    ->groupBy(['leads.utm_content']);
            })
            ->when($this->level === self::CAMPAIGN, function (Builder $builder) {
                return $builder->addSelect(DB::raw('leads.utm_campaign as level'))
                    ->groupBy(['leads.utm_campaign']);
            })
            ->leftJoinSub(
                Deposit::allowedOffers()
                    ->select(['id', 'lead_id', 'lead_return_date', 'office_id'])
                    ->whereBetween('date', [$this->since, $this->until])
                    ->when($this->offers, fn ($query) => $query->whereIn('deposits.offer_id', $this->offers))
                    ->when($this->verticalOffers, fn ($query) => $query->whereIn('deposits.offer_id', $this->verticalOffers))
                    ->when($this->offices, fn ($query) => $query->whereIn('deposits.office_id', $this->offices)),
                'deps',
                function (JoinClause $join) {
                    return $join->on('lead_order_assignments.lead_id', 'deps.lead_id')
                        ->whereColumn(DB::raw('lead_order_assignments.created_at::date'), 'deps.lead_return_date')
                        ->whereColumn('leads_orders.office_id', 'deps.office_id');
                }
            )
            ->whereBetween(
                DB::raw('lead_order_assignments.registered_at::date'),
                [$this->since->toDateString(), $this->until->toDateString()]
            )
            ->when($this->offers, fn ($query) => $query->whereIn('lead_order_routes.offer_id', $this->offers))
            ->when($this->verticalOffers, fn ($query) => $query->whereIn('lead_order_routes.offer_id', $this->verticalOffers))
            ->when($this->offices, fn ($query) => $query->whereIn('leads_orders.office_id', $this->offices))
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->groupBy(['offices.name'])
            ->get();
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
        if (is_null($since)) {
            $this->since = now()->startOfMonth();

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
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * Filters by offers
     *
     * @param array|null $offers
     *
     * @return $this
     */
    public function forOffers(?array $offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Filter by offices
     *
     * @param null|array $offices
     *
     * @return $this
     */
    public function forOffices($offices = null)
    {
        $this->offices = $offices;

        return $this;
    }

    /**
     * @param string $level
     *
     * @return $this
     */
    public function forLevel(string $level)
    {
        $this->level = in_array($level, self::LEVELS) ? $level : self::CONTENT;

        return $this;
    }

    /**
     * Filters by offer's vertical
     *
     * @param string|null $vertical
     *
     * @return $this
     */
    public function forVertical(?string $vertical = null)
    {
        if ($vertical !== null) {
            $this->verticalOffers = Offer::allowed()
                ->where('vertical', $vertical)
                ->pluck('id')
                ->push(0)
                ->toArray();
        } else {
            $this->verticalOffers = null;
        }

        return $this;
    }

    /**
     * @param null|array $groups
     *
     * @return $this
     */
    public function forOfficeGroups($groups = null)
    {
        $this->officeGroups = $groups;

        return $this;
    }

    /**
     * @param string|null $sortBy
     * @param bool|null   $descending
     *
     * @return $this
     */
    public function sortBy(?string $sortBy, ?bool $descending = false)
    {
        if ($sortBy !== null && array_key_exists($sortBy, $this->headers())) {
            $this->sortBy = $sortBy;
        }

        $this->descending = $descending ?? false;

        return $this;
    }
}
