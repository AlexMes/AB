<?php

namespace App\Reports\TrafficLoss;

use App\Insights;
use App\LeadOrderAssignment;
use App\Metrics\Binom\Clicks as BinomClicks;
use App\Metrics\Binom\LandingClicks;
use App\Metrics\Binom\Leads;
use App\Metrics\Facebook\Clicks;
use App\Metrics\Facebook\LeadsCount;
use App\Offer;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Report implements \Illuminate\Contracts\Support\Responsable, \Illuminate\Contracts\Support\Arrayable
{
    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * Report constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forDate($settings['date'] ?? now());
    }

    /**
     * Named constructor
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Reports\TrafficLoss\Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'date' => Carbon::parse($request->get('date')),
        ]);
    }

    /**
     * Set effective report date
     *
     * @param string|null|DateTimeInterface $date
     *
     * @return \App\Reports\TrafficLoss\Report
     */
    public function forDate($date)
    {
        $this->date = $date ?? now();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'headers' => $this->headers(),
            'rows'    => $this->rows(),
        ];
    }

    /**
     * Get headers for the report
     *
     * @return array
     */
    public function headers()
    {
        return [
            'Offer',
            'FB clicks','PL Clicks','FP->PL loss','LP Clicks','PL->LP loss','FB Leads','Binom Leads','Leads'
        ];
    }

    /**
     * Rows for the report
     *
     * @return array
     */
    public function rows()
    {
        return Offer::allowed()->get()->map(function (Offer $offer) {
            $insights = $this->getInsights($offer);
            $fbClicks = Clicks::make($insights)->value();
            $fbLeads = LeadsCount::make($insights)->value();
            $binomLeads = Leads::make()->forOffers($offer)->forDate($this->date)->value();
            $binomClicks = BinomClicks::make()->forOffers($offer)->forDate($this->date)->value();
            $binomLPClicks = LandingClicks::make()->forOffers($offer)->forDate($this->date)->value();
            $leads = LeadOrderAssignment::allowedOffers()
                ->whereDate('registered_at', $this->date->toDateString())
                ->whereHas('route', fn (Builder $query) => $query->where('offer_id', $offer->id))
                ->count();

            return [
                'name'             => $offer->name,
                'clicks'           => $fbClicks,
                'pl_clicks'        => $binomClicks,
                'fb_to_pl_loss'    => sprintf('%s %%', round($this->loss($fbClicks, $binomClicks), 2)),
                'lp_clicks'        => $binomLPClicks,
                'pl_lp_loss'       => sprintf('%s %%', round($this->loss($binomClicks, $binomLPClicks), 2)),
                'fb_leads'         => $fbLeads,
                'binom_leads'      => $binomLeads,
                'board_leads'      => $leads,
            ];
        })->reject(function ($row) {
            return $row['clicks'] === 0 && $row['pl_clicks'] === 0 && $row['lp_clicks'] === 0;
        })->values();
    }

    /**
     * Calculate traffic loss
     *
     * @param int $old
     * @param int $new
     *
     * @return float|int|string
     */
    protected function loss($old, $new)
    {
        if ($old) {
            return (($new - $old) / $old) * 100;
        }

        return 0;
    }

    /**
     * Get insights for offer
     *
     * @param \App\Offer $offer
     *
     * @return \App\Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getInsights(Offer $offer)
    {
        return Insights::allowedOffers()
            ->whereDate('date', $this->date->toDateString())
            ->where('offer_id', $offer->id)
            ->get(['link_clicks', 'leads_cnt']);
    }

    /**
     * @inheritDoc
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }
}
