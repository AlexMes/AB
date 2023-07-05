<?php

namespace Leads\Pipes;

use App\Lead;
use App\Leads\Pipes\EnsureLeadIsValid;
use Tests\TestCase;

class EnsureLeadIsValidTest extends TestCase
{
    /** @test */
    public function itPassesValidLeadFurther()
    {
        $lead = new Lead(['valid' => true]);

        $pipe = new EnsureLeadIsValid();

        $pipe->handle($lead, fn ($lead) => $this->assertNotNull($lead));
    }

    /** @test */
    public function itStopsAssignmentForInvalidLeads()
    {
        $lead = new Lead(['valid' => false]);

        $pipe = new EnsureLeadIsValid();

        $this->assertFalse($lead->isValid());
        $pipe->handle($lead, fn ($lead) => $this->assertTrue(false, 'Invalid lead goes to assignment'));
    }
}
