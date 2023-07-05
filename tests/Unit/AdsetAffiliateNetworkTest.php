<?php

namespace Tests\Unit;

use App\Facebook\AdSet;
use PHPUnit\Framework\TestCase;

class AdsetAffiliateNetworkTest extends TestCase
{
    /**
     * @test
     * @dataProvider facebookNamesProvider
     *
     * @param mixed $name
     */
    public function itReturnsFalseWhenNoAnIsInString($name)
    {
        $adset = new AdSet(['name' => $name]);

        $this->assertFalse($adset->fromAffiliateNetwork());
    }

    /** @test
     * @dataProvider anNamesProvider
     *
     * @param mixed $name
     */
    public function itReturnsTrueWhenAnIsInString($name)
    {
        $adset = new AdSet(['name' => $name]);

        $this->assertTrue($adset->fromAffiliateNetwork());
    }

    public function facebookNamesProvider()
    {
        return [
            ['JoSva0/0-EUG-1305-fb-rbk-gazprom-campaign-gazcreoolgafb1864-zeroint-android-ru-fm1864-fin-0403_gaz_hooklong3_4u3-1'],
            ['Dadiv1/2-ALEX-1505-fb-rbk-rosneft-campaign-zolotofb1865-ru-fm1865-0602_zolnac_news1-3'],
            ['Dadiv1/5-ALEX-1505-fb-rbk-rosneft-campaign-zolotofb1865-ru-fm1865-1503_KA_rosneft_hooklong2-2'],
            ['UlyanaKrut1/1-ALEX-1505-fb-rbk-gazprom-campaign-gazpromfb1865-zeroint-ru-fm1864-1105_KA_gaz_3deputat2-4'],
            ['krusko24-1505-eug-fban-rosneft-campaign-zolotofb1865-zeroint-pourer--ru-fm1865--0602_zolnac_news1-1'],
        ];
    }

    public function anNamesProvider()
    {
        return [
            ['krusko24-1505-eug-fban-rosneft-campaign-zolotofb1865-zeroint-an-pourer--ru-fm1865--0602_zolnac_news1-2'],
            ['krusko23-1505-eug-fban-rosneft-campaign-zolotofb1865-zeroint-an-pourer--ru-fm1865--0602_zolnac_news1-3'],
            ['krusko22-1505-eug-fban-rosneft-campaign-zolotofb1865-zeroint-an-pourer--ru-fm1865--0602_zolnac_news1-4'],
            ['krusko22-1505-eug-fban-rosneft-campaign-zolotofb1865-zeroint-an-pourer--ru-fm1865--0602_zolnac_news1-1'],
            ['miroslavas00-1505-alex-fban-gazprom-campaign-gazpromfb1865-zeroint-an-pourer--ru-fm2565-fin-updated-3'],
        ];
    }
}
