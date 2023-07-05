<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\Notifications\SubscriptionPaymentsDue;
use App\Subscription;
use Illuminate\Console\Command;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class CheckUpcomingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for scheduled subscriptions, and notify finance team';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Subscription::enabled()
            ->get()
            ->filter(fn (Subscription $subscription) => $subscription->dueInNearFuture())
            ->whenNotEmpty(
                function ($subscriptions) {
                    $notification = new SubscriptionPaymentsDue($subscriptions);
                    Notification::send(
                        $this->getNotifiable(),
                        $notification
                    );
                }
            );
    }

    /**
     * This bitch gotta work
     *
     * @return \Illuminate\Notifications\AnonymousNotifiable
     */
    protected function getNotifiable()
    {
        $notifiable              = new AnonymousNotifiable();
        $notifiable->telegram_id = AdsBoard::devsChannel();

        return $notifiable;
    }
}
