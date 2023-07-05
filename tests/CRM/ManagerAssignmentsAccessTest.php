<?php

namespace CRM;

use App\Manager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerAssignmentsAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuthOnCrmGuard()
    {
        $this->assertGuest('crm');

        $this->get(route('crm.assignments.index'))->assertRedirect('/login');
    }

    /** @test */
    public function itRequiresAuthOnWebGuard()
    {
        $this->assertGuest('web');

        $this->get(route('crm.assignments.index'))->assertRedirect('/login');
    }

    /** @test */
    public function managerCanSeeDashboard()
    {
        $this->setGuard('crm')->signIn(factory(Manager::class)->create())->withoutExceptionHandling();

        $this->get(route('crm.assignments.index'))
            ->assertStatus(200)
            ->assertViewIs('crm::assignments.index');
    }

    /** @test */
    public function webUserCanSeeDashboard()
    {
        $this->setGuard('web')->signIn();

        $this->get(route('crm.assignments.index'))
            ->assertStatus(200)
            ->assertViewIs('crm::assignments.index');
    }
}
