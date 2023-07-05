<?php

namespace Tests\Unit;

use App\AdsBoard;
use App\Jobs\SMS\UpdateMessageStatus;
use App\SmsMessage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SmsMessageTest extends TestCase
{
    /** @test */
    public function itDeterminesVendorId()
    {
        $message = new SmsMessage([
            'raw_response' => ["success_request" => [
                "info" => [
                    693105488 => "79069802275",
                ],
            ],],
        ]);

        $this->assertEquals(693105488, $message->getVendorId());
    }

    /** @test */
    public function itReturnsNullVendorIdWhenResponseEmpty()
    {
        $message = new SmsMessage(['raw_response' => ['asfsadf']]);

        $this->assertNull($message->getVendorId());
    }

    /** @test */
    public function itReturnsFalseWhenVendorIdMissing()
    {
        $message = new SmsMessage(['raw_response' => ['asfsadf']]);
        $this->assertFalse($message->hasVendorId());
    }

    /** @test */
    public function itReturnsTrueWhenVendorIdPresent()
    {
        $message = new SmsMessage([
            'raw_response' => ["success_request" => [
                "info" => [
                    693105488 => "79069802275",
                ],
            ],],
        ]);

        $this->assertTrue($message->hasVendorId());
    }

    /** @test */
    public function itDispatchesUpdateStatusJob()
    {
        \Queue::fake();

        $message = new SmsMessage(['id' => 1]);

        $message->updateStatus();

        Queue::assertPushedOn(AdsBoard::QUEUE_SMS, UpdateMessageStatus::class);
    }
}
