<?php

namespace Orkhanahmadov\CBARCurrency\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\CBARCurrency\CBAR;
use Orkhanahmadov\CBARCurrency\Exceptions\CurrencyException;
use Orkhanahmadov\CBARCurrency\Exceptions\DateException;
use Orkhanahmadov\CBARCurrency\Tests\TestCase;

class CBARTest extends TestCase
{
    use UsesGuzzler;

    public function invalidDates()
    {
        return [
            ['40.10.2019'],
            ['15.20.2019'],
            ['abc'],
            ['111'],
        ];
    }

    /**
     * @dataProvider invalidDates
     *
     * @throws DateException
     */
    public function test_initializing_class_with_invalid_date_throws_exception($date)
    {
        $this->expectException(DateException::class);
        $this->expectExceptionMessage($date.' is not a valid date.');

        (new CBAR($date))->EUR;
    }

    /**
     * @dataProvider invalidDates
     *
     * @throws DateException
     */
    public function test_passing_invalid_date_to_for_method_throws_exception($date)
    {
        $this->expectException(DateException::class);
        $this->expectExceptionMessage($date.' is not a valid date.');

        $cbar = new CBAR();
        $cbar->for($date);
    }

    public function test_for_sets_currencies_from_cbar()
    {
        $cbar = new CBAR();
        $cbar->setClient($this->guzzler->getClient());
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/01.05.2019.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));
        $this->assertEmpty($cbar->getRates());

        $cbar->for('01.05.2019');

        $this->assertNotEmpty($cbar->getRates());
    }

    public function test_guzzle_will_not_fetch_cbar_api_if_rates_for_given_date_are_already_available()
    {
        $cbar = new CBAR();
        $cbar->setClient($this->guzzler->getClient());
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/01.05.2019.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));

        $cbar->for('01.05.2019');
        $cbar->for('01.05.2019');
    }

    public function test_magic_get_method_returns_currency_rate()
    {
        $cbar = new CBAR('01.05.2019');
        $cbar->setRates([
            '01.05.2019' => [
                'USD' => [
                    'nominal' => 1,
                    'rate'    => 1.7053,
                ],
            ],
        ]);

        $this->assertEquals(1.7053, $cbar->USD);
    }

    public function test_magic_get_method_throws_exception_if_currency_is_not_available()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionMessage('Currency with EUR code is not available');
        $cbar = new CBAR('01.05.2019');
        $cbar->setRates([
            '01.05.2019' => [
                'USD' => [
                    'nominal' => 1,
                    'rate'    => 1.7053,
                ],
            ],
        ]);

        $cbar->EUR;
    }

    public function test_method_get_method_get_rates_from_cbar_if_rates_for_given_date_is_not_available()
    {
        $cbar = new CBAR();
        $cbar->setClient($this->guzzler->getClient());
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/'.date('d.m.Y').'.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));
        $this->assertEmpty($cbar->getRates());

        $cbar->USD;
    }

    public function test_magic_set_method_returns_calculated_amount()
    {
        $cbar = new CBAR('01.05.2019');
        $cbar->setRates([
            '01.05.2019' => [
                'USD' => [
                    'nominal' => 1,
                    'rate'    => 1.7053,
                ],
            ],
        ]);

        $this->assertEquals(170.53, $cbar->USD(100));
    }

    public function test_magic_set_method_throws_exception_if_currency_is_not_available()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionMessage('Currency with EUR code is not available');
        $cbar = new CBAR('01.05.2019');
        $cbar->setRates([
            '01.05.2019' => [
                'USD' => [
                    'nominal' => 1,
                    'rate'    => 1.7053,
                ],
            ],
        ]);

        $cbar->EUR(100);
    }

    public function test_method_set_method_get_rates_from_cbar_if_rates_for_given_date_is_not_available()
    {
        $cbar = new CBAR();
        $cbar->setClient($this->guzzler->getClient());
        $this->guzzler
            ->expects($this->once())
            ->get('https://www.cbar.az/currencies/'.date('d.m.Y').'.xml')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../dummy_response.xml')));
        $this->assertEmpty($cbar->getRates());

        $cbar->USD(100);
    }

    public function test_azn_method_returns_azn_to_other_currency_conversion_with_given_amount()
    {
        $cbar = new CBAR('01.05.2019');
        $cbar->setRates([
            '01.05.2019' => [
                'EUR' => [
                    'nominal' => 1,
                    'rate'    => 2,
                ],
            ],
        ]);

        $this->assertEquals(0.5, $cbar->AZN()->EUR);
        $this->assertEquals(5, $cbar->AZN(10)->EUR);
    }
}
