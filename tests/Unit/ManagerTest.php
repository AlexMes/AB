<?php

namespace Tests\Unit;

use App\Manager;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    /** @test */
    public function itExtractsIdFromUrl()
    {
        $manager = new Manager();

        $manager->spreadsheet_id = 'https://docs.google.com/spreadsheets/d/1mrsetjgfZI2BIypz7SGHMOfHGv6kTKTzY0xOM5c6TXY/edit#gid=1842172258';

        $this->assertEquals('1mrsetjgfZI2BIypz7SGHMOfHGv6kTKTzY0xOM5c6TXY', $manager->spreadsheet_id);
    }

    /** @test */
    public function itExctractsNothingIfSpreadSheetIsNull()
    {
        $manager = new Manager();

        $manager->spreadsheet_id = null;

        $this->assertNull($manager->spreadsheet_id);
    }

    /** @test */
    public function itKeepsSheetIdIfOnlySpreadSheetIdIsPassed()
    {
        $manager = new Manager();

        $manager->spreadsheet_id = '1mrsetjgfZI2BIypz7SGHMOfHGv6kTKTzY0xOM5c6TXY';

        $this->assertNotNull($manager->spreadsheet_id);
        $this->assertEquals('1mrsetjgfZI2BIypz7SGHMOfHGv6kTKTzY0xOM5c6TXY', $manager->spreadsheet_id);
    }
}
