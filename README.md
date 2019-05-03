## :dollar: PHP library to get currency rates from [CBAR](https://www.cbar.az)

[![Build Status](https://travis-ci.org/orkhanahmadov/cbar-currency.svg?branch=master)](https://travis-ci.org/orkhanahmadov/cbar-currency)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d5cf2c42b3f6febb6a29/test_coverage)](https://codeclimate.com/github/orkhanahmadov/cbar-currency/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/d5cf2c42b3f6febb6a29/maintainability)](https://codeclimate.com/github/orkhanahmadov/cbar-currency/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/orkhanahmadov/cbar-currency/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/orkhanahmadov/cbar-currency/?branch=master)
[![StyleCI](https://github.styleci.io/repos/184592322/shield?branch=master)](https://github.styleci.io/repos/184592322)

[![Latest Stable Version](https://poser.pugx.org/orkhanahmadov/cbar-currency/version)](https://packagist.org/packages/orkhanahmadov/cbar-currency)
[![Total Downloads](https://poser.pugx.org/orkhanahmadov/cbar-currency/downloads)](https://packagist.org/packages/orkhanahmadov/cbar-currency)
[![License](https://poser.pugx.org/orkhanahmadov/cbar-currency/license)](https://packagist.org/packages/orkhanahmadov/cbar-currency)

### Requirements

**PHP 7.2** or higher, ``simplexml`` and ``bcmath`` extensions.

### Installation

```bash
composer require orkhanahmadov/cbar-currency
```

### Usage

#### Fetching rates from CBAR

Instantiate ``Orkhanahmadov\CBARCurrency\CBAR`` with date you want to fetch rates for. If you won't pass a date, current date will be used:

```php
use Orkhanahmadov\CBARCurrency\CBAR;

$cbar = new CBAR(); // this will fetch rates for current date
$cbar = new CBAR('01.05.2019'); // this will fetch rates for 01.05.2019
```

You can access currency rate as class property with uppercase currency codes:
```php
$cbar->EUR; // returns EUR rate
```

You can change date for a new date by calling ``for()`` method without re-instantiating class:

```php
$cbar->for('25.04.2019'); // this will fetch rates for 25.04.2019
$cbar->USD; // returns USD rate for 25.04.2019
```

You can pass dates in any format that acceptable by PHP's ``strtotime()`` function.
For example, ``20.10.2019``, ``10/20/2019``, ``2019-10-20``, ``today``, ``yesterday``, ``-1 week``, ``-1 year``.

You can fetch rates multiple for dates with same class instance. Rates for each unique date will be called once, stored rates will be used for next same date calls:

```php
$cbar = new CBAR();
$cbar->for('20.04.2019'); // this will fetch rates from CBAR
$cbar->for('23.04.2019'); // this will also fetch rates from CBAR
$cbar->for('20.04.2019'); // since rates for 20.04.2019 fetched previously, this won't fetch anything from CBAR, will use stored rates
```

You can chain methods with fluent API syntax:

```php
$cbar = new CBAR();
$cbar->for('yesterday')->EUR;
```

#### Converting amount to/from AZN

Library supports converting given amount in foreign currency to AZN with given date's rates:

```php
$cbar = new CBAR();
$cbar->USD(57.5); // this will return AZN equivalent of 57.5 USD with today's rates. ({USD rate for today} * 57.5)
$cbar->for('01.05.2019')->USD(57.5); // this will return AZN equivalent of 57.5 USD with 01.05.2019 rates. ({USD rate for 01.05.2019} * 57.5)
```

You can also convert given amount in AZN to foreign currency:

```php
$cbar = new CBAR();
$cbar->AZN()->USD; // this will return USD equivalent of 1 AZN with today's rates
$cbar->AZN(55)->USD; // this will return USD equivalent of 55 AZN with today's rates
$cbar->for('01.05.2019')->AZN(17.3)->USD; // this will return USD equivalent of 17.3 AZN with 01.05.2019 rates
```

#### Helper function

Library ships with global helper function you can use:

```php
cbar()->USD // returns USD rate for today
cbar('01.05.2019')->USD; // returns USD rate for 01.05.2019
cbar()->for('01.05.2019')->EUR; // same as above
cbar()->USD(27.3); // returns 27.3 USD to AZN conversion
cbar()->AZN(15.8)->EUR; // returns 15.8 USD to EUR conversion
```

**Important!** ``cbar()`` global function always returns new instance of ``Orkhanahmadov\CBARCurrency\CBAR`` class.
Passing same date to multiple ``cbar()`` functions will always fetch rates from CBAR, unlike using same instance of the class.

### Testing
You can run the tests with:

```bash
vendor/bin/phpunit
```

### Changelog
Please see [CHANGELOG](https://github.com/orkhanahmadov/cbar-currency/blob/master/CHANGELOG.md) for more information what has changed recently.

### Contributing
Please see [CONTRIBUTING](https://github.com/orkhanahmadov/cbar-currency/blob/master/CONTRIBUTING.md) for details.

### License
The MIT License (MIT). Please see [License file](https://github.com/orkhanahmadov/cbar-currency/blob/master/LICENSE.md) for more information.
