<?php

namespace App\Deluge\Reports\BuyerCosts;

use App\ManualInsight;
use App\Offer;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Offer filter
     *
     * @var array|null
     */
    protected ?array $offers;

    /**
     * @var array|null
     */
    protected ?array $users;

    /**
     * @var array|null
     */
    protected ?array $teams;

    /**
     * @var array|null
     */
    protected ?array $bundles = null;

    /**
     * @var array|null
     */
    protected ?array $verticalOffers;

    /**
     * @var bool
     */
    protected bool $groupByAccount = false;

    /**
     * @var string
     */
    protected string $sortBy = 'date';

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
            'users'         => $request->get('users'),
            'teams'         => $request->get('teams'),
            'bundles'       => $request->get('bundles'),
            'vertical'      => $request->get('vertical'),
            'by_account'    => $request->boolean('by_account'),
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
            ->forOffers($settings['offers'])
            ->forUsers($settings['users'])
            ->forTeams($settings['teams'])
            ->forBundles($settings['bundles'])
            ->forVertical($settings['vertical'])
            ->groupByAccount($settings['by_account'] ?? false);

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
        return collect([
            'date'       => 'date',
            'buyer'      => 'buyer',
            'account_id' => 'account_id',
            'cost'       => 'cost',
        ])->reject(function ($value, $key) {
            if (!$this->groupByAccount) {
                return $key === 'account_id';
            }

            return false;
        })->toArray();
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
            return collect([
                'date'       => $item->date ?? '',
                'buyer'      => $item->buyer ?? '',
                'account_id' => $item->account_id ?? '',
                'cost'       => $item->spend ?? 0,
            ])->reject(function ($value, $key) {
                if (!$this->groupByAccount) {
                    return $key === 'account_id';
                }

                return false;
            })->toArray();
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
        return collect([
            'date'       => 'Итого',
            'buyer'      => '',
            'account_id' => '',
            'cost'       => $data->sum('spend'),
        ])->reject(function ($value, $key) {
            if (!$this->groupByAccount) {
                return $key === 'account_id';
            }

            return false;
        })->toArray();
    }

    /**
     * Report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getReportData()
    {
        return ManualInsight::visible()->allowedOffers()
            ->select([
                DB::raw('manual_insights.date as date'),
                DB::raw('users.name as buyer'),
                DB::raw('sum(manual_insights.spend) as spend'),
            ])
            ->join('manual_accounts', 'manual_insights.account_id', 'manual_accounts.account_id')
            ->leftJoin('users', 'manual_accounts.user_id', 'users.id')
            ->when($this->groupByAccount, function (Builder $builder) {
                return $builder->addSelect('manual_insights.account_id')
                    ->groupBy('manual_insights.account_id');
            })
            ->when($this->since, fn ($query) => $query->whereDate('manual_insights.date', '>=', $this->since))
            ->when($this->until, fn ($query) => $query->whereDate('manual_insights.date', '<=', $this->until))
            ->when($this->users, fn (Builder $builder) => $builder->whereIn('manual_accounts.user_id', $this->users))
            ->when($this->offers, function (Builder $builder) {
                return $builder->whereHas('campaign', function ($query) {
                    return $query->whereHas('bundle', fn ($q) => $q->whereIn('offer_id', $this->offers));
                });
            })
            ->when($this->teams, function (Builder $builder) {
                return $builder->whereExists(function ($query) {
                    return $query->select(DB::raw(1))
                        ->from('team_user')
                        ->whereColumn('team_user.user_id', '=', 'manual_accounts.user_id')
                        ->whereIn('team_user.team_id', $this->teams);
                });
            })
            ->when($this->bundles, function (Builder $builder) {
                return $builder->whereHas('campaign', fn ($query) => $query->whereIn('bundle_id', $this->bundles));
            })
            ->when($this->verticalOffers, function ($query) {
                return $query->whereHas('campaign', function ($q) {
                    return $q->whereHas('bundle', fn ($q1) => $q1->whereIn('offer_id', $this->verticalOffers));
                });
            })
            ->groupBy(['manual_insights.date', 'buyer'])
            ->withCasts(['date' => null])
            ->get();
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return $this
     */
    public function since($since = null): Report
    {
        $this->since = Carbon::parse($since)->startOfDay();

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return $this
     */
    public function until($until = null): Report
    {
        $this->until = Carbon::parse($until)->endOfDay();

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param null|array $offers
     *
     * @return $this
     */
    public function forOffers($offers = null): Report
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Filter by users
     *
     * @param null|array $users
     *
     * @return $this
     */
    public function forUsers($users = null): Report
    {
        if ($users !== null) {
            $this->users = User::visible()->pluck('id')->intersect($users)->values()->push(0)->toArray();

            return $this;
        }

        $this->users = User::visible()->pluck('id')->push(0)->toArray();

        return $this;
    }

    /**
     * Filter by teams
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

    /**
     * Filter by bundles
     *
     * @param null|array $bundles
     *
     * @return $this
     */
    public function forBundles($bundles = null)
    {
        $this->bundles = $bundles;

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
     * @param bool $byAccount
     *
     * @return $this
     */
    public function groupByAccount(bool $byAccount)
    {
        $this->groupByAccount = $byAccount;

        return $this;
    }

    /**
     * @param string|null $sortBy
     * @param bool|null   $descending
     *
     * @return $this
     */
    public function sortBy(?string $sortBy, ?bool $descending = false): Report
    {
        if ($sortBy !== null && array_key_exists($sortBy, $this->headers())) {
            $this->sortBy = $sortBy;
        }

        $this->descending = $descending ?? false;

        return $this;
    }
}
