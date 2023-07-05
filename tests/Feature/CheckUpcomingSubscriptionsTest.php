<?php

namespace Tests\Feature;

use App\Console\Commands\CheckUpcomingSubscriptions;
use App\Notifications\SubscriptionPaymentsDue;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckUpcomingSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configure test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2020-01-06 00:00:00');
    }

    /** @test */
    public function itDoesntSendNotificationWhenNoSubscriptionsEnabled()
    {
        Notification::fake();

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '@monthly',
            'enabled'   => false,
        ]);

        $this->artisan(CheckUpcomingSubscriptions::class);

        $this->assertEmpty(Subscription::enabled()->get());

        Notification::assertNothingSent();
    }

    /** @test */
    public function itDoesntSendNotificationWhenNoSubscriptionsExists()
    {
        Notification::fake();

        $this->artisan(CheckUpcomingSubscriptions::class);

        $this->assertEmpty(Subscription::enabled()->get());

        Notification::assertNothingSent();
    }

    /** @test */
    public function itDoesntSendNotificationWhenNoSubscriptionsDueInNearFuture()
    {
        Notification::fake();

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '* * 16 * *',
            'enabled'   => true,
        ]);

        $this->assertNotEmpty(Subscription::enabled()->get());

        $this->artisan(CheckUpcomingSubscriptions::class);


        Notification::assertNothingSent();
    }

    /** @test */
    public function itDoesSendNotificationWhenTodayIsMondayAndSubscriptionsDueInCurrentWeek()
    {
        Notification::fake();

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '* * 7 * *',
            'enabled'   => true,
        ]);

        $this->assertTrue(now()->isMonday());
        $this->assertNotEmpty(Subscription::enabled()->get());

        $this->artisan(CheckUpcomingSubscriptions::class);

        Notification::assertSentTo(new AnonymousNotifiable(), SubscriptionPaymentsDue::class);
    }

    /** @test */
    public function itDoesNotSendNotificationWhenTodayIsNotMondayAndSubscriptionsDueInCurrentWeek()
    {
        Notification::fake();

        Carbon::setTestNow('2020-01-07 00:00:00');

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '* * 9 * *',
            'enabled'   => true,
        ]);

        $this->assertFalse(now()->isMonday(), 'Today set to Monday, change please');
        $this->assertNotEmpty(Subscription::enabled()->get(), 'No enabled subscriptions found');

        $this->artisan(CheckUpcomingSubscriptions::class);

        Notification::assertNothingSent();
    }

    /** @test */
    public function itDoesSendNotificationWhenSubscriptionsDueToday()
    {
        Notification::fake();

        Carbon::setTestNow('2020-01-07 00:00:00');

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '* * 7 * *',
            'enabled'   => true,
        ]);

        $this->assertNotEmpty(Subscription::enabled()->get());

        $this->artisan(CheckUpcomingSubscriptions::class);

        Notification::assertSentTo(new AnonymousNotifiable(), SubscriptionPaymentsDue::class);
    }

    /** @test */
    public function itDoesSendNotificationWhenSubscriptionsDueTomorrow()
    {
        Notification::fake();

        Carbon::setTestNow('2020-01-07 00:00:00');

        Subscription::create([
            'service'   => 'test',
            'amount'    => '100 USD',
            'account'   => 'test@yo.co',
            'frequency' => '* * 8 * *',
            'enabled'   => true,
        ]);

        $this->assertNotEmpty(Subscription::enabled()->get());

        $this->artisan(CheckUpcomingSubscriptions::class);

        Notification::assertSentTo(new AnonymousNotifiable(), SubscriptionPaymentsDue::class);
    }

    /**
     * Cleanup environment
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
