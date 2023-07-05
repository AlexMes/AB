<?php

namespace App\Reports\MonthlyDtdNOA;

use App\AssignmentDayToDaySnapshot;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Collection;

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
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now());
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
        $this->since = (is_null($since) ? now() : Carbon::parse($since) ?? now())->startOfDay();

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
        $this->until = (is_null($until) ? now() : Carbon::parse($until) ?? now())->endOfDay();

        return $this;
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
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $rows = $this->aggregate();

        return [
            'headers' => Fields::ALL,
            'rows'    => $this->rows($rows),
            'summary' => $this->summary($rows)
        ];
    }

    /**
     * Get report rows
     *
     * @param mixed $rows
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function rows($rows)
    {
        return $rows->map(function ($row) {
            return [
                Fields::NAME    => $row->name,
                Fields::LEADS   => $row->leads,
                Fields::NOA     => $row->noa,
            ];
        })->values();
    }

    /**
     * Get report summary
     *
     * @param mixed $rows
     *
     * @return array
     */
    public function summary($rows): array
    {
        return [
            Fields::NAME        => 'TOTAL',
            Fields::LEADS       => $rows->sum('leads'),
            Fields::NOA         => $rows->sum('noa'),
        ];
    }

    /**
     * Load rows
     *
     * @return AssignmentDayToDaySnapshot[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Collection|void
     */
    protected function aggregate()
    {
        return AssignmentDayToDaySnapshot::query()
            ->select([
                DB::raw('offices.name AS name'),
                DB::raw('sum(assignment_day_to_day_snapshots.total) AS leads'),
                DB::raw('sum(assignment_day_to_day_snapshots.no_answer) AS noa'),
            ])
            ->leftJoin('managers', 'assignment_day_to_day_snapshots.manager_id', 'managers.id')
            ->leftJoin('offices', 'managers.office_id', 'offices.id')
            ->whereBetween('assignment_day_to_day_snapshots.created_at', [$this->since, $this->until])
            ->where(function ($query) {
                $query->where('offices.frx_tenant_id', 1)->orWhereIn('offices.id', [49, 50, 51, 52]);
            })
            ->groupBy(['offices.name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
