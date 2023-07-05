<?php

namespace Tests\Feature;

use App\Domain;
use App\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransferOrderDomainsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuthentication()
    {
        Event::fake();
        $this->assertGuest();

        $order = factory(Order::class)->create();

        $this->postJson(route('order-transfer-domains', $order))
            ->assertStatus(401);
    }

    /** @test */
    public function adminCanTransferDomains()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();
        $domains = factory(Domain::class)->create(['order_id' => $order->id]);

        $targetOrder = factory(Order::class)->create();

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => $domains->pluck('id')]
        )->assertStatus(204);
    }

    /** @test */
    public function orderIdIsRequired()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();
        $domains = factory(Domain::class)->create(['order_id' => $order->id]);

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => null, 'domain_ids' => $domains->pluck('id')]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    /** @test */
    public function orderIdMustExist()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();
        $domains = factory(Domain::class)->create(['order_id' => $order->id]);

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => 100500, 'domain_ids' => $domains->pluck('id')]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    /** @test */
    public function domainIdsAreRequired()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();

        $targetOrder = factory(Order::class)->create();

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => null]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domain_ids']);
    }

    /** @test */
    public function domainIdsMustBeArray()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();

        $targetOrder = factory(Order::class)->create();

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => 'not array']
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domain_ids']);
    }

    /** @test */
    public function domainIdsMustExist()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();

        $targetOrder = factory(Order::class)->create();

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => [100500]]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domain_ids.0']);
    }


    /** @test */
    public function itTransfersDomains()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create();
        $domains = factory(Domain::class, 2)->create(['order_id' => $order->id]);

        $targetOrder = factory(Order::class)->create();

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => $domains->pluck('id')]
        )->assertStatus(204);

        $this->assertCount(0, $order->refresh()->domains);
        $this->assertCount(2, $targetOrder->refresh()->domains);
    }

    /** @test */
    public function itUpdatesProgress()
    {
        Event::fake();
        $this->signIn();

        $order   = factory(Order::class)->create(['links_done_count' => 2]);
        factory(Domain::class)->create(['order_id' => $order->id]);
        $domains = factory(Domain::class, 2)->state('ready')->create(['order_id' => $order->id]);

        $targetOrder = factory(Order::class)->create(['links_done_count' => 0]);

        $this->postJson(
            route('order-transfer-domains', $order),
            ['order_id' => $targetOrder->id, 'domain_ids' => $domains->pluck('id')]
        )->assertStatus(204);

        $this->assertEquals(0, $order->refresh()->links_done_count);
        $this->assertEquals(2, $targetOrder->refresh()->links_done_count);
    }
}
