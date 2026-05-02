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
