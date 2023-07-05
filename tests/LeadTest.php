<?php

namespace Tests;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(Form::class)->create();
    }

    /** @test */
    public function it_is_duplicate_if_it_has_a_payload_response_with_duplicate_flag_in_telephone_field()
    {
        $hasDuplicate = factory(Lead::class)->create();

        factory(Payload::class)->state('duplicate_response')->create(['lead_id' => $hasDuplicate->id]);

        $this->assertTrue($hasDuplicate->is_duplicate);
    }

    /** @test */
    public function it_is_duplicate_if_some_payload_response_has_duplicate_flag_in_telephone_field()
    {
        $hasDuplicate = factory(Lead::class)->create();

        factory(Payload::class)->state('duplicate_response')->create(['lead_id' => $hasDuplicate->id]);
        factory(Payload::class)->state('normal_response')->create(['lead_id' => $hasDuplicate->id]);
        factory(Payload::class)->create(['lead_id' => $hasDuplicate->id]); // null response from CRM

        $this->assertTrue($hasDuplicate->is_duplicate);
    }

    /** @test */
    public function it_is_not_duplicate_if_some_payload_ok()
    {
        $withoutDuplicate = factory(Lead::class)->create();

        factory(Payload::class)->state('normal_response')->create(['lead_id' => $withoutDuplicate->id]);
        factory(Payload::class)->create(['lead_id' => $withoutDuplicate->id]); // null response from CRM

        $this->assertFalse($withoutDuplicate->is_duplicate);
    }

    /** @test */
    public function it_is_not_duplicate_if_some_payload_response_null()
    {
        $withoutDuplicate = factory(Lead::class)->create();

        factory(Payload::class)->create(['lead_id' => $withoutDuplicate->id]); // null response from CRM

        $this->assertFalse($withoutDuplicate->is_duplicate);
    }

    /** @test */
    public function it_is_not_duplicate_if_some_payload_response_empty()
    {
        $withoutDuplicate = factory(Lead::class)->create();

        factory(Payload::class)->create([
            'lead_id'          => $withoutDuplicate->id,
            'responseContents' => ''
        ]);

        $this->assertFalse($withoutDuplicate->is_duplicate);
    }

    /** @test */
    public function it_is_not_duplicate_if_it_has_not_any_payloads()
    {
        $withoutDuplicate = factory(Lead::class)->create();

        $this->assertFalse($withoutDuplicate->is_duplicate);
    }
}
