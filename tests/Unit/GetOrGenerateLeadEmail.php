<?php

namespace Tests\Unit;

use App\Lead;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class GetOrGenerateLeadEmail extends TestCase
{
    /** @test */
    public function itRespondsWithLeadEmailWhenItWasSet()
    {
        $lead = new Lead([
            'email' => 'yolo.com',
        ]);

        $this->assertSame('yolo.com', $lead->getOrGenerateEmail());
    }

    /** @test */
    public function itRespondsWithFakeEmailWhenAttributeIsNull()
    {
        $lead = new Lead([
            'email'      => null,
            'firstname'  => 'Yooolo',
        ]);

        $this->assertNotNull($lead->getOrGenerateEmail(), 'Generated email is null');
        $this->assertTrue(Str::startsWith($lead->getOrGenerateEmail(), 'yooolo'), 'First name was not used for email');
    }
}
