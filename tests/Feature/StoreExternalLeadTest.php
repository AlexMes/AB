<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreExternalLeadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRespondsWithRightCode()
    {
        $this->assertDatabaseMissing('leads', ['phone' => '79456767678']);

        $this->postJson(route('leads.external'), [
            'phone'      => '79456767678',
            'firstname'  => 'Testing shit'
        ])->assertStatus(201);
    }

    /** @test */
    public function itRespondsWithMessage()
    {
        $this->post(route('leads.external'), [
            'phone'      => '75454454',
            'firstname'  => 'af'
        ])->assertJson(['message' => 'Stored']);
    }
}
