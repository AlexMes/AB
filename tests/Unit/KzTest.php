<?php

namespace Tests\Unit;

use App\Offer;
use PHPUnit\Framework\TestCase;

class KzTest extends TestCase
{
    /**
     * @test
     * @dataProvider kzDataProvider
     *
     * @param $name
     */
    public function itDetectsKazakhstan($name)
    {
        $offer = new Offer(['name' => $name]);
        $this->assertTrue($offer->isKz());
    }

    public function kzDataProvider()
    {
        yield ['DELOKZ'];
        yield ['ZOLOTOKZ'];
        yield ['GAZKZ'];
    }

    /**
     * @test
     * @dataProvider otherDataProvider
     *
     * @param $name
     */
    public function itFalseOthers($name)
    {
        $offer = new Offer(['name' => $name]);
        $this->assertFalse($offer->isKz());
    }

    public function otherDataProvider()
    {
        yield ['DELOVOSTOK'];
        yield ['UKTMP'];
        yield ['ROSZOLOTO'];
        yield ['ZOLOTO2'];
        yield ['VOSTOK'];
        yield ['GAZMT'];
    }
}
