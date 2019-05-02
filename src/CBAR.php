<?php

namespace Orkhanahmadov\CBARCurrency;

use GuzzleHttp\Client;
use Orkhanahmadov\CBARCurrency\Exceptions\CurrencyException;
use Orkhanahmadov\CBARCurrency\Exceptions\DateException;

class CBAR
{
    /**
     * @var string|null
     */
    private $date;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $rates = [];
    /**
     * @var float|int|null
     */
    private $aznAmount = null;

    /**
     * Parser constructor.
     * @param string|null $date
     * @throws DateException
     */
    public function __construct(?string $date = null)
    {
        $this->client = new Client();
        $this->date = date('d.m.Y');

        if ($date) {
            $this->setDate($date);
        }
    }

    /**
     * @param string $date
     * @return $this
     * @throws DateException
     */
    public function for(string $date)
    {
        $this->setDate($date);

        if (!isset($this->rates[$this->date])) {
            $this->getRatesFromCBAR();
        }

        return $this;
    }

    private function getRatesFromCBAR()
    {
        $response = $this->client->get('https://www.cbar.az/currencies/'.$this->date.'.xml');

        $xml = simplexml_load_string($response->getBody()->getContents());

        foreach ($xml->ValType[1]->Valute as $currency) {
            $this->rates[$this->date][(string) $currency->attributes()['Code']] = [
                'rate' => (float) $currency->Value,
                'nominal' => (int) $currency->Nominal
            ];
        }
    }

    /**
     * @param string $currency
     * @return mixed
     * @throws CurrencyException
     */
    public function __get(string $currency)
    {
        if (!isset($this->rates[$this->date])) {
            $this->getRatesFromCBAR();
        }

        if (!isset($this->rates[$this->date][$currency])) {
            throw new CurrencyException('Currency with '.$currency.' code is not available');
        }

        if ($this->aznAmount) {
            $conversion = bcdiv($this->aznAmount, $this->rates[$this->date][$currency]['rate'], 4);
            $this->aznAmount = null;
            return $conversion;
        }

        return bcdiv($this->rates[$this->date][$currency]['rate'], $this->rates[$this->date][$currency]['nominal'], 4);
    }

    /**
     * @param string $currency
     * @param array $arguments
     * @return float|int
     * @throws CurrencyException
     */
    public function __call(string $currency, array $arguments)
    {
        // todo: validate if date is available. if not, fetch new

        if (!isset($this->rates[$this->date][$currency])) {
            throw new CurrencyException('Currency with '.$currency.' code is not available');
        }

        return $this->$currency * $arguments[0];
    }

    /**
     * @param float|int $amount
     * @return CBAR
     */
    public function AZN($amount = 1)
    {
        $this->aznAmount = $amount;

        return $this;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param array $rates
     */
    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @param string $date
     * @throws DateException
     */
    public function setDate(string $date): void
    {
        if (!$validatedDate = strtotime($date)) {
            throw new DateException($date.' is not a valid date.');
        }

        $this->date = date('d.m.Y', $validatedDate);
    }
}
