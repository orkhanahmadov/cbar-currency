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

    public function fetchCurrencies(string $date)
    {
        $response = $this->client->get('https://www.cbar.az/currencies/'.$date.'.xml');

        $currencies = simplexml_load_string($response->getBody()->getContents());

        foreach ($currencies->ValType[1]->Valute as $currency) {
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
        if (isset($this->currencies[$currency])) {
            return $this->currencies[$currency];
        }

        throw new CurrencyException('Currency with '.$currency.' code is not available');
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
     * @return CBAR
     */
    public function setCurrencies(array $currencies): CBAR
    {
        $this->currencies = $currencies;
        return $this;
    }
}
