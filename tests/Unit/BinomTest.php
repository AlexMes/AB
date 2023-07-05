<?php

namespace Tests\Unit;

use App\Binom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BinomTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itEnablesBinom()
    {
        $binom = Binom::create([
            'name'         => 'test',
            'url'          => 'https://ex.com',
            'enabled'      => false,
            'access_token' => '213'
        ]);
        $this->assertFalse($binom->isEnabled());

        $this->assertTrue($binom->enable()->isEnabled());
    }

    /** @test */
    public function isDisablesBinom()
    {
        $binom = Binom::create([
            'name'         => 'test',
            'url'          => 'https://ex.com',
            'enabled'      => true,
            'access_token' => '213'
        ]);
        $this->assertTrue($binom->isEnabled());

        $this->assertFalse($binom->disable()->isEnabled());
    }
}
