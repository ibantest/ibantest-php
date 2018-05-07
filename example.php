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

# calculate IBAN out of country code, bank code and account number
$res = $api->calculateIban('AT', '12000', '703447144');
print_r($res);

# validate BIC
$res = $api->validateBic('BFSWDE33BER');
print_r($res);

# find Bank by country code and bank code
$res = $api->findBank('CH', '100');
print_r($res);
