<?php

namespace App\Reports\AccountsDaily;

use App\Facebook\Account;
use App\Insights;
use App\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Class Report
 *
 * @package App\Reports\AccountsDaily
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
     * Accounts used to load report data
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $accounts;

    /**
     * Users collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $users;

    /**
     * Collection of results row
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\AccountsDaily\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'     => $request->get('since'),
            'until'     => $request->get('until'),
            'users'     => $request->get('users'),
            'accounts'  => $request->get('accounts'),
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
            ->forUsers($settings['users'] ?? null)
            ->forAccounts($settings['accounts'] ?? null);

        $this->rows = collect();
    }

    /**
     * Collect and map report data
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function collectData()
    {
        Insights::visible()
            ->allowedOffers()
            ->whereBetween('date', [$this->since->toDateTimeString(), $this->until->toDateTimeString()])
            ->whereIn('account_id', $this->accounts->pluck('account_id')->values())
            ->when($this->users !== null, fn ($q) => $q->whereIn('user_id', $this->users->pluck('id')))
            ->orderBy('date')
            ->get()
            ->map(function (Insights $insights) {
                return ['date' => $insights->date ,'account' => $insights->account];
            })
            ->unique()
            ->each(function ($insight) {
                $this->rows->push(
                    (new Row($insight['date'], $insight['account']))->toArray()
                );
            });


        return $this->rows;
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\AccountsDaily\Report
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
     * @return \App\Reports\AccountsDaily\Report
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
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'headers' => Headers::ALL,
            'rows'    => $this->collectData(),
            'summary' => Summary::build($this->since, $this->until, $this->accounts, $this->users)->toArray(),
            'period'  => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
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
     * Filter report for specific accounts
     *
     * @param string|array $accounts
     *
     * @return \App\Reports\AccountsDaily\Report
     */
    public function forAccounts($accounts = null)
    {
        if ($accounts === null) {
            $this->accounts  = $this->users === null
                ? Account::visible()->get()
                : Account::forUsers($this->users->pluck('id')->values());

            return $this;
        }

        $this->accounts = Account::visible()->whereIn('id', Arr::wrap($accounts))->get();

        return $this;
    }

    /**
     * Filter report by users
     *
     * @param array|string $users
     *
     * @return \App\Reports\AccountsDaily\Report
     */
    public function forUsers($users)
    {
        if ($users !== null) {
            $this->users  = User::whereIn('id', Arr::wrap($users))->get();

            return $this;
        }
        $this->users = null;

        return $this;
    }
}
