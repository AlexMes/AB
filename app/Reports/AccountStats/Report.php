<?php

namespace App\Reports\AccountStats;

use App\Facebook\Account;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
     * @var array|null
     */
    protected ?array $users;

    /**
     * @var array|null
     */
    protected ?array $statuses;

    /**
     * @var array|null
     */
    protected ?array $campaigns;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\AccountStats\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'users'        => $request->get('users'),
            'statuses'     => $request->get('statuses'),
            'campaigns'    => $request->get('campaigns'),
        ]);
    }

    /**
     * AccountStats constructor.
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
            ->forStatuses($settings['statuses'] ?? null)
            ->forCampaigns($settings['campaigns'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\AccountStats\Report
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
     * @return \App\Reports\AccountStats\Report
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
     * Filter report by users
     *
     * @param array|null $users
     *
     * @return \App\Reports\AccountStats\Report
     */
    protected function forUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Filter report by account statuses
     *
     * @param array|null $statuses
     *
     * @return \App\Reports\AccountStats\Report
     */
    protected function forStatuses($statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * Filter report by campaigns
     *
     * @param array|null $campaigns
     *
     * @return \App\Reports\AccountStats\Report
     */
    protected function forCampaigns($campaigns)
    {
        $this->campaigns = $campaigns;

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
     * Shape report data into array
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->fetchData();

        return [
            'headers'  => ['account', 'profile', 'status', 'utm campaign', 'card', 'link'],
            'rows'     => $this->rows($data),
            'summary'  => [],
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function fetchData()
    {
        return Account::query()
            ->when($this->users, function (Builder $query) {
                $query->whereHas('profile', fn ($q) => $q->whereIn('user_id', $this->users));
            })
            ->notEmptyWhereIn('account_status', $this->statuses)
            ->with([
                'profile',
                'campaigns' => function ($query) {
                    $query->when($this->campaigns, fn ($q) => $q->whereIn('utm_key', $this->campaigns));
                },
                'campaigns.ads' => function ($query) {
                    $query->whereHas(
                        'cachedInsights',
                        function ($query) {
                            $query->whereBetween('date', [$this->since, $this->until])
                                ->notEmptyWhereIn('user_id', $this->users);
                        }
                    );
                },
            ])
            ->orderBy('name')
            ->get()
            ->reject(fn ($account) => $account->campaigns->flatMap->ads->count() === 0)
            ->map(function ($account) {
                return [
                    'account_name'  => $account->name,
                    'profile_name'  => $account->profile->name,
                    'status'        => $account->status,
                    'campaigns'     => $account->campaigns->reject(fn ($campaign) => $campaign->ads->count() === 0),
                    'card_number'   => $account->card_number,
                ];
            });
    }

    /**
     * @param \Illuminate\Support\Collection $data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function rows($data)
    {
        return $data->map(function ($row) {
            return [
                'account'       => $row['account_name'],
                'profile'       => $row['profile_name'],
                'status'        => $row['status'],
                'utm_campaign'  => $row['campaigns']->unique('utm_key')->implode('utm_key', ', '),
                'card'          => $row['card_number'],
                'link'          => $row['campaigns']->flatMap->ads
                    ->unique('creative_url')
                    ->reject(fn ($ad) => empty($ad->creative_url))
                    ->implode('creative_url', ', '),
            ];
        });
    }
}
