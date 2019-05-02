<?php

namespace Orkhanahmadov\CBARCurrency;

class Currency
{
    /**
     * @var float
     */
    public $rate;
    /**
     * @var int
     */
    public $nominal;

    /**
     * Currency constructor.
     *
     * @param float $rate
     * @param int $nominal
     */
    public function __construct(float $rate, int $nominal = 1)
    {
        $this->rate = $rate;
        $this->nominal = $nominal;
    }
}
