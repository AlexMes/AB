<?php

namespace App\Reports\BuyersAccountsStats;

use App\Insights;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Report implements Responsable, Arrayable
{
    /**
     * Select report for a specific user
     *
     * @var \App\User|null
     */
    protected $user;
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $since;
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $until;
    /**
     * Buyers with accounts for report
     *
     * @var mixed
     */
    protected $buyers;

    /**
     * Facebook insights for specific accounts
     *
     * @var mixed
     */
    protected $insights;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forUser($settings['user'] ?? null)
            ->forPeriod($settings['since'] ?? null, $settings['until'] ?? null)
            ->fetchBuyers()
            ->fetchInsights();
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'user'      => $request->get('user'),
            'since'     => $request->get('start'),
            'until'     => $request->get('end'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $user
     *
     * @return \App\Reports\BuyersAccountsStats\Report
     */
    public function forUser($user)
    {
        $this->user = $user;

        return  $this;
    }

    /**
     * Filter stats by specific user
     *
     * @param null $since
     * @param null $until
     *
     * @return \App\Reports\BuyersAccountsStats\Report
     */
    public function forPeriod($since = null, $until = null)
    {
        if (is_null($since)) {
            return $this;
        }

        try {
            $this->since = Carbon::parse($since)->startOfDay();
            $this->until = Carbon::parse($until)->endOfDay();

            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $rows = Rows::make($this->buyers, $this->insights);

        return [
            'headers' => Headers::ALL,
            'rows'    => $rows->metric(),
            'summary' => Summary::make($rows->values()),
        ];
    }

    /**
     * Load facebook insights for certain users
     *
     * @return $this
     */
    protected function fetchInsights()
    {
        $accounts = $this->buyers->flatMap->accounts;

        $this->insights = Insights::allowedOffers()
            ->when($this->since, function ($query) {
                $query->whereBetween('date', [
                    $this->since->toDateString(),
                    $this->until->toDateString(),
                ]);
            })
            ->whereIn('account_id', $accounts->pluck('account_id')->unique()->toArray())
            ->whereIn('user_id', $this->buyers->pluck('id'))
            ->get(['account_id','spend']);

        return $this;
    }

    /**
     * Load users
     *
     * @return $this
     */
    protected function fetchBuyers()
    {
        $this->buyers = User::visible()
            ->with(['accounts' => function ($query) {
                return $query->when($this->since, function ($query) {
                    $query->whereBetween('facebook_ads_accounts.created_at', [
                        $this->since->toDateTimeString(),
                        $this->until->toDateTimeString(),
                    ]);
                });
            }])
            ->notEmptyWhere('id', $this->user)
            ->get(['id','name']);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
