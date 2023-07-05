<?php

namespace Tests\Unit;

use App\DestinationDrivers\Contracts\Bitrix24;
use App\LeadDestination;
use App\LeadDestinationDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadDestinationDriverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\LeadDestinationSeeder::class);
    }

    /** @test */
    public function bitrix24DriverNotMappedLeadIfConfigurationStatusesIsNull()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            'statuses' => null
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $lead = [
            'NAME'      => 'test',
            'STATUS_ID' => 1,
        ];

        $this->assertSame($lead, $driver->mapResponse($lead));
    }

    /** @test */
    public function bitrix24DriverNotMappedLeadIfResponseIsNull()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            'statuses' => [1 => 'NEW']
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $this->assertNull($driver->mapResponse(null));
    }

    /** @test */
    public function bitrix24DriverNotMappedLeadStatusIfConfigurationStatusesNotSetUp()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            // 'statuses' => null
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $lead = [
            'NAME'      => 'test',
            'STATUS_ID' => 1,
        ];

        $this->assertSame($lead, $driver->mapResponse($lead));
    }

    /** @test */
    public function bitrix24DriverNotMappedLeadStatusIfNeedleConfigurationStatusesIsEmpty()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            'statuses' => [],
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $lead = [
            'NAME'      => 'test',
            'STATUS_ID' => 1,
        ];

        $this->assertSame($lead, $driver->mapResponse($lead));
    }

    /** @test */
    public function bitrix24DriverNotMappedLeadStatusIfNeedleConfigurationStatusesDoesntHave()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            'statuses' => [1 => 'NEW'],
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $lead = [
            'NAME'      => 'test',
            'STATUS_ID' => 2,
        ];

        $this->assertSame($lead, $driver->mapResponse($lead));
    }

    /** @test */
    public function bitrix24DriverMappedLeadStatusIfConfigurationStatusesIsRight()
    {
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = [
            'url'      => null,
            'statuses' => [1 => 'NEW'],
        ];

        $destination->save();

        /** @var Bitrix24 */
        $driver = $destination->initialize();

        $lead = [
            'NAME'      => 'test',
            'STATUS_ID' => 1,
        ];

        $this->assertNotSame($lead, $driver->mapResponse($lead));
    }
}
