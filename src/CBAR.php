<?php

namespace Orkhanahmadov\CBARCurrency;

use GuzzleHttp\Client;
use Orkhanahmadov\CBARCurrency\Exceptions\DateException;
use Orkhanahmadov\CBARCurrency\Exceptions\CurrencyException;

/**
 * @property float|int USD
 * @property float|int EUR
 * @property float|int AUD
 * @property float|int ARS
 * @property float|int BYN
 * @property float|int BRL
 * @property float|int AED
 * @property float|int ZAR
 * @property float|int KRW
 * @property float|int CZK
 * @property float|int CLP
 * @property float|int CNY
 * @property float|int DKK
 * @property float|int GEL
 * @property float|int HKD
 * @property float|int INR
 * @property float|int GBP
 * @property float|int IDR
 * @property float|int IRR
 * @property float|int SEK
 * @property float|int CHF
 * @property float|int ILS
 * @property float|int CAD
 * @property float|int KWD
 * @property float|int KZT
 * @property float|int KGS
 * @property float|int LBP
 * @property float|int MYR
 * @property float|int MXN
 * @property float|int MDL
 * @property float|int EGP
 * @property float|int NOK
 * @property float|int UZS
 * @property float|int PLN
 * @property float|int RUB
 * @property float|int SGD
 * @property float|int SAR
 * @property float|int SDR
 * @property float|int TRY
 * @property float|int TWD
 * @property float|int TJS
 * @property float|int TMT
 * @property float|int UAH
 * @property float|int JPY
 * @property float|int NZD
 *
 * @method USD(int|float $int)
 * @method EUR(int|float $int)
 * @method AUD(int|float $int)
 * @method ARS(int|float $int)
 * @method BYN(int|float $int)
 * @method BRL(int|float $int)
 * @method AED(int|float $int)
 * @method ZAR(int|float $int)
 * @method KRW(int|float $int)
 * @method CZK(int|float $int)
 * @method CLP(int|float $int)
 * @method CNY(int|float $int)
 * @method DKK(int|float $int)
 * @method GEL(int|float $int)
 * @method HKD(int|float $int)
 * @method INR(int|float $int)
 * @method GBP(int|float $int)
 * @method IDR(int|float $int)
 * @method IRR(int|float $int)
 * @method SEK(int|float $int)
 * @method CHF(int|float $int)
 * @method ILS(int|float $int)
 * @method CAD(int|float $int)
 * @method KWD(int|float $int)
 * @method KZT(int|float $int)
 * @method KGS(int|float $int)
 * @method LBP(int|float $int)
 * @method MYR(int|float $int)
 * @method MXN(int|float $int)
 * @method MDL(int|float $int)
 * @method EGP(int|float $int)
 * @method NOK(int|float $int)
 * @method UZS(int|float $int)
 * @method PLN(int|float $int)
 * @method RUB(int|float $int)
 * @method SGD(int|float $int)
 * @method SAR(int|float $int)
 * @method SDR(int|float $int)
 * @method TRY(int|float $int)
 * @method TWD(int|float $int)
 * @method TJS(int|float $int)
 * @method TMT(int|float $int)
 * @method UAH(int|float $int)
 * @method JPY(int|float $int)
 * @method NZD(int|float $int)
 */
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
     *
     * @param string|null $date
     */
    public function __construct(?string $date = null)
    {
        $this->client = new Client();
        $this->date = $date ?: date('d.m.Y');
    }

    /**
     * Sets currency rate date.
     *
     * @param string $date
     *
     * @throws DateException
     *
     * @return $this
     */
    public function for(string $date)
    {
        $this->date = $date;

        if (! isset($this->rates[$this->date])) {
            $this->getRatesFromCBAR();
        }

        return $this;
    }

    /**
     * Gets currency rate.
     *
     * @param string $currency
     *
     * @throws DateException
     * @throws CurrencyException
     *
     * @return mixed
     */
    public function __get(string $currency)
    {
        if (! isset($this->rates[$this->date])) {
            $this->getRatesFromCBAR();
        }

        if (! isset($this->rates[$this->date][$currency])) {
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
     * Converts currency with given amount.
     *
     * @param string $currency
     * @param array  $arguments
     *
     * @throws DateException
     * @throws CurrencyException
     *
     * @return float|int
     */
    public function __call(string $currency, array $arguments)
    {
        if (! isset($this->rates[$this->date])) {
            $this->getRatesFromCBAR();
        }

        if (! isset($this->rates[$this->date][$currency])) {
            throw new CurrencyException('Currency with '.$currency.' code is not available');
        }

        return $this->$currency * ($arguments[0] ?? 1);
    }

    /**
     * Converts AZN amount to other currency.
     *
     * @param float|int $amount
     *
     * @return CBAR
     */
    public function AZN($amount = 1)
    {
        $this->aznAmount = $amount;

        return $this;
    }

    /**
     * Fetches currency rates from CBAR with given date.
     *
     * @throws DateException
     */
    private function getRatesFromCBAR()
    {
        if (! $validatedDate = strtotime($this->date)) {
            throw new DateException($this->date.' is not a valid date.');
        }
        $this->date = date('d.m.Y', $validatedDate);

        $response = $this->client->get('https://www.cbar.az/currencies/'.$this->date.'.xml');

        $xml = simplexml_load_string($response->getBody()->getContents());

        foreach ($xml->ValType[1]->Valute as $currency) {
            $this->rates[$this->date][(string) $currency->attributes()['Code']] = [
                'rate'    => (float) $currency->Value,
                'nominal' => (int) $currency->Nominal,
            ];
        }
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
     *
     * @return CBAR
     */
    public function setRates(array $rates): self
    {
        $this->rates = $rates;

        return $this;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }
}
