<?php

namespace App\Reports\ResellDoubles;

use App\Offer;
use App\Office;
use App\ResellBatch;
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
 * @package App\Reports\ResellDoubles
 */
class Report implements Responsable, Arrayable
{
    public const GROUP_OFFICE                = 'office';

    public const GROUP_LIST = [
        self::GROUP_OFFICE,
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
     * @var array|null
     */
    protected $offices = null;

    /**
     * @var |null
     */
    protected $offers = null;

    /**
     * @var array|null
     */
    protected $officeGroups = null;


    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\ResellDoubles\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'groupBy'      => $request->get('groupBy'),
            'offices'      => $request->get('offices'),
            'offers'       => $request->get('offers'),
            'officeGroups' => $request->get('officeGroups'),
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
            ->forOffices($settings['offices'] ?? null)
            ->forOffers($settings['offers'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null);
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
        return ResellBatch::visible()
            ->autoCreated()
            ->select([
                DB::raw('count(lead_order_assignments.id) as leads')
            ])
            ->join('lead_resell_batch', 'resell_batches.id', 'lead_resell_batch.batch_id')
            ->join('lead_order_assignments', 'lead_resell_batch.assignment_id', 'lead_order_assignments.id')
            ->join('lead_order_routes', 'lead_order_assignments.route_id', 'lead_order_routes.id')
            ->join('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->when(self::GROUP_OFFICE === $this->groupBy, function (Builder $builder) {
                return $builder->addSelect([DB::raw('offices.name as group')])
                    ->join('offices', 'leads_orders.office_id', 'offices.id');
            })
            ->when($this->since && $this->until, function (Builder $builder) {
                return $builder->whereBetween(DB::raw('lead_order_assignments.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->offices, fn (Builder $builder) => $builder->whereIn('leads_orders.office_id', $this->offices))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('lead_order_routes.offer_id', $this->offers))
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('leads_orders.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
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
            $result = ['group' => $row->group];

            $result = array_merge($result, [
                'leads'     => $row->leads,
            ]);

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
        $result = ['group' => 'Итого'];

        $result = array_merge($result, [
            'leads'     => $data->sum('leads'),
        ]);

        return $result;
    }

    /**
     * Gets headers of the report
     *
     * @return string[]
     */
    protected function headers()
    {
        $result = [];

        foreach (explode('-', $this->groupBy) as $i => $item) {
            $result[$i > 0 ? $item : 'group'] = $item;
        }

        $result = array_merge($result, [
            'leads'     => 'leads',
        ]);

        return $result;
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
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = Office::visible()
            ->when($offices, fn ($q) => $q->whereIn('id', Arr::wrap($offices)))
            ->pluck('id')
            ->push(0)
            ->toArray();

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

    /**
     * @param array|null $groups
     *
     * @return Report
     */
    public function forOfficeGroups($groups = null)
    {
        $this->officeGroups = $groups;

        return $this;
    }
}
