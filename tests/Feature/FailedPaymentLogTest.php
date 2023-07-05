<?php

namespace Tests\Feature;

use App\Facebook\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FailedPaymentLogTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function itLogsFailureWhenPaymentFails()
    {
        $account = factory(Account::class)->create();

        $this->assertNotNull($account->account_status);

        $account->update([
            'account_status' => 3,
        ]);

        $this->assertDatabaseHas('facebook_payment_fails_log', ['account_id' => $account->account_id]);
    }

    /** @test */
    public function itIgnoresShitWhenAccountStatusNotUpdated()
    {
        $account = factory(Account::class)->create();

        $this->assertNotNull($account->account_status);

        $account->update([
            'name' => 'waffle',
        ]);

        $this->assertDatabaseMissing('facebook_payment_fails_log', ['account_id' => $account->account_id]);
    }

    /** @test */
    public function itLogsNothingWhenAccountStatusNotRelatedToPayment()
    {
        $account = factory(Account::class)->create();

        $this->assertNotNull($account->account_status);

        $account->update([
            'name' => 4,
        ]);

        $this->assertDatabaseMissing('facebook_payment_fails_log', ['account_id' => $account->account_id]);
    }
}
