<?php

namespace App\Reports\WeekDaysConversion;

use App\Result;
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
 * @package App\Reports\WeekDaysConversion
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
     * @var array|null
     */
    protected $offices;

    /**
     * @var array|null
     */
    protected $officeGroups;

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\WeekDaysConversion\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'offices'      => $request->get('offices'),
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
            ->forOffices($settings['offices'] ?? null)
            ->forOfficeGroups($settings['officeGroups'] ?? null);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\WeekDaysConversion\Report
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
     * @return \App\Reports\WeekDaysConversion\Report
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
     * @param array|null $offices
     *
     * @return Report
     */
    public function forOffices($offices = null)
    {
        $this->offices = $offices;

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
            'headers'  => ['day', 'leads', 'ftd', 'ftd%'],
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * @return Result[]|array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function aggregate()
    {
        return Result::allowedOffers()
            ->select([
                DB::raw('EXTRACT(DOW FROM created_at) as day'),
                DB::raw('sum(leads_count) AS leads'),
                DB::raw('sum(ftd_count) AS deposits'),
            ])
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween('date', [$this->since, $this->until]);
            })
            ->notEmptyWhereIn('office_id', $this->offices)
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('results.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->groupBy('day')
            ->orderBy('day')
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
                'day'           => Carbon::getDays()[$row->day],
                'leads'         => $row->leads ?? 0,
                'deposits'      => $row->deposits ?? 0,
                'ftd_percent'   => sprintf('%s %%', percentage($row->deposits, $row->leads)),
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
            'office'        => 'Итого',
            'leads'         => $data->sum('leads'),
            'deposits'      => $data->sum('deposits'),
            'ftd_percent'   => sprintf('%s %%', percentage($data->sum('deposits'), $data->sum('leads'))),
        ];
    }
}
