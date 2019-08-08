# :dollar: PHP library to work with [CBAR](https://www.cbar.az/home?language=en) currency rates

[![Latest Stable Version](https://poser.pugx.org/orkhanahmadov/cbar-currency/v/stable)](https://packagist.org/packages/orkhanahmadov/cbar-currency)
[![Latest Unstable Version](https://poser.pugx.org/orkhanahmadov/cbar-currency/v/unstable)](https://packagist.org/packages/orkhanahmadov/cbar-currency)
[![Total Downloads](https://img.shields.io/packagist/dt/orkhanahmadov/cbar-currency)](https://packagist.org/packages/orkhanahmadov/cbar-currency)
[![GitHub license](https://img.shields.io/github/license/orkhanahmadov/cbar-currency.svg)](https://github.com/orkhanahmadov/cbar-currency/blob/master/LICENSE.md)

[![Build Status](https://img.shields.io/travis/orkhanahmadov/cbar-currency.svg)](https://travis-ci.org/orkhanahmadov/cbar-currency)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d5cf2c42b3f6febb6a29/test_coverage)](https://codeclimate.com/github/orkhanahmadov/cbar-currency/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/d5cf2c42b3f6febb6a29/maintainability)](https://codeclimate.com/github/orkhanahmadov/cbar-currency/maintainability)
[![Quality Score](https://img.shields.io/scrutinizer/g/orkhanahmadov/cbar-currency.svg)](https://scrutinizer-ci.com/g/orkhanahmadov/cbar-currency)
[![StyleCI](https://github.styleci.io/repos/184592322/shield?branch=master)](https://github.styleci.io/repos/184592322)


<p align="center">
<img src="https://raw.githubusercontent.com/orkhanahmadov/cbar-currency/master/screenshot.png" />
</p>

## Requirements

**PHP 7.1** or higher, ``simplexml`` and ``bcmath`` extensions.

## Installation

```bash
composer require orkhanahmadov/cbar-currency
```

## Usage

### Fetching rates from CBAR

Instantiate ``Orkhanahmadov\CBARCurrency\CBAR`` with date you want to fetch rates for. If you don't pass a date, current date will be used:

```php
use Orkhanahmadov\CBARCurrency\CBAR;

$cbar = new CBAR(); // this will fetch rates for current date
$cbar = new CBAR('01.05.2019'); // this will fetch rates for 01.05.2019
```

You can get currency rate by accessing it with with uppercase currency code:

```php
$cbar->EUR; // returns EUR rate
```

You can change date for a new date by calling ``for()`` method without instantiating new class:

```php
$cbar->for('25.04.2019'); // this will fetch rates for 25.04.2019
$cbar->USD; // returns USD rate for 25.04.2019
```

You can pass dates in any format that acceptable by PHP's ``strtotime()`` function.
For example, ``20.10.2019``, ``10/20/2019``, ``2019-10-20``, ``today``, ``yesterday``, ``-1 week``, ``-1 year``, ``15 December 2015``, ``last Friday``.

You can fetch currency rates for multiple dates with same class instance.
Class instance fetches rates for each unique date only once and stores results for each date.
If you set date to previously fetched date, stored rates will be used.

```php
$cbar = new CBAR();
$cbar->for('20.04.2019'); // this will fetch rates from CBAR API
$cbar->for('23.04.2019'); // this will also fetch rates from CBAR API
$cbar->for('20.04.2019'); // since rates for 20.04.2019 fetched previously stored rates will be used instead of fetching rates for same day again
```

You can chain methods with fluent API syntax:

```php
$cbar = new CBAR();
$cbar->for('yesterday')->EUR;
```

All available currencies and currency codes can be found in [CBAR website](https://www.cbar.az/currency/rates?language=en)

### Converting amount to and from AZN

Library supports converting given amount in foreign currency to AZN with given date's rates:

```php
$cbar = new CBAR();
$cbar->USD(13); // returns AZN equivalent of 13.00 USD with today's rates
$cbar->for('01.05.2019')->USD(57.5); // returns AZN equivalent of 57.50 USD with 01.05.2019 rates
```

You can also convert given amount in AZN to any available foreign currency:

```php
$cbar = new CBAR();
$cbar->AZN()->USD; // returns USD equivalent of 1.00 AZN with today's rates
$cbar->AZN(55)->USD; // returns USD equivalent of 55.00 AZN with today's rates
$cbar->for('01.05.2019')->AZN(17.3)->USD; // returns USD equivalent of 17.30 AZN with 01.05.2019 rates
```

### Helper function

Library ships with global helper function. You can use it like:

```php
cbar()->USD // returns USD rate for today
cbar('01.05.2019')->USD; // returns USD rate for 01.05.2019
cbar()->for('01.05.2019')->EUR; // same as above
cbar()->USD(27); // returns 27.00 USD to AZN conversion
cbar()->AZN(15.8)->EUR; // returns 15.80 AZN to EUR conversion
```

**Note:** Calling ``cbar()`` global function always returns new instance of ``Orkhanahmadov\CBARCurrency\CBAR`` class.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ahmadov90@gmail.com instead of using the issue tracker.

## Credits

- [Orkhan Ahmadov](https://github.com/orkhanahmadov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
