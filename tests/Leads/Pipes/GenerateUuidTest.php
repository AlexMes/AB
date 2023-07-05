<?php

namespace Leads\Pipes;

use App\Lead;
use Illuminate\Support\Str;
use Tests\TestCase;

class GenerateUuidTest extends TestCase
{
    /** @test */
    public function itGeneratesUuidForLead()
    {
        $lead = new Lead();

        $this->assertNull($lead->uuid, 'Sanity check failed.');

        $pipe = new \App\Leads\Pipes\GenerateUuid();

        $pipe->handle($lead, function (Lead $lead) {
            $this->assertNotNull($lead->uuid, 'UUID not generated');
            $this->assertTrue(Str::isUuid($lead->uuid), 'Wrong UUID format');
        });
    }
}
