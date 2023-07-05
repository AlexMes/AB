<?php

namespace App\Reports\BuyersMonthStats;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Report implements Responsable, Arrayable
{
    /**
     * Select report for a specific user
     *
     * @var \App\User|null
     */
    protected $user;

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
     * Construct report
     *
     * @param array $settings
     *
     * @throws \Exception
     */
    public function __construct(array $settings = [])
    {
        $this->forUser($settings['user'] ?? null);
        $this->since = Carbon::now()->startOfMonth();
        $this->until = Carbon::now()->endOfMonth();
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'user'      => $request->get('user'),
        ]);
    }

    /**
     * Filter stats by specific user
     *
     * @param mixed $user
     *
     * @return \App\Reports\UrgentStats\Report
     */
    public function forUser($user)
    {
        $this->user = $user;

        return  $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'headers'  => Headers::ALL,
            'rows'     => $this->rows(),
            'summary'  => $this->summary(),
        ];
    }

    /**
     * Get report rows
     *
     * @return array
     */
    public function headers()
    {
        return Headers::ALL;
    }

    /**
     * Get report rows
     *
     * @return array
     */
    public function rows()
    {
        return User::visible()
            ->notEmptyWhereIn('id', Arr::wrap($this->user))
            ->get()
            ->map(function (User $user) {
                return (new Row($user, $this->since, $this->until))->toArray();
            })->values();
    }

    /**
     * Get report summary
     *
     * @return array
     */
    public function summary(): array
    {
        $users = User::visible()
            ->notEmptyWhereIn('id', Arr::wrap($this->user))
            ->get();

        return Summary::build($users, $this->since, $this->until)->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
