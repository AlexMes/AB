<?php

namespace Leads;

use App\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterLeadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresFirstNameForLead()
    {
        $this->postJson(route('leads.register'), [
            'firstname' => null,
        ])->assertStatus(422)->assertJsonValidationErrors('firstname');
    }

    /** @test */
    public function firstNameMustBeAtLeads3CharactersLong()
    {
        $this->postJson(route('leads.register'), [
            'firstname' => 'yo',
        ])->assertStatus(422)->assertJsonValidationErrors('firstname');
    }

    /** @test */
    public function firstNameShouldBeShorterThen30Characters()
    {
        $this->postJson(route('leads.register'), [
            'firstname' => Str::random(31),
        ])->assertStatus(422)->assertJsonValidationErrors('firstname');
    }

    /** @test */
    public function itDoesNotRequireLastNameForLead()
    {
        $this->postJson(route('leads.register'), [
            'lastname' => null,
        ])->assertJsonMissingValidationErrors('lastname');
    }

    /** @test */
    public function lastNameMustBeAtLeads3CharactersLong()
    {
        $this->postJson(route('leads.register'), [
            'lastname' => 'yo',
        ])->assertStatus(422)->assertJsonValidationErrors('lastname');
    }

    /** @test */
    public function lastNameShouldBeShorterThen30Characters()
    {
        $this->postJson(route('leads.register'), [
            'lastname' => Str::random(31),
        ])->assertStatus(422)->assertJsonValidationErrors('lastname');
    }

    /** @test */
    public function middleNameMustBeAtLeads3CharactersLong()
    {
        $this->postJson(route('leads.register'), [
            'middlename' => 'yo',
        ])->assertStatus(422)->assertJsonValidationErrors('middlename');
    }

    /** @test */
    public function middleNameShouldBeShorterThen30Characters()
    {
        $this->postJson(route('leads.register'), [
            'middlename' => Str::random(31),
        ])->assertStatus(422)->assertJsonValidationErrors('middlename');
    }

    /** @test */
    public function emailMustBeValidWhenPassed()
    {
        $this->postJson(route('leads.register'), [
            'email' => 'not-a-valid-email',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    /** @test */
    public function phoneMustBeNumeric()
    {
        $this->postJson(route('leads.register'), [
            'phone' => 'not-a-number',
        ])->assertStatus(422)->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function phoneMustBeLongerThan8Chars()
    {
        $this->postJson(route('leads.register'), [
            'phone' => '1234556',
        ])->assertStatus(422)->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function phoneMustBeShorterThan15Chars()
    {
        $this->postJson(route('leads.register'), [
            'phone' => '1234567891011120',
        ])->assertStatus(422)->assertJsonValidationErrors('phone');
    }

    /** @test */
    public function itRegistersLeadFromLanding()
    {
        Event::fake();
        $this->withoutExceptionHandling();

        $landing = factory(Domain::class)->state(Domain::LANDING)->create();

        $this->postJson(route('leads.register'), [
            'firstname' => 'Test',
            'lastname'  => 'test',
            'phone'     => '7949379992',
            'domain'    => $landing->url,
        ])->assertStatus(201);
    }
}
