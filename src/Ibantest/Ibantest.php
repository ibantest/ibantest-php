<?php

namespace Ibantest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    /** @var Client */
    protected $client;

    /** @var string API Token */
    protected $apiToken;

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
                'base_uri' => $apiUrl.'/'.$apiVersion.'/',
            ]
        );
    }

    /**
     * set API Token
     *
     * @param string $token Your API Token
     */
    public function setToken($token)
    {
        $this->apiToken = $token;
    }

    /**
     * returns default authorization header
     *
     * @return array
     */
    protected function getAuthHeader()
    {
        return [
            'Authorization' => 'Bearer '.$this->apiToken,
        ];
    }

    /**
     * get count of remaining credits
     *
     * @return array|mixed
     */
    public function remainingCredits()
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                'account/credits',
                $this->getAuthHeader()
            );
            $response = $this->client->send($request);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * validate IBAN
     *
     * @param string $iban IBAN
     * @return array|mixed
     */
    public function validateIban($iban)
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                'validate_iban/'.$iban,
                $this->getAuthHeader()
            );
            $response = $this->client->send($request);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * calculate IBAN
     *
     * @param string $country ISO code of country (e.g. DE, AT, CH)
     * @param string $bankcode bank code
     * @param string $account account number
     * @return array|mixed
     */
    public function calculateIban($country, $bankcode, $account)
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                'calculate_iban/'.$country.'/'.$bankcode.'/'.$account,
                $this->getAuthHeader()
            );
            $response = $this->client->send($request);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * validate bic / swift code
     *
     * @param string $bic BIC / SWIFT Code
     * @return array|mixed
     */
    public function validateBic($bic)
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                'validate_bic/'.$bic,
                $this->getAuthHeader()
            );
            $response = $this->client->send($request);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * find bank
     *
     * @param string $country ISO code of country (e.g. DE, AT, CH)
     * @param string $bankcode bank code
     * @return array|mixed
     */
    public function findBank($country, $bankcode)
    {
        try {
            $request = new Request(
                self::METHOD_GET,
                'find_bank/'.$country.'/'.$bankcode,
                $this->getAuthHeader()
            );
            $response = $this->client->send($request);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * handle Exception
     *
     * @param \Exception $e
     * @return array
     */
    protected function handleException(\Exception $e)
    {
        return [
            'error' => $e->getMessage()
        ];
    }
}
