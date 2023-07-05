<?php

namespace App\Console\Commands;

use App\SmsMessage;
use Illuminate\Console\Command;

class CheckMessagesDeliveryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:check-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check delivery status for sent sms messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->messagesSentToday() as $message) {
            /** @var SmsMessage $message */
            $message->updateStatus();
        }
    }

    /**
     * Fetch SMS that was sent today
     *
     * @return \Illuminate\Support\LazyCollection
     */
    protected function messagesSentToday()
    {
        return SmsMessage::where('status', SmsMessage::STATUS_SENT)
            ->whereDate('created_at', now()->toDateString())
            ->cursor();
    }
}
