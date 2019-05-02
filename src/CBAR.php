<?php

namespace Orkhanahmadov\CBARCurrency;

use GuzzleHttp\Client;
use Orkhanahmadov\CBARCurrency\Exceptions\CurrencyException;

class CBAR
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $currencies = [];

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function for(string $date)
    {
        $response = $this->client->get('https://www.cbar.az/currencies/'.$date.'.xml');

        $xml = simplexml_load_string($response->getBody()->getContents());

        foreach ($xml->ValType[1]->Valute as $currency) {
            $this->currencies[(string) $currency->attributes()['Code']] = [
                'rate' => (float) $currency->Value,
                'nominal' => (int) $currency->Nominal
            ];
        }

        return $this;
    }

    /**
     * @param string $currency
     * @return mixed
     * @throws CurrencyException
     */
    public function __get(string $currency)
    {
        if (!isset($this->currencies[$currency])) {
            throw new CurrencyException('Currency with '.$currency.' code is not available');
        }

        return bcdiv($this->currencies[$currency]['rate'], $this->currencies[$currency]['nominal'], 6);
    }

    /**
     * @param string $currency
     * @param array $arguments
     * @return $this
     * @throws CurrencyException
     */
    public function __call(string $currency, array $arguments)
    {
        if (!isset($this->currencies[$currency])) {
            throw new CurrencyException('Currency with '.$currency.' code is not available');
        }

        return $this->$currency * $arguments[0];
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param array $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->currencies = $currencies;
    }

    /**
     * @return array
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }
}
