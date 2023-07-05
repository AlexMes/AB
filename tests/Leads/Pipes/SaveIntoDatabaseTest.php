<?php

namespace Leads\Pipes;

use App\Lead;
use App\Leads\Pipes\SaveIntoDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SaveIntoDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /** @test */
    public function itSavesLeadInDatabase()
    {
        $lead = new Lead([
            'firstname' => 'Test',
            'phone'     => '9379992'
        ]);

        $pipe = new SaveIntoDatabase();

        $pipe->handle($lead, function ($lead) {
            $this->assertTrue($lead->wasRecentlyCreated);
            $this->assertDatabaseHas('leads', ['id' => $lead->id]);
        });
    }
}
