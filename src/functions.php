<?php

use Orkhanahmadov\CBARCurrency\CBAR;

if (!function_exists('cbar')) {
    /**
     * Helper function
     *
     * @param string|null $date
     *
     * @throws \Orkhanahmadov\CBARCurrency\Exceptions\DateException
     *
     * @return CBAR
     */
    function cbar(?string $date = null)
    {
        return new CBAR($date);
    }
}
