<?php

namespace App\Console\Commands\Fixtures;

use App\Jobs\Leads\DetectGender;
use App\Jobs\Leads\FetchIpAddressData;
use App\Lead;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RetrieveGendersForSt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:retrieve-leads-genders-for-st';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // pull fresh ip address data, where missing
        $this->ipAddresses();

        $leads = Lead::whereHas('ipAddress', function (Builder $query) {
            return $query->whereIn('country_code', ['ZA','CL']);
        })->where(function (Builder $query) {
            return $query->whereNull('gender_id')->orWhere('gender_id', 0);
        })->get();

        $this->info(sprintf("Got total of %s leads", $leads->count()));

        // detect gender
        $leads->each(function (Lead $lead) {
            DetectGender::dispatchNow($lead);
            $this->info(sprintf(
                "Gender was %s detected that gender is:%s",
                $lead->gender_id,
                $lead->refresh()->gender_id
            ));
        });

        return 0;
    }

    /**
     * Pull ip addresses, where missing
     *
     * @return void
     */
    public function ipAddresses()
    {
        $leads = Lead::whereDoesntHave('ipAddress')->whereIn('offer_id', $this->offers())
            ->whereNotNull('ip')
            ->get();

        $this->info(sprintf("%s leads without detected IP addresses", $leads->count()));

        foreach ($leads as $lead) {
            FetchIpAddressData::dispatchNow($lead);
        }
    }

    /**
     * Get offers for leads
     *
     * @return \Illuminate\Support\Collection
     */
    protected function offers(): \Illuminate\Support\Collection
    {
        return Offer::where('name', 'like', '%ZA%')
            ->orWhere('name', 'like', '%CL%')
            ->pluck('id');
    }
}
