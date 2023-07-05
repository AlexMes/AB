<?php

namespace Tests\Feature\External;

use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RegistrationDateIsStored extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->postJson(route('leads.external'), [
            'firstname'  => 'Yolo',
            'phone'      => '380686001400',
            'created_at' => '2020-11-28T06:21:38.930Z'
        ])->assertStatus(201);

        $date = Carbon::parse('2020-11-28T06:21:38.930Z');
        $lead = Lead::wherePhone('380686001400')->first();

        $this->assertNotNull($lead, 'Lead not fukcing registered');
        $this->assertEquals($date->toDateTimeString(), $lead->created_at, 'Registration date is ignored');
    }
}
