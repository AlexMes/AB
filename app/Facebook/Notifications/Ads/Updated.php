<?php

namespace App\Facebook\Notifications\Ads;

use App\AdsBoard;
use App\Bot\TelegramChannel;
use App\Notifications\NotificationSeverity;
use App\NotificationType;
use App\User;
use FacebookAds\Object\Values\AdEffectiveStatusValues;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class Updated extends Notification
{
    use Queueable;

    /**
     * Campaign instance
     *
     * @var \App\Facebook\Ad
     */
    protected $ad;

    protected $old;

    protected $new;

    /**
     * Create a new notification instance.
     *
     * @param \App\Facebook\Events\Ads\Updated $event
     *
     * @return void
     */
    public function handle(\App\Facebook\Events\Ads\Updated $event)
    {
        $this->ad         = $event->ad;
        $this->queue      = AdsBoard::QUEUE_NOTIFICATIONS;
        $this->old        = $event->ad->getRawOriginal()['effective_status'];
        $this->new        = $event->ad->getDirty()['effective_status'] ?? null;

        if ($this->shouldSend()) {
            app(Dispatcher::class)->send($event->ad->account->profile->user, $this);
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable instanceof User) {
            $result = ['database', 'broadcast'];
            if ($notifiable->telegram_id) {
                $result[] = TelegramChannel::class;
            }

            return $result;
        }

        return null;
    }

    /**
     * Get message contents
     *
     * @return string
     */
    public function toTelegram()
    {
        return "Статус обьявления {$this->ad->name} ({$this->ad->id}) обновлен." . PHP_EOL
                . sprintf('%s -> %s', $this->old, $this->new) . PHP_EOL
                . sprintf('*URL:* %s', $this->ad->creative_url) . PHP_EOL
                . sprintf('*Adset ID:* %s', $this->ad->adset_id) . PHP_EOL
                . sprintf('*Account:* %s (%s)', $this->ad->account->name, $this->ad->account_id) . PHP_EOL
                . sprintf('*Account status:* %s', $this->ad->account->status) . PHP_EOL
                . sprintf('*Profile:* %s', $this->ad->profile->name) . PHP_EOL
                . $this->getProblem();
    }

    /**
     * Array representation for the notification
     *
     * @param $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'     => "Статус обьявления {$this->ad->name} ({$this->ad->id}) обновлен.",
            'body'      => "Статус обьявления {$this->ad->name} ({$this->ad->id}) обновлен." . PHP_EOL
                . sprintf('%s -> %s', $this->old, $this->new) . PHP_EOL
                . sprintf('*URL:* %s', $this->ad->creative_url) . PHP_EOL
                . sprintf('*Adset ID:* %s', $this->ad->adset_id) . PHP_EOL
                . sprintf('*Account:* %s (%s)', $this->ad->account->name, $this->ad->account_id) . PHP_EOL
                . sprintf('*Account status:* %s', $this->ad->account->status) . PHP_EOL
                . sprintf('*Profile:* %s', $this->ad->profile->name) . PHP_EOL
                . $this->getProblem(),
            'severity'  => NotificationSeverity::INFO,
        ];
    }

    /**
     * Notification representation for broadcasting
     *
     * @param $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Determine when notification should really be sent
     *
     * @return bool
     */
    protected function shouldSend()
    {
        if (
            $this->old === AdEffectiveStatusValues::PENDING_REVIEW
            && ($this->new === AdEffectiveStatusValues::ACTIVE || $this->new === AdEffectiveStatusValues::DISAPPROVED)
            && $this->ad->profile->user !== null
            && $this->ad->account->profile->user->hasTelegramNotification(NotificationType::AD_STATUS_UPDATED)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine when notification with feedback should be sent
     *
     * @return bool
     */
    protected function hasFeedback()
    {
        return $this->ad->ad_review_feedback !== null;
    }

    /**
     * Get problem text
     *
     * @return string
     */
    protected function getProblem()
    {
        if ($this->hasFeedback()) {
            return sprintf('*Issue:* %s', array_key_first($this->ad->ad_review_feedback['global']));
        }

        return '';
    }
}
