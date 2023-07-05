<?php

namespace App\Console\Commands;

use App\Lead;
use App\Offer;
use App\ResellBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ColdBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:cold
                            {offer}
                            {--since=2022-02-01 : created since}
                            {--until=2022-02-28 : created until}
                            {--amount=1000 : Leads to get}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload 1k cold leads into separate offer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $since = Carbon::parse($this->option('since'))->startOfDay();
        $until = Carbon::parse($this->option('until'))->endOfDay();

        $offer = Offer::find($this->argument('offer'));

        $newoffer = $offer->getColdBaseCopy();

        $this->info('New offer is '.$newoffer->name);

        $leads = Lead::whereOfferId($offer->id)
            ->whereBetween('created_at', [$since->toDateTimeString(), $until->toDateTimeString()])
            ->whereDoesntHave('deposits')
            ->whereDoesntHave('assignments', fn ($assignmentQuery) => $assignmentQuery->whereIn('lead_order_assignments.status', ['Депозит','Неверный номер','В работе у клоузера','Демонстрация']))
            ->whereNotIn('leads.id', DB::table('lead_resell_batch')->select('lead_id')->get()->pluck('lead_id')->toArray())
            ->limit($this->option('amount'))
            ->get();

        $this->info('Got '.$leads->count().' leads to go.');

        $batch = ResellBatch::create([
            'name'          => 'Cold 1k '.$offer->name,
            'registered_at' => now()->toDateString(),
            'branch_id'     => $offer->branch_id,
        ]);

        $this->info('batch created');

        $batch->leads()->attach($leads);

        $this->info('Leads attached to batch');

        $leads->each(function (Lead $lead) use ($newoffer) {
            $response = Http::post('https://uleads.app/leads/register', [
                'firstname' => $lead->firstname,
                'lastname'  => $lead->lastname,
                'phone'     => $lead->phone,
                'email'     => $lead->getOrGenerateEmail(),
                'offer'     => $newoffer->uuid,
                'poll'      => $lead->poll,
                'ip'        => $lead->ip,
            ]);

            $this->info($response->body());
        });


        $this->info('Leads loaded');

        return 0;
    }
}
