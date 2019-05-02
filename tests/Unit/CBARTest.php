<?php

namespace Orkhanahmadov\CBARCurrency\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\CBARCurrency\Exceptions\CurrencyException;
use Orkhanahmadov\CBARCurrency\CBAR;
use Orkhanahmadov\CBARCurrency\Tests\TestCase;

class CBARTest extends TestCase
{
    use UsesGuzzler;

    /**
     * @var CBAR
     */
    private $cbar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cbar = new CBAR();
        $this->cbar->setClient($this->guzzler->getClient());
    }

//    public function test_experiment()
//    {
//        $cbar = new CBAR();
//        $cbar->for('01.05.2019')->USD;
//        $cbar->for('01.05.2019')->USD(100)->toAZN();
//        $cbar->for('01.05.2019')->AZN(100)->toUSD();
//    }

    public function test_for_sets_currencies_from_cbar()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/01.05.2019.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));
        $this->assertEmpty($this->cbar->getCurrencies());

        $this->cbar->for('01.05.2019');

        $this->assertNotEmpty($this->cbar->getCurrencies());
    }

    public function test_magic_get_method_returns_currency_rate()
    {
        $cbar = new CBAR();
        $cbar->setCurrencies(['USD' => ['nominal' => 1, 'rate' => 1.7053]]);

        $this->assertEquals(1.7053, $cbar->USD);
    }

    public function test_magic_get_method_throws_exception_if_currency_is_not_available()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionMessage('Currency with EUR code is not available');
        $cbar = new CBAR();
        $cbar->setCurrencies(['USD' => ['nominal' => 1, 'rate' => 1.7053]]);

        $cbar->EUR;
    }

    public function test_magic_set_method_returns_calculated_amount()
    {
        $cbar = new CBAR();
        $cbar->setCurrencies(['USD' => ['nominal' => 1, 'rate' => 1.7053]]);

        $this->assertEquals(170.53, $cbar->USD(100));
    }

    public function test_magic_set_method_throws_exception_if_currency_is_not_available()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionMessage('Currency with EUR code is not available');
        $cbar = new CBAR();
        $cbar->setCurrencies(['USD' => ['nominal' => 1, 'rate' => 1.7053]]);

        $cbar->EUR(100);
    }
}