<?php

namespace App\Reports\LeftoversByBuyers;

use App\Lead;
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
 * @package App\Reports\LeftoversByBuyers
 */
class Report implements Responsable, Arrayable
{
    public const GROUP_OFFER     = 'offer';
    public const GROUP_USER      = 'user';
    public const GROUP_BRANCH    = 'branch';
    public const GROUP_DATE      = 'date';

    public const GROUP_LIST = [
        self::GROUP_OFFER,
        self::GROUP_USER,
        self::GROUP_BRANCH,
        self::GROUP_DATE,
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
     * @var array|null
     */
    protected $users = null;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\LeftoversByBuyers\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
            'offers'   => $request->get('offers'),
            'branches' => $request->get('branches'),
            'users'    => $request->get('users'),
            'groupBy'  => $request->get('groupBy'),
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
            ->forUsers($settings['users'] ?? null)
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
        return Lead::leftovers()
            ->selectRaw("count(leads.id) leads_count")
            ->leftJoin('users', 'leads.user_id', 'users.id')
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween(DB::raw('leads.created_at::date'), [$this->since, $this->until]);
            })
            ->when($this->users, fn (Builder $query) => $query->whereIn('leads.user_id', $this->users))
            ->when($this->branches, fn (Builder $builder) => $builder->whereIn('users.branch_id', $this->branches))
            ->when($this->offers, fn (Builder $builder) => $builder->whereIn('leads.offer_id', $this->offers))
            ->when($this->groupBy === self::GROUP_USER, function (Builder $builder) {
                return $builder->addSelect([DB::raw('users.name as group')]);
            })
            ->when($this->groupBy === self::GROUP_DATE, function (Builder $builder) {
                return $builder->addSelect([DB::raw('leads.created_at::date as group')]);
            })
            ->when($this->groupBy === self::GROUP_OFFER, function (Builder $builder) {
                return $builder->addSelect([DB::raw('offers.name as group')])
                    ->leftJoin('offers', 'leads.offer_id', 'offers.id');
            })
            ->when($this->groupBy === self::GROUP_BRANCH, function (Builder $builder) {
                return $builder->addSelect([DB::raw('branches.name as group')])
                    ->leftJoin('branches', 'users.branch_id', 'branches.id');
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
        return $data->map(function ($row) {
            return [
                'group'       => $row->group,
                'leads_count' => $row->leads_count,
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
            'leads_count' => $data->sum('leads_count'),
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
            'leads_count' => 'lead count',
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
    public function groupBy(string $groupBy = 'offer')
    {
        $this->groupBy = in_array($groupBy, self::GROUP_LIST) ? $groupBy : self::GROUP_OFFER;

        return $this;
    }

    /**
     * @param array|null $branches
     *
     * @return Report
     */
    public function forBranches($branches = null)
    {
        if (!empty($branches)) {
            $this->branches = Arr::wrap($branches);
        }

        return $this;
    }

    /**
     * @param array|null $users
     *
     * @return Report
     */
    public function forUsers($users = null)
    {
        if (!empty($users)) {
            $this->users = Arr::wrap($users);
        }

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
