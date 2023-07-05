<?php

namespace Tests\Unit;

use App\Subscription;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    /**
     * Configure test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2020-01-31 00:00:00');
    }

    /** @test */
    public function itRespondsTrueWhenEnabledIsTrue()
    {
        $subscription = new Subscription(['enabled' => true]);

        $this->assertTrue($subscription->isEnabled());
    }

    /** @test */
    public function itRespondsFalseWhenEnabledIsFalse()
    {
        $subscription = new Subscription(['enabled' => false]);

        $this->assertFalse($subscription->isEnabled());
    }

    /** @test */
    public function itHasNextRunDate()
    {
        // Monthly subscription, every 1-st day of the month
        $subscription = new Subscription(['frequency' => '59 23 1 * *']);

        $this->assertNotNull($subscription->nextRunAt());
    }

    /** @test */
    public function nextRunDateIsCarbonInstanceForConvenience()
    {
        // Monthly subscription, every 1-st day of the month
        $subscription = new Subscription(['frequency' => '59 23 1 * *']);

        $this->assertInstanceOf(Carbon::class, $subscription->nextRunAt());
    }

    /** @test */
    public function itAwareAboutSubscriptionsDueInCurrentWeek()
    {
        $subscription = new Subscription(['frequency' => '* * 1 * *']);

        $this->assertTrue($subscription->dueThisWeek(), 'It is not aware about subscriptions due in current week');
    }

    /** @test */
    public function itAwareAboutSubscriptionsDueTomorrow()
    {
        $subscription = new Subscription(['frequency' => '* * 1 * *']);

        $this->assertTrue($subscription->dueTomorrow(), 'It is not aware about subscriptions due tomorrow');
    }

    /** @test */
    public function itAwareAboutSubscriptionsDueToday()
    {
        $subscription = new Subscription(['frequency' => '* * 31 * *']);

        $this->assertTrue($subscription->dueToday(), 'It is not aware about subscriptions due today');
    }

    /**
     * Cleanup environment
     *
     * @return void
     */
    public function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
