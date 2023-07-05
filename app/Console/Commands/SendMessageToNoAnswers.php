<?php

namespace App\Console\Commands;

use App\Jobs\SMS\Message;
use App\Lead;
use App\LeadOrderAssignment;
use App\SmsCampaign;
use Illuminate\Console\Command;

class SendMessageToNoAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-notification-to-na';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to NA leads from previous day.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leads = Lead::whereIn('id', LeadOrderAssignment::whereDate('created_at', now()->subDay())->pluck('lead_id'))
            ->where(fn ($query) => $query->whereIn('status', ['Нет ответа', 'нбт']));

        $campaign = SmsCampaign::find(config('sms.na-campaign'));

        if ($campaign == null) {
            return 'failure';
        }

        foreach ($leads->cursor() as $lead) {
            Message::dispatch($lead, $campaign);
        }
    }
}
