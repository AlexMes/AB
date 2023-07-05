<?php

namespace Tests\Unit;

use App\Deposit;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositBenefitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itUpdatesBenefitIfItPassedDirectly()
    {
        /** @var \App\Deposit $deposit */
        $deposit = factory(Deposit::class)->create();

        $this->assertNull($deposit->benefit);

        $deposit->updateBenefit(0.01);

        $this->assertEquals(0.01, $deposit->refresh()->benefit);
    }

    /** @test */
    public function itUpdatesBenefitFromOfficeSettings()
    {
        /** @var \App\Deposit $deposit */
        $deposit = factory(Deposit::class)->create([
            'office_id' => factory(Office::class)->create(['cpa' => 400])
        ]);

        $this->assertNull($deposit->benefit);

        $deposit->updateBenefit();

        $this->assertEquals(400, $deposit->refresh()->benefit);
    }

    /** @test */
    public function benefitStayEmptyWhenNDataCanBeUsed()
    {
        /** @var \App\Deposit $deposit */
        $deposit = factory(Deposit::class)->create();

        $this->assertNull($deposit->benefit);
        $this->assertNull($deposit->office_id);

        $deposit->updateBenefit();

        $this->assertEquals(0, $deposit->refresh()->benefit);
    }


    /** @test */
    public function itUpdatesBenefitFromOfficeSettingsAfterCreation()
    {
        $office =  factory(Office::class)->create(['cpa' => 400]);

        /** @var \App\Deposit $deposit */
        $deposit = factory(Deposit::class)->create([
            'office_id' => $office->id,
        ]);

        $this->assertNull($deposit->benefit);

        $this->assertEquals(400, $deposit->refresh()->benefit);
    }
}
