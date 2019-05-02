<?php

namespace Orkhanahmadov\CBARCurrency\Tests\Unit;

use Orkhanahmadov\CBARCurrency\CBAR;
use Orkhanahmadov\CBARCurrency\Tests\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @throws \Orkhanahmadov\CBARCurrency\Exceptions\DateException
     */
    public function test_global_function()
    {
        $cbar = cbar('01.05.2019')->setRates([
            '01.05.2019' => [
                'EUR' => [
                    'nominal' => 1,
                    'rate' => 2
                ]
            ]
        ]);
        $this->assertInstanceOf(CBAR::class, $cbar);
        $this->assertEquals(2, $cbar->EUR);
        $this->assertEquals(10, $cbar->EUR(5));
        $this->assertEquals(5, $cbar->AZN(10)->EUR);
    }
}
