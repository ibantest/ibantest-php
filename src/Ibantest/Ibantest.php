<?php

namespace Ibantest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

/**
 * Class Ibantest
 *
 * @package IBANTEST
 */
class Ibantest
{
    /** @var string API base URL */
    const API_URL = "https://api.ibantest.com";

    /** @var string API Version */
    const API_VERSION = 'v1';

    /** @var string GET Method */
    const METHOD_GET = 'GET';

    /** @var string endpoint for credits */
    const ENDPOINT_CREDITS = 'account/credits';

    /** @var string endpoint for IBAN validation */
    const ENDPOINT_VALIDATE_IBAN = 'validate_iban';

    /** @var string endpoint for IBAN calculation */
    const ENDPOINT_CALCULATE_IBAN = 'calculate_iban';

    /** @var string endpoint for BIC validation */
    const ENDPOINT_VALIDATE_BIC = 'validate_bic';

    /** @var string endpoint for finding bank */
    const ENDPOINT_FIND_BANK = 'find_bank';

    /** @var Client */
    protected $client;

    /** @var string API Token */
    protected string $apiToken = '';

    /**
     * Ibantest constructor.
     *
     * @param string $apiVersion
     * @param string $apiVersion
     */
    public function __construct($apiUrl = self::API_URL, $apiVersion = self::API_VERSION)
    {
        $this->client = new Client(
            [
                'base_uri' => $apiUrl . '/' . $apiVersion . '/',
            ]
        );
    }

    /**
     * set API Token
     *
     * @param string $token Your API Token
     */
    public function setToken($token): void
    {
        $this->apiToken = $token;
    }

    /**
     * returns default authorization header
     *
     * @return array
     */
    protected function getAuthHeader(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ];
    }

    /**
     * Sends a request to the server.
     *
     * @param string $url The URL to send the request to.
     * @param array $data The data to send with the request.
     * @param string $method The HTTP method to use for the request (e.g., GET, POST).
     * @return mixed The response from the server.
     */
    protected function sendRequest(string $url): array
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                $url,
                $this->getAuthHeader()
            );

            $response = $this->client->send($request);

            return $this->jsonResponse($response->getBody());
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * get count of remaining credits
     *
     * @return array|mixed
     */
    public function getRemainingCredits(): array
    {
        return $this->sendRequest(self::ENDPOINT_CREDITS);
    }

    /**
     * validate IBAN
     *
     * @param string $iban IBAN
     * @return array|mixed
     */
    public function validateIban($iban): array
    {
        return $this->sendRequest(self::ENDPOINT_VALIDATE_IBAN .'/' . $iban);
    }

    /**
     * calculate IBAN
     *
     * @param string $country ISO code of country (e.g. AT, BE, DE)
     * @param string $bankcode bank code
     * @param string $account account number
     * @param string $checkDigit check digit
     * @return array|mixed
     */
    public function calculateIban($country, $bankcode, $account, $checkDigit = ''): array
    {
        $data = [self::ENDPOINT_CALCULATE_IBAN, $country, $bankcode, $account];
        if(!empty($checkDigit)) {
            $data[] = $checkDigit;
        }

        return $this->sendRequest(implode('/', $data));
    }

    /**
     * validate bic / swift code
     *
     * @param string $bic BIC / SWIFT Code
     * @return array|mixed
     */
    public function validateBic($bic): array
    {
        return $this->sendRequest(implode('/', [self::ENDPOINT_VALIDATE_BIC, $bic]));
    }

    /**
     * find bank
     *
     * @param string $country ISO code of country (e.g. DE, AT, CH)
     * @param string $bankcode bank code
     * @return array|mixed
     */
    public function findBank($country, $bankcode): array
    {
        return $this->sendRequest(implode('/', [self::ENDPOINT_FIND_BANK, $country, $bankcode]));
    }

    /**
     * convert JSON response to array
     * 
     * @param string $data
     * @return array
     */
    protected function jsonResponse(string $data): array
    {
        return json_decode($data, true);
    }

    /**
     * handle Exception
     *
     * @param \Exception $e
     * @return array
     */
    protected function handleException(\Exception $e): array
    {
        if ($e instanceof ClientException && !empty($message)) {
            return json_decode($message, true);
        }

        return [
            'message' => $e->getMessage(),
            'errorCode' => 9999,
        ];
    }
}
