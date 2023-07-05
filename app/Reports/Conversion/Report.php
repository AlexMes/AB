<?php

namespace App\Reports\Conversion;

use App\Deposit;
use App\LeadOrderAssignment;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
     * Users collection
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $users;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'    => $request->get('since'),
            'until'    => $request->get('until'),
            'users'    => $request->get('users'),
        ]);
    }

    /**
     * GenderReport constructor.
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->forUsers($settings['users'] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $rows = $this->aggregate();

        return [
            'headers' => ['date','leads','ftd', 'same day','next day','in 3 days','3+ days'],
            'rows'    => $this->rows($rows),
            'summary' => $this->summary($rows),
            'period'  => [
                'since' => $this->since->toDateString(),
                'until' => $this->until->toDateString(),
            ]
        ];
    }

    /**
     * Get rows
     *
     * @param $rows
     *
     * @return \App\Deposit[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function rows($rows)
    {
        return $rows->map(fn ($day) => [
            'date'  => $day->date,
            'leads' => $day->leads,
            'ftd'   => sprintf(
                "%s / %s %%",
                $day->deposit,
                $this->percentage($day->deposit, $day->leads)
            ),
            'same'  => sprintf(
                "%s / %s %%",
                $day->same,
                $this->percentage($day->same, $day->leads)
            ),
            'next'  => sprintf(
                "%s / %s %%",
                $day->next,
                $this->percentage($day->next, $day->leads)
            ),
            'third' => sprintf(
                "%s / %s %%",
                $day->third,
                $this->percentage($day->third, $day->leads)
            ),
            'other' => sprintf(
                "%s / %s %%",
                $day->other,
                $this->percentage($day->other, $day->leads),
            ),
        ]);
    }

    /**
     * Get summary
     *
     * @param $rows
     *
     * @return array
     */
    protected function summary($rows)
    {
        return [
            'date'  => 'TOTAL',
            'leads' => $rows->sum('leads'),
            'ftd'   => sprintf(
                "%s / %s %%",
                $rows->sum('deposit'),
                $this->percentage($rows->sum('deposit'), $rows->sum('leads'))
            ),
            'same'  => sprintf(
                "%s / %s %%",
                $rows->sum('same'),
                $this->percentage($rows->sum('same'), $rows->sum('leads'))
            ),
            'next'  => sprintf(
                "%s / %s %%",
                $rows->sum('next'),
                $this->percentage($rows->sum('next'), $rows->sum('leads'))
            ),
            'third' => sprintf(
                "%s / %s %%",
                $rows->sum('third'),
                $this->percentage($rows->sum('third'), $rows->sum('leads'))
            ),
            'other' => sprintf(
                "%s / %s %%",
                $rows->sum('other'),
                $this->percentage($rows->sum('other'), $rows->sum('leads')),
            ),
        ];
    }

    protected function percentage($one, $two)
    {
        if ($two) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
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
     * @return \Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return DB::query()->fromSub(
            Deposit::visible()->allowedOffers()->select([
                DB::raw('lead_return_date AS date'),
                DB::raw('EXTRACT(epoch FROM age(deposits.date, deposits.lead_return_date)) / 86400 AS diff')
            ])
                ->when($this->users, fn ($query) => $query->whereIn('user_id', $this->users))
                ->whereDoesntHave('lead', fn ($q) => $q->whereNotNull('affiliate_id')),
            'diff'
        )
            ->selectRaw('
                    res.date,
                    res.leads,
                    count(diff) AS deposit,
                    count(CASE WHEN diff < 1 THEN 1 END) AS same,
                    count(CASE WHEN diff = 1 THEN 1	END) AS NEXT,
                    count(CASE WHEN diff = 2 THEN 1 END) AS third,
                    count(CASE WHEN diff > 2 THEN 1 END) AS other
                ')
            ->rightJoinSub(
                LeadOrderAssignment::visible()->allowedOffers()->selectRaw('created_at::date as date, count(*) as leads')
                    ->whereHas('lead', fn ($query) => $query->whereNull('affiliate_id'))
                    ->when(
                        $this->since && $this->until,
                        fn ($query) => $query->whereBetween(DB::raw('created_at::date'), [$this->since, $this->until])
                    )
                    ->when(
                        $this->users,
                        fn ($query) => $query->whereHas('lead', fn ($q) => $q->whereIn('user_id', $this->users))
                    )
                    ->groupBy('date'),
                'res',
                'diff.date',
                '=',
                'res.date'
            )
            ->groupBy('res.date', 'res.leads')
            ->orderByDesc('res.date')
            ->get();
    }

    /**
     * Filter report by users
     *
     * @param array|null $users
     *
     * @return \App\Reports\Conversion\Report
     */
    public function forUsers($users)
    {
        if ($users !== null) {
            $this->users = User::visible()->whereIn('id', Arr::wrap($users))->pluck('id')->toArray();

            return $this;
        }
        $this->users = null;

        return $this;
    }
}
