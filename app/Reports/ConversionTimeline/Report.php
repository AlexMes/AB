<?php

namespace App\Reports\ConversionTimeline;

use App\Deposit;
use App\Offer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
    protected $verticals;

    /**
     * @var array|null
     */
    protected $officeGroups;

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
            ->forVerticals($settings['verticals'] ?? null)
            ->forOfficeGroups($settings['office_groups'] ?? null);
    }

    /**
     * Build report using request variables
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\ConversionTimeline\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'         => $request->get('since'),
            'until'         => $request->get('until'),
            'offices'       => $request->get('offices'),
            'verticals'     => $request->get('verticals'),
            'office_groups' => $request->get('office_groups'),
        ]);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\ConversionTimeline\Report
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
     * @return \App\Reports\ConversionTimeline\Report
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
     * @param array|null $verticals
     *
     * @return Report
     */
    public function forVerticals($verticals = null)
    {
        $this->verticals = $verticals;

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
            'headers'  => ['Office', 'Registration month', 'Deposit month', 'Count'],
            'rows'     => $this->rows($data),
            'summary'  => $this->summary($data),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    protected function rows($data)
    {
        return $data->map(fn ($row) => [
            'office'   => $row->name,
            'regmonth' => $row->regmonth,
            'depmonth' => $row->depmonth,
            'count'    => $row->count
        ]);
    }
    /**
     * @return mixed
     */
    protected function aggregate()
    {
        return Deposit::visible()
            ->select([
                'offices.name',
                DB::raw("to_char(deposits.lead_return_date, 'Month') AS regmonth"),
                DB::raw("to_char(deposits.date, 'Month') AS depmonth"),
                DB::raw("count(deposits.id) as count"),
            ])
            ->join('offices', 'deposits.office_id', '=', 'offices.id')
            ->when($this->since && $this->until, function (Builder $query) {
                $query->whereBetween('deposits.date', [$this->since, $this->until]);
            })
            ->when($this->verticals, function (Builder $query) {
                $query->whereIn('deposits.offer_id', Offer::whereIn('vertical', $this->verticals)
                    ->pluck('id'));
            })
            ->notEmptyWhereIn('deposits.office_id', $this->offices)
            ->when($this->officeGroups, function (Builder $query) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($this->officeGroups));
                });
            })
            ->groupByRaw('offices.name, regmonth, depmonth')
            ->get();
    }

    /**
     * @param Collection $data
     *
     * @return array
     */
    protected function summary(Collection $data)
    {
        return [
            'office'           => 'Total',
            'regmonth'         => '',
            'depmonth'         => '',
            'count'            => $data->sum('count'),
        ];
    }
}
