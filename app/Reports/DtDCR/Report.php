<?php

namespace App\Reports\DtDCR;

use App\LeadOrderAssignment;
use App\Offer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Report
 *
 * @package App\Reports\DtDCR
 */
class Report implements Responsable, Arrayable
{
    public const GROUP_OFFICE            = 'office';
    public const GROUP_USER              = 'user';

    public const GROUP_LIST = [
        self::GROUP_OFFICE,
        self::GROUP_USER,
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
     * Determines how to group statistics
     *
     * @var string|null
     */
    protected $groupBy;

    /**
     * @var |null
     */
    protected $branches = null;

    /**
     * @var |null
     */
    protected $offers = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\DtDCR\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'           => $request->get('since'),
            'until'           => $request->get('until'),
            'groupBy'         => $request->get('groupBy'),
            'offers'          => $request->get('offers'),
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
            ->groupBy($settings['groupBy'] ?? null)
            ->forBranches($settings['branches'] ?? null)
            ->forOffers($settings['offers'] ?? null);
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
     * @return Collection
     */
    protected function aggregate()
    {
        return LeadOrderAssignment::visible()
            ->selectRaw("
                count(
                    CASE WHEN lead_order_assignments.created_at::date = leads.created_at::date
                    THEN 1 END
                ) AS did_lead,
                count(
                    CASE WHEN lead_order_assignments.created_at::date = leads.created_at::date
                        AND deposits.id IS NOT NULL
                    THEN 1 END
                ) AS did_deposit,
                count(
                    CASE WHEN lead_order_assignments.created_at::date != leads.created_at::date
                    THEN 1 END
                ) AS nid_lead,
                count(
                    CASE WHEN lead_order_assignments.created_at::date != leads.created_at::date
                        AND deposits.id IS NOT NULL
                    THEN 1 END
                ) AS nid_deposit
            ")
            ->join('leads', 'lead_order_assignments.lead_id', 'leads.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('users', 'leads.user_id', 'users.id')
            /*->join('branches', 'users.branch_id', 'branches.id')*/
            ->leftJoin('deposits', 'leads.id', 'deposits.lead_id')
            ->when($this->groupBy === self::GROUP_OFFICE, function (Builder $builder) {
                return $builder->join('offices', 'leads_orders.office_id', 'offices.id')
                    ->addSelect([DB::raw('offices.name as group')]);
            })
            ->when($this->groupBy === self::GROUP_USER, function (Builder $builder) {
                return $builder->addSelect([DB::raw('users.name as group')]);
            })
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->branches, fn (Builder $builder) => $builder->whereIn('users.branch_id', $this->branches))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('lead_order_routes.offer_id', $this->offers))
            ->groupBy(['group'])
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
                'group'       => $row->group,
                'did_lead'    => $row->did_lead,
                'did_deposit' => $row->did_deposit,
                'nid_lead'    => $row->nid_lead,
                'nid_deposit' => $row->nid_deposit,
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
            'group'       => 'Итого',
            'did_lead'    => $data->sum('did_lead'),
            'did_deposit' => $data->sum('did_deposit'),
            'nid_lead'    => $data->sum('nid_lead'),
            'nid_deposit' => $data->sum('nid_deposit'),
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
            'group'       => $this->groupBy,
            'did_lead'    => 'did lead',
            'did_deposit' => 'did deposit',
            'nid_lead'    => 'nid lead',
            'nid_deposit' => 'nid deposit',
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
     * @param string $groupBy
     *
     * @return Report
     */
    public function groupBy($groupBy = 'office')
    {
        $this->groupBy = in_array($groupBy, self::GROUP_LIST) ? $groupBy : self::GROUP_OFFICE;

        return $this;
    }

    /**
     * @param array|null $branches
     *
     * @return Report
     */
    public function forBranches($branches = null)
    {
        /*if (!empty($branches)) {
            $this->branches = Arr::wrap($branches);
        }*/
        $this->branches = [19];

        return $this;
    }

    /**
     * @param null $offers
     *
     * @return Report
     */
    public function forOffers($offers = null)
    {
        $this->offers = Offer::allowed()
            ->when($offers, fn ($q) => $q->whereIn('id', Arr::wrap($offers)))
            ->pluck('id')
            ->push(0)
            ->toArray();

        return $this;
    }
}
