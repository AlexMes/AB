<?php

namespace Tests\Feature;

use App\Domain;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreLeadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configure test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function itSavesLead()
    {
        $this->post(route('leads.external'), [
            'phone'     => '8900000000',
            'firstname' => 'Test'
        ])->assertStatus(201);

        $this->assertDatabaseHas('leads', ['phone' => '8900000000']);
    }

    /** @test */
    public function itSkipsDuplicatePhoneLeads()
    {
        $this->withExceptionHandling();

        $this->postJson(route('leads.external'), [
            'phone'      => '890000000',
            'firstname'  => 'Test'
        ])->assertStatus(201);

        $this->postJson(route('leads.external'), [
            'phone'      => '890000000',
            'firstname'  => 'Test'
        ])->assertJsonValidationErrors('general');

        $this->assertDatabaseHas('leads', ['phone' => '890000000']);

        $this->assertCount(1, Lead::wherePhone('890000000')->get());
    }

    /** @test */
    public function itDetectsLandingPage()
    {
        $landing = factory(Domain::class)->state(Domain::LANDING)->create();

        $this->post(route('leads.external'), [
            'phone'       => '8900000000',
            'firstname'   => 'Test',
            'domain'      => parse_url($landing->url)['host'],
        ])->assertStatus(201);

        $this->assertDatabaseHas('leads', ['domain' => parse_url($landing->url)['host']]);
    }

    /** @test */
    public function itStoresAllRequestData()
    {
        $data = [
            'phone'       => '8900000000',
            'firstname'   => 'Test',
        ];

        $this->post(route('leads.external'), [
            'phone'       => '8900000000',
            'firstname'   => 'Test',
        ])->assertStatus(201);

        $this->assertEquals($data, Lead::first()->requestData);
    }

    /** @test */
    public function itStoresLandingId()
    {
        $landing = factory(Domain::class)->state(Domain::LANDING)->create([
            'url' => 'https://test.com'
        ]);

        $this->post(route('leads.external'), [
            'phone'       => '8900000000',
            'firstname'   => 'Test',
            'domain'      => 'test.com'
        ])->assertStatus(201);

        $this->assertDatabaseHas('leads', ['landing_id' => $landing->id]);
    }

    /** @test */
    public function itDetectsObsceneNames()
    {
        $this->post(route('leads.external'), [
            'phone'       => '8900000000',
            'firstname'   => 'Хуяка',
        ])->assertStatus(201);

        $this->assertFalse(Lead::first()->valid);
    }
}
