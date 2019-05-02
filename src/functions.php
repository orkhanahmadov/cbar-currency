<?php

use Orkhanahmadov\CBARCurrency\CBAR;

if (!function_exists('cbar')) {
    /**
     * @param string|null $date
     * @return CBAR
     * @throws \Orkhanahmadov\CBARCurrency\Exceptions\DateException
     */
    function cbar(?string $date = null)
    {
        return new CBAR($date);
    }
}
