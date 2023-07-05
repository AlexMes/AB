<?php

namespace Tests\Feature\External;

use App\Affiliate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListLeadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function itRequiresApiKey()
    {
        $response = $this->getJson('/external/leads');

        $response->assertStatus(401);
    }

    /** @test */
    public function itWorksWithApiKey()
    {
        /** @var Affiliate $affiliate */
        $affiliate = factory(Affiliate::class)->create();

        $q =  http_build_query(['api_key' => $affiliate->api_key]);

        $this->getJson(sprintf("/external/leads?%s", $q))->assertStatus(200);
    }
}
