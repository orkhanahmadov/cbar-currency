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
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new CBAR();
        $this->parser->setClient($this->guzzler->getClient());
    }

    public function test_magic_method_returns_set_currencies()
    {
        $this->parser->setCurrencies(['USD' => 1.7053]);

        $this->assertEquals(1.7053, $this->parser->USD);
    }

    public function test_magic_method_throws_exception_if_currency_is_not_available()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionMessage('Currency with EUR code is not available');

        $this->parser->setCurrencies(['USD' => 1.7053]);

        $this->parser->EUR;
    }

    public function test_fetchCurrencies_method_downloads_new_currency_data_from_cbar()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/01.05.2019.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));

        $this->parser->fetchCurrencies('01.05.2019');
        $this->assertEquals(1.7, $this->parser->USD);
        $this->assertEquals(1.907, $this->parser->EUR);
        $this->assertEquals(0.0263, $this->parser->RUB);
    }
}
