<?php

namespace Tests\Feature;

use App\TelegramChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuth()
    {
        $this->assertGuest();

        $this->getJson(route('telegram.channels.index'))->assertStatus(401);
    }

    /** @test */
    public function itVisible()
    {
        $this->signIn();

        $this->getJson(route('telegram.channels.index'))->assertStatus(200);
    }

    /** @test */
    public function itReturnsChannels()
    {
        $this->signIn();

        $channels = factory(TelegramChannel::class, 10)->create();

        $this->getJson(route('telegram.channels.index'))
            ->assertStatus(200)
            ->assertJson([
                'data' => $channels->toArray(),
            ]);
    }

    /** @test */
    public function userCanCreateChannel()
    {
        $this->signIn();

        $this->postJson(route('telegram.channels.store'), [
            'name' => 'Test'
        ])->assertStatus(201);

        $this->assertDatabaseHas('telegram_channels', ['name' => 'Test']);
    }

    /** @test */
    public function userCanUpdateChannel()
    {
        $this->signIn();

        $channel = factory(TelegramChannel::class)->create();

        $this->assertNotEquals('Test', $channel->name);

        $this->putJson(route('telegram.channels.update', $channel), [
            'name' => 'Test'
        ])->assertStatus(202);

        $this->assertDatabaseHas('telegram_channels', ['name' => 'Test']);
    }

    /** @test */
    public function userCanDeleteChannel()
    {
        $this->signIn();

        $channel = factory(TelegramChannel::class)->create();

        $this->assertNotEquals('Test', $channel->name);

        $this->deleteJson(route('telegram.channels.destroy', $channel))->assertStatus(204);

        $this->assertSoftDeleted('telegram_channels', ['id' => $channel->id]);
    }
}
