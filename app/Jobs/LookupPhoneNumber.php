<?php

namespace App\Jobs;

use App\AdsBoard;
use App\Lead;
use App\PhoneLookup;
use App\Services\MessageBird\MessageBird;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class LookupPhoneNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lead;

    /**
     * Create a new job instance.
     *
     * @param mixed $phone
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MessageBird $messageBird)
    {
        if (PhoneLookup::where('phone', $this->lead->phone)->doesntExist()) {
            try {
                $raw = $messageBird->lookup($this->lead->phone);

                PhoneLookup::create([
                    'phone'   => $this->lead->phone,
                    'country' => $raw['countryCode'],
                    'prefix'  => $raw['countryPrefix'],
                    'type'    => $raw['type']
                ]);
            } catch (\Throwable $th) {
                if (cache()->lock('messagebird-exception', 2 * Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR)->get()) {
                    AdsBoard::report('Messagebird failure '.$th->getMessage());
                }
            }
        }
    }
}
