# IBANTEST API PHP client


This library can be used to access IBANTEST API endpoints.

## Installing

Get last version with [Composer](http://getcomposer.org "Composer").

```bash
$ composer require ibantest/ibantest-php
```

## Register

You need an API token to use this API.

Please register your account at https://www.ibantest.com and receive 100 credits for free.

## Basic Usage

```php
<?php

include "vendor/autoload.php";

use Ibantest\Ibantest;

$api = new Ibantest();
$api->setToken('###your_api_token###');

# get count of remaining credits
$res = $api->getRemainingCredits();
print_r($res);

# validate IBAN
$res = $api->validateIban('DE02600501010002034304');
print_r($res);

# calculate IBAN (recommended: country specific endpoint methods)
$res = $api->calculateAtIban('12000', '703447144');
print_r($res);

# calculate IBAN for BE with check digits
$res = $api->calculateBeIban('510', '0075470', '61');
print_r($res);

# calculate IBAN for ES with bank code, branch and account
$res = $api->calculateEsIban('1465', '0092', '1234567890');
print_r($res);

# calculate IBAN for IT with CIN, ABI, CAB and account
$res = $api->calculateItIban('X', '05428', '11101', '000000123456');
print_r($res);

# calculate IBAN for CZ with optional prefix
$res = $api->calculateCzIban('0800', '123456789', '19');
print_r($res);

# calculate IBAN for LI
$res = $api->calculateLiIban('08810', '123456789');
print_r($res);

# calculate IBAN for LU
$res = $api->calculateLuIban('001', '1234567890123');
print_r($res);

# calculate IBAN for MC with bank code, branch and account
$res = $api->calculateMcIban('11222', '33444', '12345678901');
print_r($res);

# calculate IBAN for NL
$res = $api->calculateNlIban('ABNA', '0123456789');
print_r($res);

# generic calculate IBAN method is still available
$res = $api->calculateIban('DE', '10090000', '0657845795');
print_r($res);

# validate BIC
$res = $api->validateBic('BFSWDE33BER');
print_r($res);

# find Bank by country code and bank code
$res = $api->findBank('CH', '100');
print_r($res);

```

## Endpoint Update Notice

The client now targets the primary API endpoints with hyphens:
- `validate-iban`
- `calculate-iban`
- `validate-bic`
- `find-bank`

Fallback routes with underscores are not exposed as dedicated client methods.

## Breaking Changes

- The client now requests primary API endpoints with hyphens instead of underscore-style fallback routes.
- Public method signatures are now strictly typed (`string` input parameters).
- URL path segments are encoded internally before requests are sent.

## Upgrade Guide

Before:

```php
$res = $api->calculateIban('AT', '12000', '703447144');
```

After (recommended):

```php
$res = $api->calculateAtIban('12000', '703447144');
```

Before:

```php
$res = $api->validateBic('BFSWDE33BER');
$res = $api->findBank('CH', '100');
```

After:

```php
$res = $api->validateBic('BFSWDE33BER');
$res = $api->findBank('CH', '100');
```

## Documentation
Please have a look at the full documentation
https://api.ibantest.com/

## License
ibantest-php is licensed under the MIT License - see the LICENSE file for details