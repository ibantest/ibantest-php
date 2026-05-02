<?php

declare(strict_types=1);

namespace Ibantest;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Throwable;

class Ibantest
{
    public const API_URL = 'https://api.ibantest.com';
    public const API_VERSION = 'v1';
    public const METHOD_GET = 'GET';

    public const ENDPOINT_ACCOUNT = 'account';
    public const ENDPOINT_CREDITS = 'credits';
    public const ENDPOINT_VALIDATE_IBAN = 'validate-iban';
    public const ENDPOINT_CALCULATE_IBAN = 'calculate-iban';
    public const ENDPOINT_VALIDATE_BIC = 'validate-bic';
    public const ENDPOINT_FIND_BANK = 'find-bank';

    protected ClientInterface $client;
    protected string $apiToken = '';

    public function __construct(
        string $apiUrl = self::API_URL,
        string $apiVersion = self::API_VERSION,
        ?ClientInterface $client = null
    ) {
        $this->client = $client ?? new Client([
            'base_uri' => rtrim($apiUrl, '/') . '/' . trim($apiVersion, '/') . '/',
        ]);
    }

    public function setToken(string $token): void
    {
        $this->apiToken = $token;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getRemainingCredits(): array
    {
        return $this->sendRequest($this->buildPath([self::ENDPOINT_ACCOUNT, self::ENDPOINT_CREDITS]));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function validateIban(string $iban): array
    {
        return $this->sendRequest($this->buildPath([self::ENDPOINT_VALIDATE_IBAN, $iban]));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateDeIban(string $bankcode, string $account): array
    {
        return $this->calculateForCountry('DE', [$bankcode, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateAtIban(string $bankcode, string $account): array
    {
        return $this->calculateForCountry('AT', [$bankcode, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateBeIban(string $bankcode, string $account, string $checkDigit): array
    {
        return $this->calculateForCountry('BE', [$bankcode, $account, $checkDigit]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateCzIban(string $bankcode, string $account, ?string $prefix = null): array
    {
        $segments = [$bankcode, $account];
        if ($prefix !== null && $prefix !== '') {
            $segments[] = $prefix;
        }

        return $this->calculateForCountry('CZ', $segments);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateEsIban(string $bankcode, string $branch, string $account): array
    {
        return $this->calculateForCountry('ES', [$bankcode, $branch, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateItIban(string $cin, string $abi, string $cab, string $account): array
    {
        return $this->calculateForCountry('IT', [$cin, $abi, $cab, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateLiIban(string $bankcode, string $account): array
    {
        return $this->calculateForCountry('LI', [$bankcode, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateLuIban(string $bankcode, string $account): array
    {
        return $this->calculateForCountry('LU', [$bankcode, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateMcIban(string $bankcode, string $branch, string $account): array
    {
        return $this->calculateForCountry('MC', [$bankcode, $branch, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateNlIban(string $bankcode, string $account): array
    {
        return $this->calculateForCountry('NL', [$bankcode, $account]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function calculateIban(string $country, string $bankcode, string $account, string $checkDigit = ''): array
    {
        $segments = [$bankcode, $account];
        if ($checkDigit !== '') {
            $segments[] = $checkDigit;
        }

        return $this->calculateForCountry($country, $segments);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function validateBic(string $bic): array
    {
        return $this->sendRequest($this->buildPath([self::ENDPOINT_VALIDATE_BIC, $bic]));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function findBank(string $country, string $bankcode): array
    {
        return $this->sendRequest($this->buildPath([self::ENDPOINT_FIND_BANK, $country, $bankcode]));
    }

    /**
     * @return array<string, string>
     */
    protected function getAuthHeader(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function sendRequest(string $path): array
    {
        try {
            $response = $this->client->request(self::METHOD_GET, $path, [
                'headers' => $this->getAuthHeader(),
            ]);

            return $this->jsonResponse((string) $response->getBody());
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @param array<int, string> $segments
     * @return array<int|string, mixed>
     */
    protected function calculateForCountry(string $country, array $segments): array
    {
        return $this->sendRequest(
            $this->buildPath(array_merge([self::ENDPOINT_CALCULATE_IBAN, strtoupper($country)], $segments))
        );
    }

    /**
     * @param array<int, string> $segments
     */
    protected function buildPath(array $segments): string
    {
        return implode('/', array_map(
            static fn (string $segment): string => rawurlencode($segment),
            $segments
        ));
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function jsonResponse(string $data): array
    {
        $decoded = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [
                'message' => 'Invalid JSON response from API',
                'errorCode' => 9998,
            ];
        }

        return $decoded;
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function handleException(Throwable $e): array
    {
        $responseBody = null;

        if ($e instanceof ClientException && $e->hasResponse()) {
            $responseBody = (string) $e->getResponse()->getBody();
        } elseif ($e instanceof RequestException && $e->hasResponse()) {
            $responseBody = (string) $e->getResponse()->getBody();
        }

        if ($responseBody !== null && $responseBody !== '') {
            $decoded = json_decode($responseBody, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [
            'message' => $e->getMessage(),
            'errorCode' => ($e->getCode() > 0 ? (int) $e->getCode() : 9999),
        ];
    }
}
